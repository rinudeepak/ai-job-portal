<?php

namespace App\Controllers;

use App\Models\CareerTransitionModel;
use App\Models\DailyTaskModel;
use App\Models\CourseModuleModel;
use App\Models\CourseLessonModel;
use App\Libraries\CareerTransitionAI;

class CareerTransition extends BaseController
{
    public function index()
    {
        $candidateId = session()->get('user_id');
        $transitionModel = new CareerTransitionModel();
        $taskModel = new DailyTaskModel();
        $userModel = new \App\Models\UserModel();
        $skillsModel = new \App\Models\CandidateSkillsModel();

        // Check if reset parameter is present
        if ($this->request->getGet('reset') === '1') {
            // Instead of deleting, just mark as inactive
            $transitionModel->where('candidate_id', $candidateId)
                           ->where('status', 'active')
                           ->set(['status' => 'inactive', 'deactivated_at' => date('Y-m-d H:i:s')])
                           ->update();
            
            session()->remove('career_suggestions');
            
            $targetRole = $this->request->getGet('target');
            if ($targetRole) {
                return redirect()->to('career-transition')->with('target_role', urldecode($targetRole));
            }
            return redirect()->to('career-transition');
        }

        $activeTransition = $transitionModel->getActiveTransition($candidateId);
        $tasks = $activeTransition ? $taskModel->getTasksByTransition($activeTransition['id']) : [];
        
        // Get candidate's current role
        $user = $userModel->find($candidateId);
        $workExpModel = new \App\Models\WorkExperienceModel();
        $latestWork = $workExpModel->where('user_id', $candidateId)->where('is_current', 1)->first();
        
        if (!$latestWork) {
            $latestWork = $workExpModel->where('user_id', $candidateId)->orderBy('start_date', 'DESC')->first();
        }
        
        $currentRole = $latestWork['job_title'] ?? ($user['work_experience'] ?? '');
        $skills = $skillsModel->where('candidate_id', $candidateId)->first();
        if (!$currentRole && $skills) {
            $currentRole = $skills['skill_name'];
        }
        
        $targetRole = session()->getFlashdata('target_role') ?? $this->request->getGet('target');

        return view('candidate/career_transition', [
            'transition' => $activeTransition,
            'tasks' => $tasks,
            'currentRole' => $currentRole,
            'targetRole' => $targetRole ? urldecode($targetRole) : ''
        ]);
    }

    public function create()
    {
        $currentRole = $this->request->getPost('current_role');
        $targetRole = $this->request->getPost('target_role');
        $candidateId = session()->get('user_id');
        
        // Remove matching career suggestion from session
        $suggestions = session()->get('career_suggestions') ?? [];
        $suggestions = array_filter($suggestions, function($s) use ($targetRole) {
            return strcasecmp($s['job_title'] ?? '', $targetRole) !== 0;
        });
        session()->set('career_suggestions', array_values($suggestions));

        $transitionModel = new CareerTransitionModel();
        
        // Check if this exact transition path already exists in history
        $existingTransition = $transitionModel
            ->where('candidate_id', $candidateId)
            ->where('LOWER(current_role)', strtolower($currentRole))
            ->where('LOWER(target_role)', strtolower($targetRole))
            ->orderBy('created_at', 'DESC')
            ->first();
        
        if ($existingTransition) {
            // Reuse existing transition - just mark as active and reset progress
            
            // First, deactivate current active transition
            $transitionModel->where('candidate_id', $candidateId)
                           ->where('status', 'active')
                           ->set(['status' => 'inactive', 'deactivated_at' => date('Y-m-d H:i:s')])
                           ->update();
            
            // Activate the historical transition
            $transitionModel->update($existingTransition['id'], [
                'status' => 'active',
                'reactivated_at' => date('Y-m-d H:i:s'),
                'reactivation_count' => ($existingTransition['reactivation_count'] ?? 0) + 1
            ]);
            
            // Reset all task completion status for fresh start
            $taskModel = new DailyTaskModel();
            $taskModel->where('transition_id', $existingTransition['id'])
                     ->set(['is_completed' => 0, 'completed_at' => null])
                     ->update();
            
            return redirect()->to('career-transition')
                ->with('success', 'Welcome back! Your previous learning path has been restored. All progress has been reset for a fresh start.');
        }
        
        // No existing transition found - create new one with AI
        
        // First, deactivate any current active transition
        $transitionModel->where('candidate_id', $candidateId)
                       ->where('status', 'active')
                       ->set(['status' => 'inactive', 'deactivated_at' => date('Y-m-d H:i:s')])
                       ->update();

        // Close DB connection before slow AI calls
        $db = \Config\Database::connect();
        $db->close();
        
        $ai = new CareerTransitionAI();
        $analysis = $ai->analyzeTransition($currentRole, $targetRole);
        $courseData = $ai->generateCourseContent($currentRole, $targetRole, $analysis['skill_gaps'] ?? []);
        
        // Reconnect DB after AI calls
        $db->reconnect();

        $transitionId = $transitionModel->insert([
            'candidate_id' => $candidateId,
            'current_role' => $currentRole,
            'target_role' => $targetRole,
            'skill_gaps' => json_encode($analysis['skill_gaps'] ?? []),
            'learning_roadmap' => json_encode($analysis['roadmap'] ?? []),
            'status' => 'active',
            'reactivation_count' => 0
        ]);
        
        $moduleModel = new CourseModuleModel();
        $lessonModel = new CourseLessonModel();
        $taskModel = new DailyTaskModel();
        
        if (isset($courseData['modules']) && count($courseData['modules']) > 0) {
            foreach ($courseData['modules'] as $module) {
                $moduleId = $moduleModel->insert([
                    'transition_id' => $transitionId,
                    'module_number' => $module['number'],
                    'title' => $module['title'],
                    'description' => $module['description'],
                    'duration_weeks' => $module['weeks']
                ]);

                if (isset($module['lessons'])) {
                    foreach ($module['lessons'] as $lesson) {
                        $lessonModel->insert([
                            'module_id' => $moduleId,
                            'lesson_number' => $lesson['number'],
                            'title' => $lesson['title'],
                            'content' => $lesson['content'],
                            'resources' => is_array($lesson['resources']) ? json_encode($lesson['resources']) : $lesson['resources'],
                            'exercises' => is_array($lesson['exercises']) ? json_encode($lesson['exercises']) : $lesson['exercises']
                        ]);
                    }
                }
            }
        }

        // Use daily tasks from course data if available
        if (isset($courseData['daily_tasks'])) {
            foreach ($courseData['daily_tasks'] as $task) {
                $taskModel->insert([
                    'transition_id' => $transitionId,
                    'task_title' => $task['title'] ?? 'Task',
                    'task_description' => $task['description'] ?? '',
                    'duration_minutes' => $task['duration'] ?? 10,
                    'day_number' => $task['day'] ?? 1,
                    'module_number' => $task['module'] ?? null,
                    'lesson_number' => $task['lesson'] ?? null
                ]);
            }
        } elseif (isset($analysis['daily_tasks'])) {
            foreach ($analysis['daily_tasks'] as $index => $task) {
                $taskModel->insert([
                    'transition_id' => $transitionId,
                    'task_title' => $task['title'] ?? 'Task',
                    'task_description' => $task['description'] ?? '',
                    'duration_minutes' => $task['duration'] ?? 10,
                    'day_number' => $task['day'] ?? ($index + 1),
                    'module_number' => $task['module'] ?? null,
                    'lesson_number' => $task['lesson'] ?? null
                ]);
            }
        }

        return redirect()->to('career-transition')
            ->with('success', 'Career transition plan created! AI-powered course content is ready.');
    }

