<?php

namespace App\Models;

use CodeIgniter\Model;

class CareerTransitionModel extends Model
{
    protected $table = 'career_transitions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['candidate_id', 'current_role', 'target_role', 'skill_gaps', 'learning_roadmap', 'status', 'progress_percentage', 'updated_at'];
    protected $useTimestamps = false;

    public function getActiveTransition($candidateId)
    {
        return $this->where('candidate_id', $candidateId)->where('status', 'active')->first();
    }
}
