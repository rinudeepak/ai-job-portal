<?php

namespace App\Controllers;

use App\Libraries\AiInterviewer;

class AiInterview extends BaseController
{
    protected $interviewer;
    protected $session;

    public function __construct()
    {
        $this->session = \Config\Services::session();
    }

    /**
     * Start interview page
     */

    public function start($applicationId)
    {

        $applicationModel = model('ApplicationModel');
        $application = $applicationModel->find($applicationId);
        $jobModel = model('JobModel');
        $job = $jobModel->find($application['job_id']);
        $job_title = $job['title'];   // Get candidate info from session/database
        // $this->interviewer = new AiInterviewer($job['min_ai_cutoff_score']);
session()->set('ai_cutoff_score', $job['min_ai_cutoff_score']);


        $userId = session()->get('user_id');

        $userModel = model('UserModel');
        $user = $userModel->find($userId);

        if (!$user) {
            return redirect()->to('/candidate/profile')->with('error', 'Please complete your profile first');
        }

        // Get resume skills and GitHub languages
        $skillModel = model('CandidateSkillsModel');
        $resumeSkills = $skillModel->where('candidate_id', $userId)->findAll();

        $githubLanguages = json_decode($user['github_languages'] ?? '[]', true);

        return view('interview/start', [
            'application' => $application,
            'user' => $user,
            'skills' => $resumeSkills,
            'github_languages' => $githubLanguages,
            'job_title' => $job_title
        ]);
    }

    /**
     * Begin the interview
     */
    public function begin($applicationId)
    {
        $userId = session()->get('user_id');
        $position = $this->request->getPost('position');

        if (empty($position)) {
            return redirect()->back()->with('error', 'Please specify the position');
        }

        // Get candidate data
        $userModel = model('UserModel');
        $skillModel = model('CandidateSkillsModel');

        $user = $userModel->find($userId);
        $resumeSkills = $skillModel->where('candidate_id', $userId)->findAll();
        $githubLanguages = json_decode($user['github_languages'] ?? '[]', true);
$this->interviewer = $this->getInterviewer();
        // Start interview
        $sessionData = $this->interviewer->startInterview($resumeSkills, $githubLanguages, $position);



        if (empty($sessionData['conversation_history'])) {
            log_message('error', 'AI failed to generate initial message');
            return redirect()->back()->with('error', 'Failed to start interview. Please try again.');
        }

        // Save to database
        $interviewModel = model('InterviewSessionModel');
        $interviewId = $interviewModel->insert([
            'user_id' => $userId,
            'session_id' => $sessionData['session_id'],
            'position' => $position,
            'conversation_history' => json_encode($sessionData['conversation_history']),
            'turn' => $sessionData['turn'],
            'max_turns' => $sessionData['max_turns'],
            'status' => $sessionData['status'],
            'created_at' => $sessionData['created_at']
        ]);

        // DEBUG: Verify insert
        log_message('debug', 'Inserted interview ID: ' . $interviewId);

        if (!$interviewId) {
            log_message('error', 'Failed to insert interview into database');
            return redirect()->back()->with('error', 'Database error. Please try again.');
        }

        // Store session ID
        session()->set('current_interview_id', $interviewId);

        return redirect()->to('/interview/chat/' . $applicationId);
    }

    /**
     * Chat interface
     */
    public function chat($applicationId)
    {
        $interviewId = session()->get('current_interview_id');

        if (!$interviewId) {
            return redirect()->to('/interview/start')->with('error', 'No active interview found');
        }

        $interviewModel = model('InterviewSessionModel');
        $interview = $interviewModel->find($interviewId);

        if (!$interview) {
            return redirect()->to('/interview/start')->with('error', 'Interview not found');
        }

        // if ($interview['status'] === 'completed') {
        //     return redirect()->to('/interview/results/' . $interviewId);
        // }

        // DEBUG: Check what's in the database
        log_message('debug', 'Interview data: ' . print_r($interview, true));

        $conversationHistory = json_decode($interview['conversation_history'], true);

        // DEBUG: Check if conversation_history is empty
        log_message('debug', 'Conversation history: ' . print_r($conversationHistory, true));

        if (empty($conversationHistory)) {
            log_message('error', 'Conversation history is empty for interview ID: ' . $interviewId);
            return redirect()->to('/interview/start')->with('error', 'Interview data corrupted. Please start again.');
        }

        $sessionData = [
            'session_id' => $interview['session_id'],
            'turn' => $interview['turn'],
            'max_turns' => $interview['max_turns'],
            'conversation_history' => $conversationHistory,
            'status' => $interview['status']
        ];
        // Check if interview is completed - show with "View Results" button
        $isCompleted = ($interview['status'] === 'completed');
        if ($isCompleted) {
            $applicationModel = model('ApplicationModel');
            $applicationModel->update($applicationId, [
                'status' => 'ai_interview_completed'
            ]);
        }

        return view('interview/chat', [
            'interview' => $interview,
            'session_data' => $sessionData,
            'is_completed' => $isCompleted,
            'application' => $applicationId
        ]);
    }

