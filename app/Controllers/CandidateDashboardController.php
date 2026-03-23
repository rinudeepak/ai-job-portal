<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AiInterviewPrepCoach;
use App\Libraries\AiJobSearchStrategyCoach;
use App\Models\CandidateSkillsModel;
use App\Models\CompanyModel;
use App\Models\JobModel;
use App\Models\RecruiterCandidateActionModel;
use App\Models\UserModel;

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
        $profileStrength = $this->calculateProfileStrength($candidateId);
        
        // Get pending actions
        $pendingActions = $this->getPendingActions($candidateId);
        
        // Get notifications
        $notifications = $this->getRecentNotifications($candidateId);

        // Top suggested jobs for dashboard (best matches only)
        $topSuggestedJobs = $this->getTopSuggestedJobs($candidateId, 3);
        $jobSearchStrategy = $this->buildJobSearchStrategyCoach((int) $candidateId, $applications, $topSuggestedJobs);
        
        return view('candidate/dashboard', [
            'applications' => $applications,
            'stats' => $stats,
            'profileStrength' => $profileStrength,
            'pendingActions' => $pendingActions,
            'notifications' => $notifications,
            'topSuggestedJobs' => $topSuggestedJobs,
            'jobSearchStrategy' => $jobSearchStrategy,
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
                candidate_resume_versions.target_role as resume_version_target_role,
                candidate_resume_versions.summary as resume_version_summary,
                candidate_resume_versions.highlight_skills as resume_version_highlight_skills,
                candidate_resume_versions.content as resume_version_content,
                candidate_resume_versions.updated_at as resume_version_updated_at,'
            : "'' as resume_version_title, '' as resume_version_target_role, '' as resume_version_summary, '' as resume_version_highlight_skills, '' as resume_version_content, NULL as resume_version_updated_at,";
        
        $builder = $applicationModel
            ->select('
                applications.*,
                jobs.title as job_title,
                jobs.company,
                jobs.description as job_description,
                jobs.required_skills,
                jobs.experience_level,
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
            $application['interview_prep'] = $this->buildInterviewPrepCoach($application);
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

    private function buildInterviewPrepCoach(array $application): array
    {
        if (in_array((string) ($application['status'] ?? ''), ['rejected', 'withdrawn', 'selected', 'hired'], true)) {
            return [];
        }

        $requiredSkills = $this->tokenizeCsv((string) ($application['required_skills'] ?? ''));
        $focusSkills = array_slice($requiredSkills, 0, 5);
        $jobTitle = trim((string) ($application['job_title'] ?? 'this role'));
        $targetRole = trim((string) ($application['resume_version_target_role'] ?? ''));
        $resumeTitle = trim((string) ($application['resume_version_title'] ?? ''));
        $policy = strtoupper((string) ($application['ai_interview_policy'] ?? JobModel::AI_POLICY_REQUIRED_HARD));
        $status = (string) ($application['status'] ?? '');

        $coachTitle = 'Pre-interview Preparation Coach';
        if ($status === 'shortlisted' || $status === 'interview_slot_booked') {
            $coachTitle = 'HR Interview Preparation Coach';
        } elseif ($policy !== JobModel::AI_POLICY_OFF) {
            $coachTitle = 'AI Interview Preparation Coach';
        }

        $talkingPoints = [];
        if ($targetRole !== '') {
            $talkingPoints[] = 'Explain why your background fits the target role "' . $targetRole . '".';
        }
        if ($resumeTitle !== '') {
            $talkingPoints[] = 'Use examples from your saved resume version "' . $resumeTitle . '".';
        }
        if (!empty($focusSkills)) {
            $talkingPoints[] = 'Prepare project stories around ' . implode(', ', array_slice($focusSkills, 0, 3)) . '.';
        }
        if (!empty($application['experience_level'])) {
            $talkingPoints[] = 'Be ready to justify your experience level: ' . trim((string) $application['experience_level']) . '.';
        }
        if (empty($talkingPoints)) {
            $talkingPoints[] = 'Prepare two role-relevant examples with measurable outcomes.';
        }

        $checklist = [
            'Review the job description and map your strongest experience to the role.',
            'Prepare concise STAR-format examples for one challenge, one achievement, and one collaboration story.',
            'Keep your resume, project examples, and skill claims consistent.',
        ];

        if (!empty($focusSkills)) {
            $checklist[] = 'Revise the top skills recruiters are likely to test: ' . implode(', ', array_slice($focusSkills, 0, 4)) . '.';
        }

        if ($policy !== JobModel::AI_POLICY_OFF && $status === 'applied') {
            $checklist[] = 'Practice answering clearly on camera with short, structured responses for the AI round.';
        }

        $likelyQuestions = [];
        foreach (array_slice($focusSkills, 0, 3) as $skill) {
            $likelyQuestions[] = 'Describe a real example where you used ' . $skill . '.';
        }
        $likelyQuestions[] = 'Why are you interested in this ' . $jobTitle . ' role?';
        $likelyQuestions[] = 'What problem did you solve recently that best shows your fit for this job?';

        $fallback = [
            'title' => $coachTitle,
            'focus_skills' => $focusSkills,
            'talking_points' => $talkingPoints,
            'checklist' => $checklist,
            'likely_questions' => array_slice($likelyQuestions, 0, 5),
            'source' => 'fallback',
        ];

        return (new AiInterviewPrepCoach())->generate($application, $fallback);
    }

    private function tokenizeCsv(string $value): array
    {
        $parts = preg_split('/[,|\\/]+/', strtolower($value)) ?: [];
        $tokens = [];
        foreach ($parts as $part) {
            $part = trim($part);
            if ($part !== '') {
                $tokens[] = $part;
            }
        }

        return array_values(array_unique($tokens));
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
     * Calculate a simple profile strength score from the core profile fields.
     */
    private function calculateProfileStrength(int $candidateId): int
    {
        $userModel = model('UserModel');
        $skillsModel = new CandidateSkillsModel();
        $user = $userModel->findCandidateWithProfile($candidateId) ?? [];
        $skillsRow = $skillsModel->where('candidate_id', $candidateId)->first() ?? [];

        $profileFields = [
            !empty($user['name']),
            !empty($user['email']),
            !empty($user['phone']),
            !empty($user['profile_photo']),
            !empty($user['resume_path']),
            !empty($user['bio']),
            !empty($user['location']),
            !empty($user['preferred_job_titles']),
            !empty($user['preferred_locations']),
            !empty($user['preferred_employment_type']),
            !empty($skillsRow['skill_name']),
        ];

        $filled = array_sum($profileFields);
        $total = max(1, count($profileFields));

        return (int) round(($filled / $total) * 100);
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

            case 'hold':
                return 'Your application is on hold. The recruiter may revisit it later.';
                
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

    public function jobSearchStrategy()
    {
        if (session()->get('role') !== 'candidate') {
            return redirect()->to(base_url('recruiter/dashboard'))->with('error', 'Access denied.');
        }

        $candidateId = (int) session()->get('user_id');
        if ($candidateId <= 0) {
            return redirect()->to('/login')->with('error', 'Please login to continue');
        }

        $applications = $this->getApplicationsWithDetails($candidateId);
        $topSuggestedJobs = $this->getTopSuggestedJobs($candidateId, 6);
        $jobSearchStrategy = $this->buildJobSearchStrategyCoach($candidateId, $applications, $topSuggestedJobs);

        return view('candidate/job_search_strategy', [
            'applications' => $applications,
            'topSuggestedJobs' => $topSuggestedJobs,
            'jobSearchStrategy' => $jobSearchStrategy,
        ]);
    }

    public function mockInterview(int $applicationId)
    {
        if (session()->get('role') !== 'candidate') {
            return redirect()->to(base_url('recruiter/dashboard'))->with('error', 'Access denied.');
        }

        $candidateId = (int) session()->get('user_id');
        if ($candidateId <= 0) {
            return redirect()->to('/login')->with('error', 'Please login to continue');
        }

        $application = $this->getApplicationDetail($candidateId, $applicationId);
        if (empty($application)) {
            return redirect()->to(base_url('candidate/applications'))->with('error', 'Application not found.');
        }

        if (in_array((string) ($application['status'] ?? ''), ['rejected', 'withdrawn', 'selected', 'hired'], true)) {
            return redirect()->to(base_url('candidate/applications'))->with('error', 'Detailed mock interview is available only for active applications.');
        }

        return view('candidate/mock_interview', [
            'application' => $application,
            'mockInterview' => $this->buildDetailedMockInterview($application),
        ]);
    }

    private function getApplicationDetail(int $candidateId, int $applicationId): ?array
    {
        $applications = $this->getApplicationsWithDetails($candidateId);
        foreach ($applications as $application) {
            if ((int) ($application['id'] ?? 0) === $applicationId) {
                return $application;
            }
        }

        return null;
    }

    private function buildDetailedMockInterview(array $application): array
    {
        $summaryPrep = $application['interview_prep'] ?? $this->buildInterviewPrepCoach($application);
        $policy = strtoupper((string) ($application['ai_interview_policy'] ?? JobModel::AI_POLICY_REQUIRED_HARD));
        $jobTitle = trim((string) ($application['job_title'] ?? 'this role'));
        $experienceLevel = trim((string) ($application['experience_level'] ?? ''));
        $focusSkills = array_values(array_slice((array) ($summaryPrep['focus_skills'] ?? []), 0, 6));

        $rounds = [
            [
                'name' => $policy === JobModel::AI_POLICY_OFF ? 'Recruiter Screen Round' : 'AI Screening Round',
                'objective' => 'Give crisp, structured answers that prove baseline fit for ' . $jobTitle . '.',
                'questions' => array_map(static function (string $skill): array {
                    return [
                        'question' => 'Give a short, clear example of where you used ' . $skill . '.',
                        'why_it_matters' => 'This checks whether your resume claims are backed by real work.',
                        'answer_tip' => 'Answer in 60 to 90 seconds with context, action, and result.',
                    ];
                }, array_slice($focusSkills, 0, 3)),
            ],
            [
                'name' => 'Technical Depth Round',
                'objective' => 'Show how you make technical decisions and solve production-level problems.',
                'questions' => [
                    [
                        'question' => 'Walk through a recent project that best matches this role.',
                        'why_it_matters' => 'Interviewers want evidence of relevant execution, not only tool familiarity.',
                        'answer_tip' => 'Cover the problem, architecture, tradeoffs, and the measurable outcome.',
                    ],
                    [
                        'question' => 'What technical challenge did you face recently, and how did you resolve it?',
                        'why_it_matters' => 'This reveals debugging discipline and ownership.',
                        'answer_tip' => 'Focus on the issue, your reasoning path, the fix, and what changed after.',
                    ],
                    [
                        'question' => 'How do you keep code quality and maintainability strong under deadlines?',
                        'why_it_matters' => 'This tests maturity, not just coding speed.',
                        'answer_tip' => 'Mention review habits, testing, refactoring decisions, and risk management.',
                    ],
                ],
            ],
            [
                'name' => 'Behavioral and Role Fit Round',
                'objective' => 'Connect your working style and career direction to the team and role.',
                'questions' => [
                    [
                        'question' => 'Why are you interested in this ' . $jobTitle . ' role right now?',
                        'why_it_matters' => 'Recruiters want a coherent reason, not a generic answer.',
                        'answer_tip' => 'Tie the role to your proven strengths, growth direction, and the company context.',
                    ],
                    [
                        'question' => 'Tell me about a time you handled conflicting priorities or stakeholder pressure.',
                        'why_it_matters' => 'This tests communication and judgment under real work conditions.',
                        'answer_tip' => 'Use STAR and show how you aligned people, not just tasks.',
                    ],
                ],
            ],
        ];

        if ($policy === JobModel::AI_POLICY_OFF) {
            $rounds[0]['questions'][] = [
                'question' => 'How would your previous manager describe your working style?',
                'why_it_matters' => 'This gives an early signal of communication and team fit.',
                'answer_tip' => 'Stay specific and support the claim with one short example.',
            ];
        }

        if ($experienceLevel !== '') {
            $rounds[2]['questions'][] = [
                'question' => 'How does your experience level of ' . $experienceLevel . ' translate into value for this role?',
                'why_it_matters' => 'Interviewers will test whether your years map to real ownership.',
                'answer_tip' => 'Link your experience to depth, scope, and outcomes rather than just time served.',
            ];
        }

        $fallback = [
            'title' => $jobTitle . ' Mock Interview',
            'intro' => 'Use this detailed interview rehearsal to practice the exact stories, technical decisions, and role-fit points most likely to come up next.',
            'focus_skills' => $focusSkills,
            'rounds' => $rounds,
            'answer_framework' => [
                'Start with the context in one or two lines before describing your action.',
                'Quantify outcomes where possible instead of describing work in vague terms.',
                'Keep each answer tightly aligned to the job requirements and your submitted resume version.',
                'When discussing tradeoffs, explain why you chose a path, not just what you built.',
            ],
            'evaluation_points' => [
                'Clear evidence that your strongest projects match the job scope.',
                'Confident explanation of the top required skills and how you used them.',
                'Strong communication structure without rambling.',
                'Consistent story between resume, application, and spoken examples.',
            ],
            'final_checklist' => [
                'Rehearse your strongest project walkthrough until it is concise and outcome-focused.',
                'Prepare one challenge story, one collaboration story, and one ownership story.',
                'Review the required skills and keep one concrete example ready for each major skill.',
                'Keep your submitted resume version open while practicing so your answers stay consistent.',
                $policy === JobModel::AI_POLICY_OFF
                    ? 'Prepare a recruiter-facing introduction that explains why this role is the right next step.'
                    : 'Practice delivering short, camera-friendly answers for the AI or recorded screening round.',
            ],
            'source' => 'fallback',
        ];

        return (new AiInterviewPrepCoach())->generateMockInterview($application, $fallback);
    }

    private function buildJobSearchStrategyCoach(int $candidateId, array $applications, array $topSuggestedJobs): array
    {
        $user = (new UserModel())->findCandidateWithProfile($candidateId) ?? [];
        $skillsRow = (new CandidateSkillsModel())->where('candidate_id', $candidateId)->first() ?? [];
        $skills = $this->tokenizeCsv((string) ($skillsRow['skill_name'] ?? ''));
        $behavior = (new JobModel())->getCandidateBehaviorProfile($candidateId);

        $activeApplications = count(array_filter($applications, static function (array $application): bool {
            return !in_array((string) ($application['status'] ?? ''), ['rejected', 'withdrawn', 'selected', 'hired'], true);
        }));
        $appliedCount = count($applications);
        $shortlistedCount = count(array_filter($applications, static function (array $application): bool {
            return in_array((string) ($application['status'] ?? ''), ['shortlisted', 'interview_slot_booked', 'selected', 'hired'], true);
        }));

        $topCategories = array_values(array_filter(array_map(static function (array $row): string {
            return trim((string) ($row['category'] ?? ''));
        }, (array) ($behavior['top_categories'] ?? []))));
        $topLocations = array_values(array_filter(array_map(static function (array $row): string {
            return trim((string) ($row['location'] ?? ''));
        }, (array) ($behavior['top_locations'] ?? []))));

        $suggestedJobsContext = array_map(static function (array $job): array {
            return [
                'id' => (int) ($job['id'] ?? 0),
                'title' => (string) ($job['title'] ?? ''),
                'company' => (string) ($job['company'] ?? ''),
                'location' => (string) ($job['location'] ?? ''),
                'match_score' => (float) ($job['match_score'] ?? 0),
                'required_skills' => (string) ($job['required_skills'] ?? ''),
                'experience_level' => (string) ($job['experience_level'] ?? ''),
            ];
        }, $topSuggestedJobs);

        $context = [
            'profile' => [
                'resume_headline' => (string) ($user['resume_headline'] ?? ''),
                'bio_present' => !empty($user['bio']),
                'preferred_job_titles' => (string) ($user['preferred_job_titles'] ?? ''),
                'preferred_locations' => (string) ($user['preferred_locations'] ?? ''),
                'preferred_employment_type' => (string) ($user['preferred_employment_type'] ?? ''),
                'resume_uploaded' => !empty($user['resume_path']),
                'skills' => array_slice($skills, 0, 12),
            ],
            'behavior' => [
                'top_categories' => array_slice($topCategories, 0, 4),
                'top_locations' => array_slice($topLocations, 0, 4),
                'top_experience_levels' => array_slice((array) ($behavior['top_experience_levels'] ?? []), 0, 3),
                'top_employment_types' => array_slice((array) ($behavior['top_employment_types'] ?? []), 0, 3),
                'applied_skill_frequency' => array_slice((array) ($behavior['applied_skill_frequency'] ?? []), 0, 8, true),
            ],
            'pipeline' => [
                'applied_count' => $appliedCount,
                'active_count' => $activeApplications,
                'shortlisted_count' => $shortlistedCount,
            ],
            'suggested_jobs' => $suggestedJobsContext,
        ];

        $recommendedJobIds = array_values(array_filter(array_map(static function (array $job): int {
            return (int) ($job['id'] ?? 0);
        }, array_slice($topSuggestedJobs, 0, 3))));

        $fallback = [
            'title' => 'Job Search Strategy Coach',
            'summary' => 'Use your strongest matching roles and current application behavior to focus on a narrower, higher-conversion search over the next 1 to 2 weeks.',
            'target_roles' => $this->deriveTargetRoles($user, $topSuggestedJobs, $topCategories),
            'priority_actions' => $this->buildPriorityActions($user, $skills, $topSuggestedJobs),
            'profile_fixes' => $this->buildProfileFixes($user, $skills),
            'application_strategy' => $this->buildApplicationStrategy($topSuggestedJobs, $activeApplications, $shortlistedCount),
            'weekly_plan' => $this->buildWeeklyPlan($topSuggestedJobs),
            'watchouts' => $this->buildWatchouts($activeApplications, $skills, $topSuggestedJobs),
            'recommended_job_ids' => $recommendedJobIds,
            'source' => 'fallback',
        ];

        return (new AiJobSearchStrategyCoach())->generate($candidateId, $context, $fallback);
    }

    private function deriveTargetRoles(array $user, array $topSuggestedJobs, array $topCategories): array
    {
        $roles = [];
        $preferredTitles = preg_split('/[,|\\/]+/', (string) ($user['preferred_job_titles'] ?? '')) ?: [];
        foreach ($preferredTitles as $title) {
            $title = trim((string) $title);
            if ($title !== '') {
                $roles[] = $title;
            }
        }
        foreach ($topSuggestedJobs as $job) {
            $title = trim((string) ($job['title'] ?? ''));
            if ($title !== '') {
                $roles[] = $title;
            }
        }
        foreach ($topCategories as $category) {
            if ($category !== '') {
                $roles[] = $category . ' roles';
            }
        }

        return array_values(array_slice(array_unique($roles), 0, 4));
    }

    private function buildPriorityActions(array $user, array $skills, array $topSuggestedJobs): array
    {
        $actions = [];
        if (!empty($topSuggestedJobs)) {
            $bestJob = $topSuggestedJobs[0];
            $actions[] = 'Prioritize roles similar to "' . trim((string) ($bestJob['title'] ?? 'your strongest match')) . '" where your match score is already highest.';
        }
        if (empty($user['preferred_locations'])) {
            $actions[] = 'Set clear preferred locations so job suggestions and recruiter visibility become more targeted.';
        }
        if (count($skills) < 5) {
            $actions[] = 'Add more verified skills to your profile so matching is not driven by a narrow keyword set.';
        } else {
            $actions[] = 'Center your search around the 3 to 5 skills that appear most often in your strongest matching jobs.';
        }
        $actions[] = 'Apply selectively to roles with strong skill overlap instead of spreading effort across loosely related openings.';

        return array_values(array_slice($actions, 0, 5));
    }

    private function buildProfileFixes(array $user, array $skills): array
    {
        $fixes = [];
        if (empty($user['resume_headline'])) {
            $fixes[] = 'Add a sharper resume headline that states your target role and strongest stack.';
        }
        if (empty($user['bio'])) {
            $fixes[] = 'Complete your profile bio so recruiters can understand your fit beyond keywords.';
        }
        if (empty($user['resume_path'])) {
            $fixes[] = 'Upload a base resume so role-based resume versions and recruiter downloads have a stronger source.';
        }
        if (count($skills) > 0) {
            $fixes[] = 'Keep profile skills and resume skills aligned so your visible profile tells the same story as your applications.';
        }
        if (empty($user['preferred_employment_type'])) {
            $fixes[] = 'Set preferred employment type to reduce noise in recommendations.';
        }

        return array_values(array_slice($fixes, 0, 5));
    }

    private function buildApplicationStrategy(array $topSuggestedJobs, int $activeApplications, int $shortlistedCount): array
    {
        $strategy = [];
        if (!empty($topSuggestedJobs)) {
            $strategy[] = 'Start with the highest-match suggested jobs and tailor your resume before applying.';
        }
        if ($activeApplications > 5) {
            $strategy[] = 'Reduce parallel active applications and spend more time on interview readiness for the strongest ones.';
        } else {
            $strategy[] = 'Maintain a focused application pipeline with a small number of strong-fit roles each week.';
        }
        if ($shortlistedCount <= 0) {
            $strategy[] = 'Track which role types are not converting and narrow your search toward jobs with stronger skill overlap.';
        } else {
            $strategy[] = 'Use shortlisted roles as your benchmark and apply to similar jobs in title, stack, and experience band.';
        }
        $strategy[] = 'Avoid applying with one generic resume when the job clearly emphasizes a specific stack or domain.';

        return array_values(array_slice($strategy, 0, 5));
    }

    private function buildWeeklyPlan(array $topSuggestedJobs): array
    {
        $plan = [
            'Review your top suggested jobs and shortlist the 3 best-fit roles for focused applications.',
            'Improve one resume version for your highest-priority role before applying.',
            'Apply to a small batch of strong-match roles instead of a broad volume of weak matches.',
            'Review any active applications and move time into interview preparation for the ones with momentum.',
            'Refresh profile skills, preferences, and headline based on the latest target roles.',
        ];

        if (!empty($topSuggestedJobs)) {
            $plan[1] = 'Improve one resume version for "' . trim((string) ($topSuggestedJobs[0]['title'] ?? 'your top role')) . '" before applying.';
        }

        return $plan;
    }

    private function buildWatchouts(int $activeApplications, array $skills, array $topSuggestedJobs): array
    {
        $watchouts = [];
        if ($activeApplications > 7) {
            $watchouts[] = 'Too many open applications can dilute preparation quality and make follow-up weaker.';
        }
        if (count($skills) < 4) {
            $watchouts[] = 'A thin visible skill set can suppress matching even when you have stronger experience.';
        }
        if (empty($topSuggestedJobs)) {
            $watchouts[] = 'Weak suggestions usually mean profile targeting is too broad or skill coverage is unclear.';
        } else {
            $watchouts[] = 'Do not ignore low match gaps like missing frameworks if they appear repeatedly in your best-fit roles.';
        }

        return array_values(array_slice($watchouts, 0, 4));
    }
}
