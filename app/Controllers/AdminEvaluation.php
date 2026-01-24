<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AdminEvaluation extends BaseController
{
    protected $interviewModel;
    protected $userModel;

    public function __construct()
    {
        $this->interviewModel = model('InterviewSessionModel');
        $this->userModel = model('UserModel');
    }

    /**
     * Display evaluation list
     */
    public function index()
    {
        // Get filter parameters
        $status = $this->request->getGet('status');
        $decision = $this->request->getGet('decision');
        $search = $this->request->getGet('search');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        // Build query
        $builder = $this->interviewModel
            ->select('interview_sessions.*, users.name as candidate_name, users.email, users.phone')
            ->join('users', 'users.id = interview_sessions.user_id', 'left')
            ->orderBy('interview_sessions.created_at', 'DESC');

        // Apply filters
        if ($status) {
            $builder->where('interview_sessions.status', $status);
        }

        if ($decision) {
            $builder->where('interview_sessions.ai_decision', $decision);
        }

        if ($search) {
            $builder->groupStart()
                ->like('users.name', $search)
                ->orLike('users.email', $search)
                ->orLike('interview_sessions.position', $search)
                ->groupEnd();
        }

        if ($dateFrom) {
            $builder->where('interview_sessions.created_at >=', $dateFrom . ' 00:00:00');
        }

        if ($dateTo) {
            $builder->where('interview_sessions.created_at <=', $dateTo . ' 23:59:59');
        }

        // Pagination
        $perPage = 20;
        $evaluations = $builder->paginate($perPage);
        $pager = $this->interviewModel->pager;

        // Calculate statistics
        $stats = $this->getStatistics();

        return view('admin/evaluations/index', [
            'evaluations' => $evaluations,
            'pager' => $pager,
            'stats' => $stats,
            'filters' => [
                'status' => $status,
                'decision' => $decision,
                'search' => $search,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]
        ]);
    }

    /**
     * View detailed evaluation
     */
    public function view($id)
    {
        $interview = $this->interviewModel
            ->select('interview_sessions.*, users.name as candidate_name, users.email, users.phone, users.github_username')
            ->join('users', 'users.id = interview_sessions.user_id', 'left')
            ->find($id);

        if (!$interview) {
            return redirect()->to('/admin/evaluations')->with('error', 'Interview not found');
        }

        $evaluationData = json_decode($interview['evaluation_data'] ?? '{}', true);
        $conversationHistory = json_decode($interview['conversation_history'] ?? '[]', true);

        return view('admin/evaluations/detail', [
            'interview' => $interview,
            'evaluation' => $evaluationData,
            'conversation' => $conversationHistory
        ]);
    }

    /**
     * Export evaluations to Excel
     */
    public function exportExcel()
    {
        // Get filter parameters (same as index)
        $status = $this->request->getGet('status');
        $decision = $this->request->getGet('decision');
        $search = $this->request->getGet('search');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        // Build query
        $builder = $this->interviewModel
            ->select('interview_sessions.*, users.name as candidate_name, users.email, users.phone')
            ->join('users', 'users.id = interview_sessions.user_id', 'left')
            ->orderBy('interview_sessions.created_at', 'DESC');

        // Apply same filters
        if ($status) $builder->where('interview_sessions.status', $status);
        if ($decision) $builder->where('interview_sessions.ai_decision', $decision);
        if ($search) {
            $builder->groupStart()
                ->like('users.name', $search)
                ->orLike('users.email', $search)
                ->orLike('interview_sessions.position', $search)
                ->groupEnd();
        }
        if ($dateFrom) $builder->where('interview_sessions.created_at >=', $dateFrom . ' 00:00:00');
        if ($dateTo) $builder->where('interview_sessions.created_at <=', $dateTo . ' 23:59:59');

        $evaluations = $builder->findAll();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set document properties
        $spreadsheet->getProperties()
            ->setCreator('AI Job Portal')
            ->setTitle('Interview Evaluations')
            ->setSubject('Candidate Interview Results')
            ->setDescription('AI-powered interview evaluation results');

        // Header styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 12
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ];

        // Set headers
        $headers = [
            'A1' => 'ID',
            'B1' => 'Candidate Name',
            'C1' => 'Email',
            'D1' => 'Phone',
            'E1' => 'Position Applied',
            'F1' => 'Technical Score',
            'G1' => 'Communication Score',
            'H1' => 'Problem Solving Score',
            'I1' => 'Adaptability Score',
            'J1' => 'Enthusiasm Score',
            'K1' => 'Overall Rating',
            'L1' => 'AI Decision',
            'M1' => 'Status',
            'N1' => 'Current Stage',
            'O1' => 'Time in Stage',
            'P1' => 'Interview Date',
            'Q1' => 'Completed Date'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Apply header style
        $sheet->getStyle('A1:Q1')->applyFromArray($headerStyle);

        // Auto-size columns
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Populate data
        $row = 2;
        foreach ($evaluations as $eval) {
            $timeInStage = $this->calculateTimeInStage($eval);
            
            $sheet->setCellValue('A' . $row, $eval['id']);
            $sheet->setCellValue('B' . $row, $eval['candidate_name']);
            $sheet->setCellValue('C' . $row, $eval['email']);
            $sheet->setCellValue('D' . $row, $eval['phone']);
            $sheet->setCellValue('E' . $row, $eval['position']);
            $sheet->setCellValue('F' . $row, $eval['technical_score'] ?? 'N/A');
            $sheet->setCellValue('G' . $row, $eval['communication_score'] ?? 'N/A');
            $sheet->setCellValue('H' . $row, $eval['problem_solving_score'] ?? 'N/A');
            $sheet->setCellValue('I' . $row, $eval['adaptability_score'] ?? 'N/A');
            $sheet->setCellValue('J' . $row, $eval['enthusiasm_score'] ?? 'N/A');
            $sheet->setCellValue('K' . $row, $eval['overall_rating'] ?? 'N/A');
            $sheet->setCellValue('L' . $row, strtoupper($eval['ai_decision'] ?? 'Pending'));
            $sheet->setCellValue('M' . $row, ucfirst($eval['status']));
            $sheet->setCellValue('N' . $row, $this->getCurrentStage($eval));
            $sheet->setCellValue('O' . $row, $timeInStage);
            $sheet->setCellValue('P' . $row, date('Y-m-d H:i:s', strtotime($eval['created_at'])));
            $sheet->setCellValue('Q' . $row, $eval['completed_at'] ? date('Y-m-d H:i:s', strtotime($eval['completed_at'])) : 'N/A');

            // Color code based on decision
            if ($eval['ai_decision'] === 'qualified') {
                $sheet->getStyle('L' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('C6EFCE');
            } elseif ($eval['ai_decision'] === 'rejected') {
                $sheet->getStyle('L' . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFC7CE');
            }

            $row++;
        }

        // Add borders to all data
        $sheet->getStyle('A1:Q' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ]
        ]);

        // Generate filename
        $filename = 'interview_evaluations_' . date('Y-m-d_His') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Write file to output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Get statistics
     */
    private function getStatistics(): array
    {
        $db = \Config\Database::connect();

        $totalInterviews = $this->interviewModel->countAll();
        
        $completed = $this->interviewModel
            ->where('status', 'evaluated')
            ->countAllResults();
        
        $qualified = $this->interviewModel
            ->where('ai_decision', 'qualified')
            ->countAllResults();
        
        $rejected = $this->interviewModel
            ->where('ai_decision', 'rejected')
            ->countAllResults();
        
        $avgTechnical = $db->query(
            "SELECT AVG(technical_score) as avg FROM interview_sessions WHERE technical_score IS NOT NULL"
        )->getRow()->avg ?? 0;
        
        $avgCommunication = $db->query(
            "SELECT AVG(communication_score) as avg FROM interview_sessions WHERE communication_score IS NOT NULL"
        )->getRow()->avg ?? 0;
        
        $avgOverall = $db->query(
            "SELECT AVG(overall_rating) as avg FROM interview_sessions WHERE overall_rating IS NOT NULL"
        )->getRow()->avg ?? 0;

        return [
            'total_interviews' => $totalInterviews,
            'completed' => $completed,
            'qualified' => $qualified,
            'rejected' => $rejected,
            'in_progress' => $totalInterviews - $completed,
            'qualification_rate' => $completed > 0 ? round(($qualified / $completed) * 100, 1) : 0,
            'avg_technical' => round($avgTechnical, 1),
            'avg_communication' => round($avgCommunication, 1),
            'avg_overall' => round($avgOverall, 1)
        ];
    }

    /**
     * Calculate time spent in current stage
     */
    private function calculateTimeInStage($interview): string
    {
        $now = time();
        $statusDate = strtotime($interview['updated_at'] ?? $interview['created_at']);
        $diff = $now - $statusDate;

        $days = floor($diff / 86400);
        $hours = floor(($diff % 86400) / 3600);
        $minutes = floor(($diff % 3600) / 60);

        if ($days > 0) {
            return "{$days}d {$hours}h";
        } elseif ($hours > 0) {
            return "{$hours}h {$minutes}m";
        } else {
            return "{$minutes}m";
        }
    }

    /**
     * Get current stage name
     */
    private function getCurrentStage($interview): string
    {
        $status = $interview['status'];
        
        $stages = [
            'active' => 'In Interview (Turn ' . $interview['turn'] . '/' . $interview['max_turns'] . ')',
            'completed' => 'Awaiting Evaluation',
            'evaluated' => 'Evaluation Complete'
        ];

        return $stages[$status] ?? 'Unknown';
    }

    /**
     * Update interview status manually
     */
    public function updateStatus($id)
    {
        $newStatus = $this->request->getPost('status');
        $notes = $this->request->getPost('notes');

        $this->interviewModel->update($id, [
            'status' => $newStatus,
            'admin_notes' => $notes,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'Status updated successfully');
    }
}