    public function course()
    {
        $candidateId = session()->get('user_id');
        $transitionModel = new CareerTransitionModel();
        $moduleModel = new CourseModuleModel();

        $activeTransition = $transitionModel->getActiveTransition($candidateId);
        $modules = $activeTransition ? $moduleModel->getModulesByTransition($activeTransition['id']) : [];

        return view('candidate/course_modules', [
            'transition' => $activeTransition,
            'modules' => $modules
        ]);
    }

    public function module($moduleId)
    {
        $candidateId = session()->get('user_id');
        $transitionModel = new CareerTransitionModel();
        $moduleModel = new CourseModuleModel();
        $lessonModel = new CourseLessonModel();

        $activeTransition = $transitionModel->getActiveTransition($candidateId);
        $module = $moduleModel->find($moduleId);
        
        if (!$module || $module['transition_id'] != $activeTransition['id']) {
            return redirect()->to('career-transition/course');
        }

        $lessons = $lessonModel->getLessonsByModule($moduleId);

        return view('candidate/course_content', [
            'transition' => $activeTransition,
            'module' => $module,
            'lessons' => $lessons
        ]);
    }

    public function completeTask($taskId)
    {
        $taskModel = new DailyTaskModel();
        $taskModel->markComplete($taskId);
        return $this->response->setJSON(['success' => true]);
    }

    public function dismissSuggestion()
    {
        session()->remove('career_suggestions');
        return $this->response->setJSON(['success' => true]);
    }

    public function reset()
    {
        $candidateId = session()->get('user_id');
        $transitionModel = new CareerTransitionModel();
        
        // Mark as inactive instead of deleting
        $transitionModel->where('candidate_id', $candidateId)
                       ->where('status', 'active')
                       ->set(['status' => 'inactive', 'deactivated_at' => date('Y-m-d H:i:s')])
                       ->update();
        
        return redirect()->to('career-transition')
            ->with('success', 'Career path saved to history. You can now start a new journey!');
    }
    
    /**
     * View all historical career transitions
     */
    public function history()
    {
        $candidateId = session()->get('user_id');
        $transitionModel = new CareerTransitionModel();
        
        // Get all transitions for this candidate
        $transitions = $transitionModel
            ->where('candidate_id', $candidateId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
        
        return view('candidate/career_history', [
            'transitions' => $transitions
        ]);
    }
    
    /**
     * Reactivate a historical transition
     */
    public function reactivate($transitionId)
    {
        $candidateId = session()->get('user_id');
        $transitionModel = new CareerTransitionModel();
        
        // Verify this transition belongs to the current user
        $transition = $transitionModel->find($transitionId);
        
        if (!$transition || $transition['candidate_id'] != $candidateId) {
            return redirect()->to('career-transition')
                ->with('error', 'Invalid transition selected.');
        }
        
        // Deactivate current active transition
        $transitionModel->where('candidate_id', $candidateId)
                       ->where('status', 'active')
                       ->set(['status' => 'inactive', 'deactivated_at' => date('Y-m-d H:i:s')])
                       ->update();
        
        // Activate the selected transition
        $transitionModel->update($transitionId, [
            'status' => 'active',
            'reactivated_at' => date('Y-m-d H:i:s'),
            'reactivation_count' => ($transition['reactivation_count'] ?? 0) + 1
        ]);
        
        // Reset all task completion status
        $taskModel = new DailyTaskModel();
        $taskModel->where('transition_id', $transitionId)
                 ->set(['is_completed' => 0, 'completed_at' => null])
                 ->update();
        
        return redirect()->to('career-transition')
            ->with('success', 'Career path reactivated! Progress has been reset for a fresh start.');
    }
}