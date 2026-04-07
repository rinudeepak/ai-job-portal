<?php

namespace App\Controllers;

use App\Models\MentorModel;
use App\Models\MentoringSessionModel;

class MentoringController extends BaseController
{
    protected $mentorModel;
    protected $sessionModel;

    public function __construct()
    {
        $this->mentorModel = new MentorModel();
        $this->sessionModel = new MentoringSessionModel();
    }

    public function bookSession($mentorId)
    {
        $mentor = $this->mentorModel->find($mentorId);
        
        if (!$mentor) {
            return redirect()->back()->with('error', 'Mentor not found');
        }

        $data = [
            'title' => 'Book Mentoring Session',
            'mentor' => $mentor,
            'goal_id' => $this->request->getGet('goal_id')
        ];

        return view('mentoring/book_session', $data);
    }

    public function processBooking($mentorId)
    {
        $userId = session()->get('user_id');
        $goalId = $this->request->getPost('goal_id');
        $sessionType = $this->request->getPost('session_type');
        $scheduledAt = $this->request->getPost('scheduled_at');
        $duration = $this->request->getPost('duration') ?: 60;

        $mentor = $this->mentorModel->find($mentorId);
        $amount = $mentor['hourly_rate'] * ($duration / 60);

        $sessionData = [
            'user_id' => $userId,
            'mentor_id' => $mentorId,
            'goal_id' => $goalId,
            'session_type' => $sessionType,
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => $duration,
            'amount' => $amount,
            'status' => 'scheduled',
            'payment_status' => 'pending'
        ];

        if ($this->sessionModel->save($sessionData)) {
            return redirect()->to('/mentoring/sessions')->with('success', 'Session booked successfully!');
        }

        return redirect()->back()->with('error', 'Failed to book session');
    }

    public function mySessions()
    {
        $userId = session()->get('user_id');
        $sessions = $this->sessionModel->getUserSessions($userId);

        $data = [
            'title' => 'My Mentoring Sessions',
            'sessions' => $sessions
        ];

        return view('mentoring/my_sessions', $data);
    }
}