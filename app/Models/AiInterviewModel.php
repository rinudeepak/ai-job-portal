<?php

namespace App\Models;

use CodeIgniter\Model;

class AiInterviewModel extends Model
{
    protected $table = 'ai_interviews';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'candidate_id', 'job_id',
        'questions', 'answers', 'technical_score', 'communication_score',
        'overall_rating', 'ai_decision', 'ai_feedback', 'status',
        'started_at', 'completed_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $casts = [
        'skills_tested' => 'json',
        'github_languages' => 'json',
        'questions' => 'json',
        'answers' => 'json',
        'ai_feedback' => 'json',
    ];
}
