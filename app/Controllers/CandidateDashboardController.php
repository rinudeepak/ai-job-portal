<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class CandidateDashboardController extends BaseController
{
    public function index()
    {
        $candidateId = session()->get('user_id');
        
        if (!$candidateId) {
            return redirect()->to('/login')->with('error', 'Please login to continue');
        }
        
        // Get all applications for this candidate
        $applications = $this->getApplicationsWithDetails($candidateId);
        
        // Get statistics
        $stats = $this->calculateStats($candidateId);
        
        // Get pending actions
        $pendingActions = $this->getPendingActions($candidateId);
        
        // Get notifications
        $notifications = $this->getRecentNotifications($candidateId);
        
        return view('candidate/dashboard', [
            'applications' => $applications,
            'stats' => $stats,
            'pendingActions' => $pendingActions,
            'notifications' => $notifications
        ]);
    }
    
    /**
     * Get applications with all details including scores
     */
    private function getApplicationsWithDetails($candidateId)
    {
        $applicationModel = model('ApplicationModel');
        
        $applications = $applicationModel
            ->select('
                applications.*,
                jobs.title as job_title,
                jobs.company,
                interview_sessions.technical_score,
                interview_sessions.communication_score,
                interview_sessions.overall_rating,
                interview_sessions.completed_at as ai_interview_completed
            ')
            ->join('jobs', 'jobs.id = applications.job_id', 'left')
            ->join('interview_sessions', 'interview_sessions.application_id = applications.id', 'left')
            ->where('applications.candidate_id', $candidateId)
            ->orderBy('applications.applied_at', 'DESC')
            ->findAll();
        
        // Add next action for each application
        foreach ($applications as &$application) {
            $application['next_action'] = $this->getNextAction($application);
        }
        
        return $applications;
    }
    
    /**
     * Calculate dashboard statistics
     */
    private function calculateStats($candidateId)
    {
        $applicationModel = model('ApplicationModel');
        $notificationModel = model('NotificationModel');
        
        // Total applications
        $totalApplications = $applicationModel
            ->where('candidate_id', $candidateId)
            ->countAllResults();
        
        // Active applications (not rejected or withdrawn)
        $activeApplications = $applicationModel
            ->where('candidate_id', $candidateId)
            ->whereNotIn('status', ['rejected', 'withdrawn'])
            ->countAllResults();
        
        // Interviews scheduled
        $interviewsScheduled = $applicationModel
            ->where('candidate_id', $candidateId)
            ->groupStart()
                ->where('status', 'ai_interview_scheduled')
                ->orWhere('status', 'hr_interview_scheduled')
            ->groupEnd()
            ->countAllResults();
        
        // Average AI score
        $db = \Config\Database::connect();
        $query = "
            SELECT AVG(interview_sessions.overall_rating) as avg_score
            FROM applications
            INNER JOIN interview_sessions ON interview_sessions.application_id = applications.id
            WHERE applications.candidate_id = ?
            AND interview_sessions.overall_rating IS NOT NULL
        ";
        
        $result = $db->query($query, [$candidateId])->getRow();
        $averageAIScore = $result ? round($result->avg_score, 1) : 0;
        
        // Unread notifications
        $unreadNotifications = $notificationModel
            ->where('user_id', $candidateId)
            ->where('is_read', 0)
            ->countAllResults();
        
        return [
            'total_applications' => $totalApplications,
            'active_applications' => $activeApplications,
            'interviews_scheduled' => $interviewsScheduled,
            'average_ai_score' => $averageAIScore,
            'unread_notifications' => $unreadNotifications
        ];
    }
    
    /**
     * Get pending actions for candidate
     */
    private function getPendingActions($candidateId)
    {
        $actions = [];
        $applicationModel = model('ApplicationModel');
        $bookingModel = model('InterviewBookingModel');
        
        // Check for AI interviews to complete
        $aiInterviewsPending = $applicationModel
            ->select('applications.*, jobs.title as job_title')
            ->join('jobs', 'jobs.id = applications.job_id', 'left')
            ->where('applications.candidate_id', $candidateId)
            ->where('applications.status', 'ai_interview_scheduled')
            ->findAll();
        
        foreach ($aiInterviewsPending as $app) {
            $actions[] = [
                'title' => 'AI Interview Pending',
                'description' => 'Complete your AI interview for ' . $app['job_title'],
                'link' => base_url('interview/start/' . $app['id']),
                'button_text' => 'Start Interview',
                'priority' => 'high'
            ];
        }
        
        // Check for HR interviews to book
        $hrInterviewsToBook = $applicationModel
            ->select('applications.*, jobs.title as job_title')
            ->join('jobs', 'jobs.id = applications.job_id', 'left')
            ->where('applications.candidate_id', $candidateId)
            ->where('applications.status', 'shortlisted')
            ->whereNotIn('applications.id', function($builder) {
                $builder->select('application_id')->from('interview_bookings');
            })
            ->findAll();
        
        foreach ($hrInterviewsToBook as $app) {
            $actions[] = [
                'title' => 'Book HR Interview',
                'description' => 'Schedule your HR interview for ' . $app['job_title'],
                'link' => base_url('candidate/book-slot/' . $app['id']),
                'button_text' => 'Book Now',
                'priority' => 'high'
            ];
        }
        
        // Check for upcoming interviews today
        $interviewsToday = $bookingModel
            ->select('interview_bookings.*, jobs.title as job_title, interview_slots.slot_time')
            ->join('jobs', 'jobs.id = interview_bookings.job_id', 'left')
            ->join('interview_slots', 'interview_slots.id = interview_bookings.slot_id', 'left')
            ->where('interview_bookings.user_id', $candidateId)
            ->where('interview_bookings.slot_datetime', date('Y-m-d'))
            ->where('interview_bookings.booking_status', 'confirmed')
            ->findAll();
        
        foreach ($interviewsToday as $interview) {
            $actions[] = [
                'title' => 'Interview Today',
                'description' => 'You have an interview for ' . $interview['job_title'] . ' at ' . date('h:i A', strtotime($interview['slot_time'])),
                'link' => base_url('candidate/my-bookings'),
                'button_text' => 'View Details',
                'priority' => 'urgent'
            ];
        }
        
        // Check for profile completion
        $userModel = model('UserModel');
        $user = $userModel->find($candidateId);
        
        if (empty($user['resume_path']) || empty($user['phone']) || empty($user['bio']) || empty($user['email'])) {
            $actions[] = [
                'title' => 'Complete Your Profile',
                'description' => 'Add missing information to improve your chances',
                'link' => base_url('candidate/profile'),
                'button_text' => 'Update Profile',
                'priority' => 'medium'
            ];
        }
        
        return $actions;
    }
    
    /**
     * Get recent notifications
     */
    private function getRecentNotifications($candidateId)
    {
        $notificationModel = model('NotificationModel');
        
        return $notificationModel
            ->where('user_id', $candidateId)
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->findAll();
    }
    
    /**
     * Determine next action for an application
     */
    private function getNextAction($application)
    {
        switch ($application['status']) {
            case 'pending':
                return 'Your application is being reviewed by our team.';
                
            case 'ai_interview_scheduled':
                return 'Complete your AI interview to proceed to the next stage.';
                
            case 'ai_interview_completed':
                return 'Your AI interview has been completed. Waiting for review.';
                
            case 'shortlisted':
                return 'Congratulations! You\'ve been shortlisted. Book your HR interview slot.';
                
            case 'hr_interview_scheduled':
                return 'Your HR interview is scheduled. Please check your bookings for details.';
                
            case 'hr_interview_completed':
                return 'HR interview completed. Waiting for final decision.';
                
            case 'selected':
                return 'Congratulations! You\'ve been selected. Check your email for next steps.';
                
            case 'rejected':
                return 'Unfortunately, we are proceeding with other candidates at this time.';
                
            case 'withdrawn':
                return 'You have withdrawn this application.';
                
            default:
                return 'Application in progress.';
        }
    }

    /**
     * View all applications
     */
    public function applications()
    {
        $candidateId = session()->get('user_id');
        
        if (!$candidateId) {
            return redirect()->to('/login')->with('error', 'Please login to continue');
        }
        
        $applications = $this->getApplicationsWithDetails($candidateId);
        
        return view('candidate/applications', [
            'applications' => $applications
        ]);
    }
}