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
            $transitionModel->where('candidate_id', $candidateId)
                           ->where('status', 'active')
                           ->delete();
            
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

        $ai = new CareerTransitionAI();
        
        // Quick analysis first
        $analysis = $ai->analyzeTransition($currentRole, $targetRole);

        $transitionModel = new CareerTransitionModel();
        $transitionId = $transitionModel->insert([
            'candidate_id' => $candidateId,
            'current_role' => $currentRole,
            'target_role' => $targetRole,
            'skill_gaps' => json_encode($analysis['skill_gaps'] ?? []),
            'learning_roadmap' => json_encode($analysis['roadmap'] ?? []),
            'status' => 'active'
        ]);

        // Generate course content (this takes time)
        $courseData = $ai->generateCourseContent($currentRole, $targetRole, $analysis['skill_gaps'] ?? []);
        
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

        return redirect()->to('career-transition')->with('success', 'Career transition plan created! AI-powered course content is ready.');
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
        
        $transitionModel->where('candidate_id', $candidateId)
                       ->where('status', 'active')
                       ->delete();
        
        return redirect()->to('career-transition')->with('success', 'Career path reset. You can now start a new journey!');
    }
}