<?php

namespace App\Controllers;

use App\Libraries\AiInterviewer;

class AiInterview extends BaseController
{
    protected $interviewer;
    protected $session;
    
    public function __construct()
    {
        $this->interviewer = new AiInterviewer();
        $this->session = \Config\Services::session();
    }

    /**
     * Start interview page
     */
    
    public function start()
    {
        

        // Get candidate info from session/database
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
            'user' => $user,
            'skills' => $resumeSkills,
            'github_languages' => $githubLanguages
        ]);
    }

    /**
     * Begin the interview
     */
    public function begin()
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
    
    return redirect()->to('/interview/chat');
}

    /**
     * Chat interface
     */
    public function chat()
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
    
    if ($interview['status'] === 'completed') {
        return redirect()->to('/interview/results/' . $interviewId);
    }
    
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
    
    return view('interview/chat', [
        'interview' => $interview,
        'session_data' => $sessionData
    ]);
}

    /**
     * Submit candidate answer
     */
    public function submitAnswer()
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
        
        // Continue interview
        $updatedSession = $this->interviewer->continueInterview($sessionData, $answer);
        
        // Update database
        $interviewModel->update($interviewId, [
            'conversation_history' => json_encode($updatedSession['conversation_history']),
            'turn' => $updatedSession['turn'],
            'status' => $updatedSession['status'],
            'updated_at' => $updatedSession['updated_at']
        ]);
        
        // If complete, redirect to evaluation
        if ($updatedSession['status'] === 'completed') {
            return redirect()->to('/interview/complete/' . $interviewId);
        }
        
        return redirect()->to('/interview/chat');
    }

    /**
     * Complete interview and evaluate
     */
    public function complete($interviewId)
    {
        $interviewModel = model('InterviewSessionModel');
        $interview = $interviewModel->find($interviewId);
        
        if (!$interview) {
            return redirect()->to('/interview/start')->with('error', 'Interview not found');
        }
        
        // Get user skills
        $skillModel = model('CandidateSkillsModel');
        $resumeSkills = $skillModel->where('candidate_id', $interview['user_id'])->findAll();
        
        $conversationHistory = json_decode($interview['conversation_history'], true);
        
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
            'overall_rating' => $evaluation['overall_rating'],
            'ai_decision' => $evaluation['ai_decision'],
            'status' => 'evaluated',
            'completed_at' => date('Y-m-d H:i:s')
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

    /**
     * Helper: Get last AI message
     */
    private function getLastAiMessage(array $conversationHistory): string
    {
        $filtered = array_filter($conversationHistory, fn($msg) => $msg['role'] === 'assistant');
        $last = end($filtered);
        return $last['content'] ?? '';
    }
}