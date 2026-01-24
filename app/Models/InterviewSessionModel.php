<?php

namespace App\Models;

use CodeIgniter\Model;

class InterviewSessionModel extends Model
{
    protected $table = 'interview_sessions';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'user_id',
        'application_id',
        'session_id',
        'position',
        'conversation_history',
        'turn',
        'max_turns',
        'status',
        'evaluation_data',
        'technical_score',
        'communication_score',
        'problem_solving_score',
        'adaptability_score',
        'enthusiasm_score',
        'overall_rating',
        'ai_decision',
        'created_at',
        'updated_at',
        'completed_at'
    ];

    protected $useTimestamps = false;

    protected $validationRules = [
        'user_id' => 'required|integer',
        'session_id' => 'required|max_length[100]',
        'position' => 'required|max_length[255]',
        'turn' => 'integer',
        'max_turns' => 'integer'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required'
        ],
        'position' => [
            'required' => 'Position is required'
        ]
    ];

    /**
     * Get active interview for user
     */
    public function getActiveInterview(int $userId)
    {
        return $this->where('user_id', $userId)
            ->where('status', 'active')
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    /**
     * Get all interviews for a user
     */
    public function getUserInterviews(int $userId, int $limit = 10)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    /**
     * Get interview statistics
     */
    public function getInterviewStats(int $userId): array
    {
        $total = $this->where('user_id', $userId)->countAllResults();
        $completed = $this->where('user_id', $userId)
            ->where('status', 'evaluated')
            ->countAllResults();
        $qualified = $this->where('user_id', $userId)
            ->where('ai_decision', 'qualified')
            ->countAllResults();

        $avgScore = $this->selectAvg('overall_rating')
            ->where('user_id', $userId)
            ->where('overall_rating IS NOT NULL')
            ->first();

        return [
            'total_interviews' => $total,
            'completed_interviews' => $completed,
            'qualified_count' => $qualified,
            'average_score' => round($avgScore['overall_rating'] ?? 0, 2),
            'success_rate' => $completed > 0 ? round(($qualified / $completed) * 100, 2) : 0
        ];
    }

    /**
     * Calculate time in stage
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
     * Get current stage
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

}