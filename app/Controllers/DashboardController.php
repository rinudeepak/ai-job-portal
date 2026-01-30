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

        // Candidate Funnel Overview
        $funnel = [
            'total_applications' => $applicationModel->countAll(),
            'ai_interview_started' => $applicationModel->where('status', 'ai_interview_started')->countAllResults(),
            'ai_interview_completed' => $applicationModel->where('status', 'ai_interview_completed')->countAllResults(),
            'shortlisted' => $applicationModel->where('status', 'shortlisted')->countAllResults(),
            'rejected' => $applicationModel->where('status', 'rejected')->countAllResults(),
            'interview_slot_booked' => $applicationModel->where('status', 'interview_slot_booked')->countAllResults()
            
        ];

        // Pending Actions Count
        $pendingActions = [
            'pending_screening' => $applicationModel->where('status', 'pending')->countAllResults(),
            'ai_interviews_to_review' => $interviewModel
                ->join('applications', 'applications.id = interview_sessions.application_id')
                ->where('applications.status', 'ai_interview_completed')
                ->where('interview_sessions.overall_rating >', 0)
                ->countAllResults(),

            'hr_interviews_today' => model('InterviewBookingModel')
                ->where('slot_datetime', date('Y-m-d'))
                ->where('booking_status', 'confirmed')
                ->countAllResults()
            // 'pending_offers' => $applicationModel->where('status', 'selected')
            //                                      ->where('offer_status', 'pending')
            //                                      ->countAllResults()
        ];

        // Recent Activity
        $recentApplications = $applicationModel
            ->select('applications.*, users.name as candidate_name, jobs.title as job_title')
            ->join('users', 'users.id = applications.candidate_id', 'left')
            ->join('jobs', 'jobs.id = applications.job_id', 'left')
            ->orderBy('applications.applied_at', 'DESC')
            ->limit(10)
            ->find();

        // Stage Time Analytics (Average days in each stage)
        $stageTimeAnalytics = $this->calculateStageTimeAnalytics();

        // Job Statistics
        $jobStats = [
            'active_jobs' => $jobModel->where('status', 'active')->countAllResults(),
            'total_positions' => $jobModel->selectSum('openings')->where('status', 'active')->get()->getRow()->openings ?? 0,
            'available_slots' => $slotModel->where('is_available', 1)
                ->where('slot_datetime >', date('Y-m-d H:i:s'))
                ->countAllResults()
        ];

        // Top Jobs by Applications
        $topJobs = $applicationModel
            ->select('jobs.title, jobs.id, COUNT(applications.id) as application_count')
            ->join('jobs', 'jobs.id = applications.job_id', 'left')
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

        $applicationModel = model('ApplicationModel');

        switch ($type) {
            case 'overview':
                $data = $this->getOverviewExportData();
                $filename = 'recruitment_overview_' . date('Y-m-d');
                break;

            case 'leaderboard':
                $data = $this->getLeaderboardExportData();
                $filename = 'candidate_leaderboard_' . date('Y-m-d');
                break;

            case 'funnel':
                $data = $this->getFunnelExportData();
                $filename = 'recruitment_funnel_' . date('Y-m-d');
                break;

            case 'detailed':
                $data = $this->getDetailedExportData();
                $filename = 'recruitment_detailed_' . date('Y-m-d');
                break;

            default:
                return redirect()->back()->with('error', 'Invalid export type');
        }

        // Generate Excel file
        $excelPath = $this->generateExcelReport($data, $filename);

        return $this->response->download($excelPath, null)->setFileName($filename . '.xlsx');
    }

    /**
     * Calculate Stage Time Analytics
     */
    private function calculateStageTimeAnalytics()
    {
        $db = \Config\Database::connect();

        // This is a simplified version - you might want to track stage transitions in a separate table
        $query = "
            SELECT 
                status,
                AVG(DATEDIFF(NOW(), applied_at)) as avg_days
            FROM applications
            WHERE status IN ('pending', 'ai_interview_scheduled', 'ai_interview_completed', 'shortlisted', 'hr_interview_scheduled')
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
    private function calculateConversionMetrics()
    {
        $applicationModel = model('ApplicationModel');

        $total = $applicationModel->countAll();
        if ($total == 0)
            return [];

        return [
            'application_to_ai_interview' => round(
                ($applicationModel->where('status', 'ai_interview_completed')->countAllResults() / $total) * 100,
                1
            ),
            'ai_interview_to_shortlist' => round(
                ($applicationModel->where('status', 'shortlisted')->countAllResults() /
                    max(1, $applicationModel->where('status', 'ai_interview_completed')->countAllResults())) * 100,
                1
            ),
            'shortlist_to_hr_interview' => round(
                ($applicationModel->where('status', 'hr_interview_scheduled')->countAllResults() /
                    max(1, $applicationModel->where('status', 'shortlisted')->countAllResults())) * 100,
                1
            ),
            'hr_interview_to_selection' => round(
                ($applicationModel->where('status', 'selected')->countAllResults() /
                    max(1, $applicationModel->where('status', 'hr_interview_completed')->countAllResults())) * 100,
                1
            ),
            'overall_conversion' => round(
                ($applicationModel->where('status', 'selected')->countAllResults() / $total) * 100,
                1
            )
        ];
    }

    /**
     * Get Monthly Trends
     */
    private function getMonthlyTrends()
    {
        $db = \Config\Database::connect();

        $query = "
            SELECT 
                DATE_FORMAT(applied_at, '%Y-%m') as month,
                COUNT(*) as total_applications,
                SUM(CASE WHEN status = 'selected' THEN 1 ELSE 0 END) as selected,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            FROM applications
            WHERE applied_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
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
    private function getOverviewExportData()
    {
        $applicationModel = model('ApplicationModel');

        return [
            'Summary' => $this->getSummarySheet(),
            'Applications' => $this->getApplicationsSheet(),
            'Job Statistics' => $this->getJobStatsSheet()
        ];
    }

    /**
     * Get Leaderboard Export Data
     */
    private function getLeaderboardExportData()
    {
        $applicationModel = model('ApplicationModel');

        $candidates = $applicationModel
            ->select('applications.*, users.name, users.email, jobs.title as job_title, jobs.required_skills as required_skills,
                    interview_sessions.technical_score,
                    interview_sessions.communication_score,
                    interview_sessions.overall_rating')
            ->join('users', 'users.id = applications.candidate_id', 'left')
            ->join('jobs', 'jobs.id = applications.job_id', 'left')
            ->join('interview_sessions', 'interview_sessions.application_id = applications.id', 'left')
            ->where('interview_sessions.overall_rating IS NOT NULL')
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
    private function getFunnelExportData()
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

        $total = $applicationModel->countAll();

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
    private function getDetailedExportData()
    {
        $applicationModel = model('ApplicationModel');

        $applications = $applicationModel
            ->select('applications.*, users.name, users.email, jobs.title as job_title,
            interview_sessions.overall_rating')
            ->join('users', 'users.id = applications.candidate_id', 'left')
            ->join('jobs', 'jobs.id = applications.job_id', 'left')
            ->join('interview_sessions', 'interview_sessions.application_id = applications.id', 'left')
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
     * Helper Methods
     */
    private function getSummarySheet()
    {
        $applicationModel = model('ApplicationModel');

        return [
            ['Metric', 'Value'],
            ['Total Applications', $applicationModel->countAll()],
            ['Active Applications', $applicationModel->whereIn('status', ['pending', 'ai_interview_scheduled', 'shortlisted'])->countAllResults()],
            ['Completed Interviews', $applicationModel->where('status', 'ai_interview_completed')->countAllResults()],
            ['Selected Candidates', $applicationModel->where('status', 'selected')->countAllResults()],
            ['Rejected Candidates', $applicationModel->where('status', 'rejected')->countAllResults()],
            ['Generated On', date('Y-m-d H:i:s')]
        ];
    }

    private function getApplicationsSheet()
    {
        $applicationModel = model('ApplicationModel');

        $apps = $applicationModel
            ->select('applications.id, users.name, jobs.title as job, applications.status, applications.applied_at')
            ->join('users', 'users.id = applications.candidate_id', 'left')
            ->join('jobs', 'jobs.id = applications.job_id', 'left')
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

    private function getJobStatsSheet()
    {
        $db = \Config\Database::connect();

        $query = "
            SELECT 
                jobs.title,
                COUNT(applications.id) as total_applications,
                SUM(CASE WHEN applications.status = 'selected' THEN 1 ELSE 0 END) as selected,
                SUM(CASE WHEN applications.status = 'rejected' THEN 1 ELSE 0 END) as rejected
            FROM jobs
            LEFT JOIN applications ON applications.job_id = jobs.id
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

    private function getStageCount($stage)
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

        if ($stage === 'Total Applications') {
            return $applicationModel->countAll();
        }

        $status = $statusMap[$stage] ?? null;
        if ($status) {
            return $applicationModel->where('status', $status)->countAllResults();
        }

        return 0;
    }
}