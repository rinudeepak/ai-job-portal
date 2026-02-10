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

        $activeTransition = $transitionModel->getActiveTransition($candidateId);
        $tasks = $activeTransition ? $taskModel->getTasksByTransition($activeTransition['id']) : [];
        
        // Get candidate's current role from latest work experience
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

        return view('candidate/career_transition', [
            'transition' => $activeTransition,
            'tasks' => $tasks,
            'currentRole' => $currentRole
        ]);
    }

    public function create()
    {
        $currentRole = $this->request->getPost('current_role');
        $targetRole = $this->request->getPost('target_role');
        $candidateId = session()->get('user_id');

        $ai = new CareerTransitionAI();
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

        $taskModel = new DailyTaskModel();
        if (isset($analysis['daily_tasks'])) {
            foreach ($analysis['daily_tasks'] as $index => $task) {
                $taskModel->insert([
                    'transition_id' => $transitionId,
                    'task_title' => $task['title'] ?? 'Task',
                    'task_description' => $task['description'] ?? '',
                    'duration_minutes' => $task['duration'] ?? 10,
                    'day_number' => $task['day'] ?? ($index + 1)
                ]);
            }
        }

        // Generate offline course content
        $courseData = $ai->generateCourseContent($currentRole, $targetRole, $analysis['skill_gaps'] ?? []);
        $moduleModel = new CourseModuleModel();
        $lessonModel = new CourseLessonModel();

        if (isset($courseData['modules'])) {
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

        return redirect()->to('career-transition')->with('success', 'Career transition plan created!');
    }

    public function course()
    {
        $candidateId = session()->get('user_id');
        $transitionModel = new CareerTransitionModel();
        $moduleModel = new CourseModuleModel();
        $lessonModel = new CourseLessonModel();

        $activeTransition = $transitionModel->getActiveTransition($candidateId);
        $modules = $activeTransition ? $moduleModel->getModulesByTransition($activeTransition['id']) : [];

        foreach ($modules as &$module) {
            $module['lessons'] = $lessonModel->getLessonsByModule($module['id']);
        }

        return view('candidate/course_content', [
            'transition' => $activeTransition,
            'modules' => $modules
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
        session()->remove('career_suggestion');
        return $this->response->setJSON(['success' => true]);
    }

    public function reset()
    {
        $candidateId = session()->get('user_id');
        $transitionModel = new CareerTransitionModel();
        
        // Delete active transition (CASCADE will delete tasks and modules)
        $transitionModel->where('candidate_id', $candidateId)
                       ->where('status', 'active')
                       ->delete();
        
        return redirect()->to('career-transition')->with('success', 'Career path reset. You can now start a new journey!');
    }
}
