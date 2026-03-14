<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CompanyModel;
use App\Models\JobModel;
use App\Models\RecruiterCandidateActionModel;

class CandidateDashboardController extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'candidate') {
            return redirect()->to(base_url('recruiter/dashboard'))->with('error', 'Access denied.');
        }
        
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

        // Top suggested jobs for dashboard (best matches only)
        $topSuggestedJobs = $this->getTopSuggestedJobs($candidateId, 3);
        
        return view('candidate/dashboard', [
            'applications' => $applications,
            'stats' => $stats,
            'pendingActions' => $pendingActions,
            'notifications' => $notifications,
            'topSuggestedJobs' => $topSuggestedJobs,
        ]);
    }
    
    /**
     * Get applications with all details including scores
     */
    private function getApplicationsWithDetails($candidateId)
    {
        $applicationModel = model('ApplicationModel');
        $db = \Config\Database::connect();
        $hasPolicyColumn = $db->fieldExists('ai_interview_policy', 'jobs');
        $hasResumeVersions = $db->tableExists('candidate_resume_versions') && $db->fieldExists('resume_version_id', 'applications');
        $policySelect = $hasPolicyColumn
            ? 'jobs.ai_interview_policy'
            : "'REQUIRED_HARD' as ai_interview_policy";
        $resumeSelect = $hasResumeVersions
            ? 'candidate_resume_versions.title as resume_version_title,
                candidate_resume_versions.target_role as resume_version_target_role,'
            : "'' as resume_version_title, '' as resume_version_target_role,";
        
        $builder = $applicationModel
            ->select('
                applications.*,
                jobs.title as job_title,
                jobs.company,
                ' . $resumeSelect . '
                ' . $policySelect . ',
                0 as technical_score,
                0 as communication_score,
                0 as overall_rating,
                NULL as ai_interview_completed
            ')
            ->join('jobs', 'jobs.id = applications.job_id', 'left');

        if ($hasResumeVersions) {
            $builder->join('candidate_resume_versions', 'candidate_resume_versions.id = applications.resume_version_id', 'left');
        }

        $applications = $builder
            ->where('applications.candidate_id', $candidateId)
            ->orderBy('applications.applied_at', 'DESC')
            ->findAll();
        
        // Add next action for each application
        foreach ($applications as &$application) {
            $application['next_action'] = $this->getNextAction($application);
        }

        $applicationIds = array_values(array_filter(array_map(static function ($application) {
            return (int) ($application['id'] ?? 0);
        }, $applications)));

        $activitySummary = (new RecruiterCandidateActionModel())
            ->getSummaryByApplicationIds((int) $candidateId, $applicationIds);

        foreach ($applications as &$application) {
            $appId = (int) ($application['id'] ?? 0);
            $application['recruiter_activity'] = $activitySummary[$appId] ?? [
                'profile_unique_recruiters' => 0,
                'contact_unique_recruiters' => 0,
                'resume_unique_recruiters' => 0,
                'profile_viewed_count' => 0,
                'contact_viewed_count' => 0,
                'resume_downloaded_count' => 0,
                'last_recruiter_activity_at' => null,
            ];
        }
        unset($application);
        
        return $applications;
    }
    
    /**
     * Calculate dashboard statistics
     */
    private function calculateStats($candidateId)
    {
        $applicationModel = model('ApplicationModel');
        $bookingModel = model('InterviewBookingModel');
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
        
        // Scheduled interviews are the real slot bookings in the current lifecycle.
        $interviewsScheduled = $bookingModel
            ->where('user_id', $candidateId)
            ->whereIn('booking_status', ['booked', 'rescheduled'])
            ->countAllResults();
        
        // Average AI score
        $averageAIScore = 0;
        
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
        
        // Python interview flow starts from applied state as needed.
        $aiInterviewsPending = $applicationModel
            ->select('applications.*, jobs.title as job_title, jobs.ai_interview_policy')
            ->join('jobs', 'jobs.id = applications.job_id', 'left')
            ->where('applications.candidate_id', $candidateId)
            ->where('applications.status', 'applied')
            ->where('jobs.ai_interview_policy !=', JobModel::AI_POLICY_OFF)
            ->findAll();
        
        foreach ($aiInterviewsPending as $app) {
            $actions[] = [
                'title' => 'AI Interview Pending',
                'description' => 'Start your AI interview for ' . $app['job_title'],
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
            ->whereIn('interview_bookings.booking_status', ['booked', 'rescheduled'])
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
        $user = $userModel->findCandidateWithProfile((int) $candidateId) ?? $userModel->find($candidateId);
        
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
     * Get top job suggestions for dashboard (limited list).
     */
    private function getTopSuggestedJobs(int $candidateId, int $limit = 3): array
    {
        $jobModel = new JobModel();
        $suggestedJobs = $jobModel->getSuggestedJobsBasic($candidateId, $limit);

        if (empty($suggestedJobs)) {
            return [];
        }

        $companyIds = [];
        foreach ($suggestedJobs as $job) {
            $companyId = (int) ($job['company_id'] ?? 0);
            if ($companyId > 0) {
                $companyIds[] = $companyId;
            }
        }

        $companyLogoMap = [];
        if (!empty($companyIds)) {
            $companyModel = new CompanyModel();
            $companies = $companyModel
                ->select('id, logo')
                ->whereIn('id', array_values(array_unique($companyIds)))
                ->findAll();

            foreach ($companies as $company) {
                $companyLogoMap[(int) $company['id']] = (string) ($company['logo'] ?? '');
            }
        }

        foreach ($suggestedJobs as $idx => $job) {
            $companyId = (int) ($job['company_id'] ?? 0);
            $suggestedJobs[$idx]['company_logo'] = $companyLogoMap[$companyId] ?? '';
        }

        return $suggestedJobs;
    }
    
    /**
     * Determine next action for an application
     */
    private function getNextAction($application)
    {
        switch ($application['status']) {
            case 'applied':
                $policy = strtoupper((string) ($application['ai_interview_policy'] ?? 'REQUIRED_HARD'));
                return $policy === JobModel::AI_POLICY_OFF
                    ? 'Your application is under recruiter review.'
                    : 'Start your AI interview to move forward.';

            case 'shortlisted':
                return 'Congratulations! You\'ve been shortlisted. Book your HR interview slot.';
                
            case 'interview_slot_booked':
                return 'Your interview slot is booked. Check your bookings for the schedule.';
                
            case 'selected':
                return 'Congratulations! You\'ve been selected. Check your email for next steps.';

            case 'hired':
                return 'Congratulations! Your hiring process is complete.';
                
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
        if (session()->get('role') !== 'candidate') {
            return redirect()->to(base_url('recruiter/dashboard'))->with('error', 'Access denied.');
        }
        
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
