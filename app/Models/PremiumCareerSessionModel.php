<?php

namespace App\Models;

use CodeIgniter\Model;

class PremiumCareerSessionModel extends Model
{
    protected $table = 'premium_career_sessions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'session_type', 'current_role', 'target_role', 'timeline',
        'ai_analysis', 'action_plan', 'progress_tracking', 'status'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getUserActiveSessions($userId)
    {
        return $this->where('user_id', $userId)
                   ->where('status', 'active')
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    public function updateProgress($sessionId, $progressData)
    {
        return $this->update($sessionId, [
            'progress_tracking' => json_encode($progressData)
        ]);
    }
}