    /**
     * Submit candidate answer
     */
    public function submitAnswer($applicationId)
    {
        $interviewId = session()->get('current_interview_id');
        $answer = $this->request->getPost('answer');

        if (empty(trim($answer))) {
            return redirect()->back()->with('error', 'Please provide an answer');
        }

        $interviewModel = model('InterviewSessionModel');
        $interview = $interviewModel->find($interviewId);

        $sessionData = [
            'session_id' => $interview['session_id'],
            'turn' => $interview['turn'],
            'max_turns' => $interview['max_turns'],
            'conversation_history' => json_decode($interview['conversation_history'], true),
            'status' => $interview['status']
        ];
$this->interviewer = $this->getInterviewer();
        // Continue interview
        $updatedSession = $this->interviewer->continueInterview($sessionData, $answer);

        // Update database
        $interviewModel->update($interviewId, [
            'conversation_history' => json_encode($updatedSession['conversation_history']),
            'turn' => $updatedSession['turn'],
            'status' => $updatedSession['status'],
            'updated_at' => $updatedSession['updated_at']
        ]);

        // Always redirect back to chat page
        // The chat page will show "View Results" button if completed
        return redirect()->to('/interview/chat/' . $applicationId);
    }

    /**
     * Trigger evaluation (called when user clicks "View Results")
     */
    public function triggerEvaluation($applicationId)
    {
        $interviewId = session()->get('current_interview_id');

        if (!$interviewId) {
            return redirect()->to('/interview/start')->with('error', 'No interview found');
        }

        $interviewModel = model('InterviewSessionModel');
        $interview = $interviewModel->find($interviewId);

        if (!$interview) {
            return redirect()->to('/interview/start')->with('error', 'Interview not found');
        }

        // Check if already evaluated
        if ($interview['status'] === 'evaluated') {
            return redirect()->to('/interview/results/' . $interviewId);
        }

        // Get user skills
        $skillModel = model('CandidateSkillsModel');
        $resumeSkills = $skillModel->where('candidate_id', $interview['user_id'])->findAll();

        $conversationHistory = json_decode($interview['conversation_history'], true);
$this->interviewer = $this->getInterviewer();
        // Evaluate interview
        $evaluation = $this->interviewer->evaluateInterview(
            $conversationHistory,
            $resumeSkills,
            $interview['position']
        );

        // Save evaluation
        $interviewModel->update($interviewId, [
            'evaluation_data' => json_encode($evaluation),
            'technical_score' => $evaluation['technical_score'],
            'communication_score' => $evaluation['communication_score'],
            'problem_solving_score' => $evaluation['problem_solving_score'] ?? 0,
            'adaptability_score' => $evaluation['adaptability_score'] ?? 0,
            'enthusiasm_score' => $evaluation['enthusiasm_score'] ?? 0,
            'overall_rating' => $evaluation['overall_rating'],
            'ai_decision' => $evaluation['ai_decision'],
            'status' => 'evaluated',
            'application_id' => $applicationId,
            'completed_at' => date('Y-m-d H:i:s')
        ]);
        $applicationModel = model('ApplicationModel');
        $applicationModel->update($applicationId, [
            'status' => 'ai_evaluated'
        ]);

        return redirect()->to('/interview/results/' . $interviewId);
    }


    /**
     * Show results
     */
    public function results($interviewId)
    {
        $interviewModel = model('InterviewSessionModel');
        $interview = $interviewModel->find($interviewId);

        if (!$interview) {
            return redirect()->to('/candidate/dashboard')->with('error', 'Interview not found');
        }

        $evaluation = json_decode($interview['evaluation_data'] ?? '{}', true);
        $conversationHistory = json_decode($interview['conversation_history'], true);

        return view('interview/results', [
            'interview' => $interview,
            'evaluation' => $evaluation,
            'conversation' => $conversationHistory
        ]);
    }
private function getInterviewer(): AiInterviewer
{
    $cutoff = session()->get('ai_cutoff_score');

    if (!$cutoff) {
        throw new \Exception('AI cutoff score not found in session');
    }

    return new AiInterviewer($cutoff);
}

    
    
}