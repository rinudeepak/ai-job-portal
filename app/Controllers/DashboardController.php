<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    /**
     * Admin Dashboard - Main Overview
     */
    public function index()
    {
        $applicationModel = model('ApplicationModel');
        $userModel = model('UserModel');
        $jobModel = model('JobModel');
        $slotModel = model('InterviewSlotModel');
        $interviewModel = model('InterviewSessionModel');

        // Get current recruiter/admin ID
        $currentUserId = session()->get('user_id');
        $jobIds = [];
        // Get jobs posted by this recruiter
        $recruiterJobs = $jobModel->where('recruiter_id', $currentUserId)->findAll();
        $jobIds = array_column($recruiterJobs, 'id');

        // If no jobs posted, show empty dashboard
        if (empty($jobIds)) {
            return view('recruiter/dashboard/index', [
                'funnel' => [
                    'total_applications' => 0,
                    'ai_interview_started' => 0,
                    'ai_interview_completed' => 0,
                    'shortlisted' => 0,
                    'rejected' => 0,
                    'interview_slot_booked' => 0

                ],
                'pendingActions' => [
                    'pending_screening' => 0,
                    'ai_interviews_to_review' => 0,
                    'hr_interviews_today' => 0,
                    'pending_offers' => 0
                ],
                'recentApplications' => [],
                'stageTimeAnalytics' => [],
                'jobStats' => [
                    'active_jobs' => 0,
                    'total_positions' => 0,
                    'available_slots' => 0
                ],
                'topJobs' => [],
                'conversionMetrics' => [],
                'monthlyTrends' => [],
                'noJobs' => true
            ]);
        }
        // Build base query for applications
        $applicationBuilder = $applicationModel;
        if (!empty($jobIds)) {
            $applicationBuilder = $applicationBuilder->whereIn('job_id', $jobIds);
        }


        // Candidate Funnel Overview
        $funnel = [
            'total_applications' => $applicationBuilder->countAllResults(false),
            'ai_interview_started' => $applicationBuilder->where('status', 'ai_interview_started')->countAllResults(false),
            'ai_interview_completed' => $applicationBuilder->where('status', 'ai_interview_completed')->countAllResults(false),
            'shortlisted' => $applicationBuilder->where('status', 'shortlisted')->countAllResults(false),
            'rejected' => $applicationBuilder->where('status', 'rejected')->countAllResults(false),
            'interview_slot_booked' => $applicationBuilder->where('status', 'interview_slot_booked')->countAllResults(false)

        ];

        // Pending Actions Count
        $pendingActions = [
            'pending_screening' => $applicationModel->where('status', 'pending')
                ->whereIn('job_id', $jobIds ?: [0])
                ->countAllResults(),
            'ai_interviews_to_review' => $interviewModel
                ->join('applications', 'applications.id = interview_sessions.application_id')
                ->where('applications.status', 'ai_interview_completed')
                ->where('interview_sessions.overall_rating >', 0)
                ->whereIn('job_id', $jobIds ?: [0])
                ->countAllResults(),

            'hr_interviews_today' => model('InterviewBookingModel')
                ->where('slot_datetime', date('Y-m-d'))
                ->where('booking_status', 'confirmed')
                ->whereIn('job_id', $jobIds ?: [0])
                ->countAllResults()
            // 'pending_offers' => $applicationModel->where('status', 'selected')
            //                                      ->where('offer_status', 'pending')
            // ->whereIn('job_id', $jobIds ?: [0])
            //                                      ->countAllResults()
        ];

        // Recent Activity
        $recentApplicationsBuilder = $applicationModel
            ->select('applications.*, users.name as candidate_name, jobs.title as job_title')
            ->join('users', 'users.id = applications.candidate_id', 'left')
            ->join('jobs', 'jobs.id = applications.job_id', 'left');
        if (!empty($jobIds)) {
            $recentApplicationsBuilder->whereIn('applications.job_id', $jobIds);
        }
        $recentApplications = $recentApplicationsBuilder
            ->orderBy('applications.applied_at', 'DESC')
            ->limit(10)
            ->find();

        // Stage Time Analytics (Average days in each stage)
        $stageTimeAnalytics = $this->calculateStageTimeAnalytics($jobIds);

        // Job Statistics
        $jobStats = [
            'active_jobs' => $jobModel->where('status', 'active')->countAllResults(),
            'total_positions' => $jobModel->selectSum('openings')->where('status', 'active')->get()->getRow()->openings ?? 0,
            'available_slots' => $slotModel->where('is_available', 1)
                ->where('slot_datetime >', date('Y-m-d H:i:s'))
                ->whereIn('job_id', $jobIds ?: [0])
                ->countAllResults()
        ];

        // Top Jobs by Applications - Only recruiter's jobs
        $topJobsBuilder = $applicationModel
            ->select('jobs.title, jobs.id, COUNT(applications.id) as application_count')
            ->join('jobs', 'jobs.id = applications.job_id', 'left');

        if (!empty($jobIds)) {
            $topJobsBuilder->whereIn('applications.job_id', $jobIds);
        }

        // Top Jobs by Applications
        $topJobs = $topJobsBuilder
            ->groupBy('applications.job_id')
            ->orderBy('application_count', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();


        // Conversion Metrics
        $conversionMetrics = $this->calculateConversionMetrics();

        // Monthly Trends (Last 6 months)
        $monthlyTrends = $this->getMonthlyTrends();

        return view('recruiter/dashboard/index', [
            'funnel' => $funnel,
            'pendingActions' => $pendingActions,
            'recentApplications' => $recentApplications,
            'stageTimeAnalytics' => $stageTimeAnalytics,
            'jobStats' => $jobStats,
            'topJobs' => $topJobs,
            'conversionMetrics' => $conversionMetrics,
            'monthlyTrends' => $monthlyTrends
        ]);
    }

    /**
     * Skill Leaderboard
     */
    public function leaderboard()
    {
        $applicationModel = model('ApplicationModel');
        $jobModel = model('JobModel');

        // Get current recruiter/admin ID
        $currentUserId = session()->get('user_id');
        $jobIds = [];
        // Get jobs posted by this recruiter
        $recruiterJobs = $jobModel->where('recruiter_id', $currentUserId)->findAll();
        $jobIds = array_column($recruiterJobs, 'id');

        // If no jobs posted, show empty dashboard
        if (empty($jobIds)) {
            return view('recruiter/dashboard/leaderboard', [
                'candidates' => [],
                'pager' => $applicationModel->pager,
                'skills' => [],
                'jobs' => [],
                'filters' => [
                    'skill' => null,
                    'sort_by' => 'technical_score',
                    'job_id' => null
                ],
                'noJobs' => true
            ]);
        }
        // Get filters
        $skill = $this->request->getGet('skill');
        $sortBy = $this->request->getGet('sort_by') ?? 'technical_score';
        $jobId = $this->request->getGet('job_id');

        // Build query
        $builder = $applicationModel
            ->select('applications.*, users.name, users.email, jobs.title as job_title, jobs.required_skills, interview_sessions.technical_score,
                    interview_sessions.communication_score,
                    interview_sessions.overall_rating')
            ->join('users', 'users.id = applications.candidate_id', 'left')
            ->join('jobs', 'jobs.id = applications.job_id', 'left')
            ->join('interview_sessions', 'interview_sessions.application_id = applications.id', 'left')
            ->where('interview_sessions.overall_rating IS NOT NULL')
            ->where('interview_sessions.overall_rating >', 0);
        // Filter by recruiter's jobs only
        if (!empty($jobIds)) {
            $builder->whereIn('applications.job_id', $jobIds);
        }

        // Apply job filter
        if ($jobId) {
            $builder->where('applications.job_id', $jobId);
        }

        // Apply skill filter (search in candidate_skills table)
        if ($skill) {
            $builder->join('candidate_skills', 'candidate_skills.candidate_id = applications.candidate_id', 'left');
            $builder->where("FIND_IN_SET(" . $builder->db->escape($skill) . ", candidate_skills.skill_name) >", 0);
            $builder->groupBy('applications.id');
        }


        // Apply sorting
        if ($sortBy === 'technical_score') {
            $builder->orderBy('interview_sessions.technical_score', 'DESC');
        } elseif ($sortBy === 'overall_rating') {
            $builder->orderBy('interview_sessions.overall_rating', 'DESC');
        } elseif ($sortBy === 'communication_score') {
            $builder->orderBy('interview_sessions.communication_score', 'DESC');
        }

        $builder->orderBy('applications.applied_at', 'DESC');

        $candidates = $builder->paginate(10);
        $pager = $applicationModel->pager;

        // Get candidate skills for each candidate
        foreach ($candidates as &$candidate) {
            $candidate['candidate_skills'] = $this->getCandidateSkills($candidate['candidate_id']);
            $candidate['required_skills'] = $this->parseRequiredSkills($candidate['required_skills']);
            $candidate['skill_match'] = $this->calculateSkillMatch(
                $candidate['candidate_skills'],
                $candidate['required_skills']
            );
        }

        // Get unique skills for filter
        $allSkills = $this->extractAllSkills();

        // Get all jobs for filter
        $jobs = $jobModel->findAll();

        // Calculate ranks
        $candidates = $this->assignRanks($candidates, $sortBy);


        return view('recruiter/dashboard/leaderboard', [
            'candidates' => $candidates,
            'pager' => $pager,
            'skills' => $allSkills,
            'jobs' => $jobs,
            'filters' => [
                'skill' => $skill,
                'sort_by' => $sortBy,
                'job_id' => $jobId
            ]
        ]);
    }

    /**
     * Export Dashboard Data to Excel
     */
    public function exportExcel()
    {
        $type = $this->request->getGet('type') ?? 'overview';
        // Get current recruiter/admin ID and role
        $currentUserId = session()->get('user_id');
        // Get job IDs for recruiter filtering
        $jobIds = [];
        $jobModel = model('JobModel');
        $recruiterJobs = $jobModel->where('recruiter_id', $currentUserId)->findAll();
        $jobIds = array_column($recruiterJobs, 'id');

        // If no jobs, return error
        if (empty($jobIds)) {
            return redirect()->back()->with('error', 'You have no jobs to export data from.');
        }


        // try {


        switch ($type) {
            case 'overview':
                $data = $this->getOverviewExportData($jobIds);
                $filename = 'recruitment_overview_' . date('Y-m-d');
                break;

            case 'leaderboard':
                $data = $this->getLeaderboardExportData($jobIds);
                $filename = 'candidate_leaderboard_' . date('Y-m-d');
                break;

            case 'funnel':
                $data = $this->getFunnelExportData($jobIds);
                $filename = 'recruitment_funnel_' . date('Y-m-d');
                break;

            case 'detailed':
                $data = $this->getDetailedExportData($jobIds);
                $filename = 'recruitment_detailed_' . date('Y-m-d');
                break;

            default:
                return redirect()->back()->with('error', 'Invalid export type');
        }

        // Check if PhpSpreadsheet is available
        // if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
        //     log_message('warning', 'PhpSpreadsheet not found, falling back to CSV export');
        //     return $this->exportAsCSV($data, $filename);
        // }

        // Generate Excel file
        $excelPath = $this->generateExcelReport($data, $filename);

        return $this->response->download($excelPath, null)->setFileName($filename . '.xlsx');

        // } catch (\Exception $e) {
        //     log_message('error', 'Export failed: ' . $e->getMessage());

        //     // Try CSV fallback on any error
        //     try {
        //         return $this->exportAsCSV($data, $filename);
        //     } catch (\Exception $csvError) {
        //         return redirect()->back()->with('error', 'Export failed. Please contact administrator.');
        //     }
        // }

    }

    /**
     * Calculate Stage Time Analytics
     */
    private function calculateStageTimeAnalytics($jobIds = [])
    {
        $db = \Config\Database::connect();
        $whereClause = '';
        if (!empty($jobIds)) {
            $jobIdsStr = implode(',', $jobIds);
            $whereClause = "AND job_id IN ($jobIdsStr)";
        }


        // This is a simplified version - you might want to track stage transitions in a separate table
        $query = "
            SELECT 
                status,
                AVG(DATEDIFF(NOW(), applied_at)) as avg_days
            FROM applications
            WHERE status IN ('applied', 'ai_interview_started', 'ai_interview_completed', 'shortlisted', 'interview_slot_booked')
            $whereClause
            GROUP BY status
        ";

        $results = $db->query($query)->getResultArray();

        $analytics = [];
        foreach ($results as $row) {
            $analytics[$row['status']] = round($row['avg_days'], 1);
        }

        return $analytics;
    }

    /**
     * Calculate Conversion Metrics
     */
    private function calculateConversionMetrics($jobIds = [])
    {
        $applicationModel = model('ApplicationModel');
        // Apply job filter if provided
        $builder = $applicationModel;
        if (!empty($jobIds)) {
            $builder = $builder->whereIn('job_id', $jobIds);
        }

        $total = $builder->countAllResults(false);
        if ($total == 0)
            return [];


        // Reset and reapply filter for each query
        $aiCompleted = $applicationModel->where('status', 'ai_interview_completed');
        if (!empty($jobIds))
            $aiCompleted->whereIn('job_id', $jobIds);
        $aiCompletedCount = $aiCompleted->countAllResults();

        $shortlisted = $applicationModel->where('status', 'shortlisted');
        if (!empty($jobIds))
            $shortlisted->whereIn('job_id', $jobIds);
        $shortlistedCount = $shortlisted->countAllResults();

        $hrScheduled = $applicationModel->where('status', 'interview_slot_booked');
        if (!empty($jobIds))
            $hrScheduled->whereIn('job_id', $jobIds);
        $hrScheduledCount = $hrScheduled->countAllResults();

        $hrCompleted = $applicationModel->where('status', 'hr_interview_completed');
        if (!empty($jobIds))
            $hrCompleted->whereIn('job_id', $jobIds);
        $hrCompletedCount = $hrCompleted->countAllResults();

        $selected = $applicationModel->where('status', 'selected');
        if (!empty($jobIds))
            $selected->whereIn('job_id', $jobIds);
        $selectedCount = $selected->countAllResults();

        return [
            'application_to_ai_interview' => round(($aiCompletedCount / $total) * 100, 1),
            'ai_interview_to_shortlist' => round(($shortlistedCount / max(1, $aiCompletedCount)) * 100, 1),
            'shortlist_to_hr_interview' => round(($hrScheduledCount / max(1, $shortlistedCount)) * 100, 1),
            'hr_interview_to_selection' => round(($selectedCount / max(1, $hrCompletedCount)) * 100, 1),
            'overall_conversion' => round(($selectedCount / $total) * 100, 1)
        ];

    }

    /**
     * Get Monthly Trends
     */
    private function getMonthlyTrends($jobIds = [])
    {
        $db = \Config\Database::connect();
        $whereClause = '';
        if (!empty($jobIds)) {
            $jobIdsStr = implode(',', $jobIds);
            $whereClause = "AND job_id IN ($jobIdsStr)";
        }

        $query = "
            SELECT 
                DATE_FORMAT(applied_at, '%Y-%m') as month,
                COUNT(*) as total_applications,
                SUM(CASE WHEN status = 'shortlisted' THEN 1 ELSE 0 END) as shortlisted,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            FROM applications
            WHERE applied_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            $whereClause
            GROUP BY DATE_FORMAT(applied_at, '%Y-%m')
            ORDER BY month ASC
        ";

        return $db->query($query)->getResultArray();
    }

    /**
     * Extract All Skills from Jobs
     */
    private function extractAllSkills()
    {
        $db = \Config\Database::connect();

        $skillModel = model('SkillsModel');

        $rows = $skillModel
            ->select('skill_name')
            ->orderBy('skill_name', 'ASC')
            ->findAll();

        $skills = [];

        foreach ($rows as $row) {
            if (!empty($row['skill_name'])) {
                $skills[] = trim($row['skill_name']);
            }
        }


        return $skills;
    }


    /**
     * Assign Ranks to Candidates
     */
    private function assignRanks($candidates, $sortBy)
    {
        $rank = 1;
        foreach ($candidates as &$candidate) {
            $candidate['rank'] = $rank++;
        }
        return $candidates;
    }
    /**
     * Get Candidate Skills from candidate_skills table
     */
    private function getCandidateSkills($candidateId)
    {
        $db = \Config\Database::connect();

        $row = $db->table('candidate_skills')
            ->where('candidate_id', $candidateId)
            ->get()
            ->getRowArray();

        if (!$row)
            return [];
        return array_map('trim', explode(',', $row['skill_name']));

    }

    /**
     * Parse Required Skills from JSON
     */
    private function parseRequiredSkills($requiredSkillsJson)
    {
        if (empty($requiredSkillsJson)) {
            return [];
        }

        return array_map('trim', explode(',', $requiredSkillsJson));

    }

    /**
     * Calculate Skill Match Percentage
     */
    private function calculateSkillMatch($candidateSkills, $requiredSkills)
    {
        if (empty($candidateSkills) || empty($requiredSkills))
            return 0;

        $matched = array_intersect($candidateSkills, $requiredSkills);
        return round((count($matched) / count($requiredSkills)) * 100);
    }


    /**
     * Get Overview Export Data
     */
    private function getOverviewExportData($jobIds = [])
    {

        return [
            'Summary' => $this->getSummarySheet($jobIds),
            'Applications' => $this->getApplicationsSheet($jobIds),
            'Job Statistics' => $this->getJobStatsSheet($jobIds)
        ];
    }

    /**
     * Get Leaderboard Export Data
     */
    private function getLeaderboardExportData($jobIds = [])
    {
        $applicationModel = model('ApplicationModel');

        $builder = $applicationModel
            ->select('applications.*, users.name, users.email, jobs.title as job_title, jobs.required_skills as required_skills,
                    interview_sessions.technical_score,
                    interview_sessions.communication_score,
                    interview_sessions.overall_rating')
            ->join('users', 'users.id = applications.candidate_id', 'left')
            ->join('jobs', 'jobs.id = applications.job_id', 'left')
            ->join('interview_sessions', 'interview_sessions.application_id = applications.id', 'left')
            ->where('interview_sessions.overall_rating IS NOT NULL');
        // Filter by job IDs if provided (for recruiters)
        if (!empty($jobIds)) {
            $builder->whereIn('applications.job_id', $jobIds);
        }

        $candidates = $builder
            ->orderBy('interview_sessions.overall_rating', 'DESC')
            ->findAll();


        $data = [
            ['Rank', 'Name', 'Email', 'Job', 'Required Skills', 'Technical Score', 'Communication Score', 'Overall Rating', 'Status']
        ];

        $rank = 1;
        foreach ($candidates as $candidate) {
            $data[] = [
                $rank++,
                $candidate['name'],
                $candidate['email'],
                $candidate['job_title'],
                $candidate['required_skills'],
                $candidate['technical_score'] ?? 0,
                $candidate['communication_score'] ?? 0,
                $candidate['overall_rating'] ?? 0,
                $candidate['status']

            ];
        }


        return ['Leaderboard' => $data];
    }

    /**
     * Get Funnel Export Data
     */
    private function getFunnelExportData($jobIds = [])
    {
        $applicationModel = model('ApplicationModel');

        $stages = [
            'Total Applications',
            'AI Interview Started',
            'AI Interview Completed',
            'Shortlisted',
            'Rejected',
            'Interview Slot Booked',

        ];

        $data = [
            ['Stage', 'Count', 'Percentage']
        ];

        // Get total with job filter
        if (!empty($jobIds)) {
            $total = $applicationModel->whereIn('job_id', $jobIds)->countAllResults();
        } else {
            $total = $applicationModel->countAll();
        }


        foreach ($stages as $stage) {
            $count = $this->getStageCount($stage);
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            $data[] = [$stage, $count, $percentage . '%'];
        }

        return ['Funnel Analysis' => $data];
    }

    /**
     * Get Detailed Export Data
     */
    private function getDetailedExportData($jobIds = [])
    {
        $applicationModel = model('ApplicationModel');

        $builder = $applicationModel
            ->select('applications.*, users.name, users.email, jobs.title as job_title,
            interview_sessions.overall_rating')
            ->join('users', 'users.id = applications.candidate_id', 'left')
            ->join('jobs', 'jobs.id = applications.job_id', 'left')
            ->join('interview_sessions', 'interview_sessions.application_id = applications.id', 'left');
        // Filter by job IDs if provided (for recruiters)
        if (!empty($jobIds)) {
            $builder->whereIn('applications.job_id', $jobIds);
        }

        $applications = $builder
            ->orderBy('applications.applied_at', 'DESC')
            ->findAll();

        $data = [
            [
                'ID',
                'Name',
                'Email',
                'Job',
                'Status',
                'Technical Score',
                'Communication Score',
                'Overall Rating',
                'Applied Date'

            ]
        ];

        foreach ($applications as $app) {
            $data[] = [
                $app['id'],
                $app['name'],
                $app['email'],
                $app['job_title'],
                $app['status'],
                $app['technical_score'] ?? 0,
                $app['communication_score'] ?? 0,
                $app['overall_rating'] ?? 0,
                date('Y-m-d', strtotime($app['applied_at']))

            ];
        }

        return ['Detailed Report' => $data];
    }

    /**
     * Generate Excel Report
     */
    private function generateExcelReport($data, $filename)
    {
        require_once ROOTPATH . 'vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach ($data as $sheetName => $sheetData) {
            $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $sheetName);
            $spreadsheet->addSheet($sheet);

            // Add data
            $sheet->fromArray($sheetData, null, 'A1');

            // Style header row
            $headerStyle = [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ];

            $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($headerStyle);

            // Auto-size columns
            foreach (range('A', $sheet->getHighestColumn()) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }
        }

        // Save file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filepath = WRITEPATH . 'uploads/' . $filename . '.xlsx';
        $writer->save($filepath);

        return $filepath;
    }

    /**
     * CSV Export Fallback
     */
    private function exportAsCSV($data, $filename)
    {
        // Flatten multi-sheet data into single CSV
        $csvData = [];

        foreach ($data as $sheetName => $sheetData) {
            $csvData[] = ['=== ' . $sheetName . ' ==='];
            $csvData = array_merge($csvData, $sheetData);
            $csvData[] = [''];  // Empty row between sheets
        }

        // Ensure directory exists
        $uploadDir = WRITEPATH . 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate CSV
        $filepath = $uploadDir . $filename . '.csv';

        $fp = fopen($filepath, 'w');

        foreach ($csvData as $row) {
            fputcsv($fp, $row);
        }

        fclose($fp);

        return $this->response->download($filepath, null)->setFileName($filename . '.csv');
    }


    /**
     * Helper Methods
     */
    private function getSummarySheet($jobIds = [])
    {
        $applicationModel = model('ApplicationModel');

        $builder = $applicationModel;
        if (!empty($jobIds)) {
            $builder = $builder->whereIn('job_id', $jobIds);
        }

        $total = $builder->countAllResults(false);

        $activeBuilder = clone $builder;
        $active = $activeBuilder->whereIn('status', 'ai_interview_started')->countAllResults();

        $completedBuilder = clone $builder;
        $completed = $completedBuilder->where('status', 'ai_interview_completed')->countAllResults();

        $selectedBuilder = clone $builder;
        $selected = $selectedBuilder->where('status', 'shortlisted')->countAllResults();

        $rejectedBuilder = clone $builder;
        $rejected = $rejectedBuilder->where('status', 'rejected')->countAllResults();

        return [
            ['Metric', 'Value'],
            ['Total Applications', $total],
            ['Active Applications', $active],
            ['Completed Interviews', $completed],
            ['Shortlisted Candidates', $selected],
            ['Rejected Candidates', $rejected],
            ['Generated On', date('Y-m-d H:i:s')]
        ];

    }

    private function getApplicationsSheet($jobIds = [])
    {
        $applicationModel = model('ApplicationModel');

        $builder = $applicationModel
            ->select('applications.id, users.name, jobs.title as job, applications.status, applications.applied_at')
            ->join('users', 'users.id = applications.candidate_id', 'left')
            ->join('jobs', 'jobs.id = applications.job_id', 'left');
        if (!empty($jobIds)) {
            $builder->whereIn('applications.job_id', $jobIds);
        }

        $apps = $builder
            ->orderBy('applications.applied_at', 'DESC')
            ->limit(1000)
            ->findAll();

        $data = [['ID', 'Candidate', 'Job', 'Status', 'Applied Date']];

        foreach ($apps as $app) {
            $data[] = [
                $app['id'],
                $app['name'],
                $app['job'],
                $app['status'],
                date('Y-m-d', strtotime($app['applied_at']))
            ];
        }

        return $data;
    }

    private function getJobStatsSheet($jobIds = [])
    {
        $db = \Config\Database::connect();
        $whereClause = '';
        if (!empty($jobIds)) {
            $jobIdsStr = implode(',', $jobIds);
            $whereClause = "WHERE jobs.id IN ($jobIdsStr)";
        }

        $query = "
            SELECT 
                jobs.title,
                COUNT(applications.id) as total_applications,
                SUM(CASE WHEN applications.status = 'selected' THEN 1 ELSE 0 END) as selected,
                SUM(CASE WHEN applications.status = 'rejected' THEN 1 ELSE 0 END) as rejected
            FROM jobs
            LEFT JOIN applications ON applications.job_id = jobs.id
            $whereClause
            GROUP BY jobs.id
            ORDER BY total_applications DESC
        ";

        $results = $db->query($query)->getResultArray();

        $data = [['Job Title', 'Total Applications', 'Selected', 'Rejected']];

        foreach ($results as $row) {
            $data[] = [
                $row['title'],
                $row['total_applications'],
                $row['selected'],
                $row['rejected']
            ];
        }

        return $data;
    }

    private function getStageCount($stage, $jobIds = [])
    {
        $applicationModel = model('ApplicationModel');

        $statusMap = [
            'Total Applications' => null,
            'AI Interview Scheduled' => 'ai_interview_started',
            'AI Interview Completed' => 'ai_interview_completed',
            'Shortlisted' => 'shortlisted',
            'Rejected' => 'rejected',
            'Interview Slot Booked' => 'interview_slot_booked'

        ];
        $builder = $applicationModel;

        if (!empty($jobIds)) {
            $builder = $builder->whereIn('job_id', $jobIds);
        }

        if ($stage === 'Total Applications') {
            return $builder->countAllResults();
        }

        $status = $statusMap[$stage] ?? null;
        if ($status) {
            return $builder->where('status', $status)->countAllResults();
        }

        return 0;
    }
}