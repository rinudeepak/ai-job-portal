<?php

namespace App\Models;

use CodeIgniter\Model;

class InterviewSessionModel extends Model
{
    protected $table = 'interview_sessions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'application_id',
        'job_id',
        'resume_version_id',
        'session_id',
        'position',
        'conversation_history',
        'integrity_events',
        'integrity_flags',
        'integrity_warning_count',
        'tab_switch_count',
        'hidden_duration_seconds',
        'reconnect_count',
        'last_integrity_ping_at',
        'last_resume_at',
        'turn',
        'max_turns',
        'status',
        'evaluation_data',
        'section_scores',
        'strengths',
        'concerns',
        'recommendation_summary',
        'evaluation_version',
        'technical_score',
        'communication_score',
        'problem_solving_score',
        'adaptability_score',
        'enthusiasm_score',
        'overall_rating',
        'round1_score',
        'round2_score',
        'round1_answered',
        'round1_total_questions',
        'interview_total_seconds',
        'ai_decision',
        'created_at',
        'updated_at',
        'completed_at',
    ];

    public function findLatestByApplication(int $applicationId): ?array
    {
        return $this->where('application_id', $applicationId)
            ->orderBy('id', 'DESC')
            ->first() ?: null;
    }
}
