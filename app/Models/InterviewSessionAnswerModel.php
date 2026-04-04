<?php

namespace App\Models;

use CodeIgniter\Model;

class InterviewSessionAnswerModel extends Model
{
    protected $table = 'interview_session_answers';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'interview_session_id',
        'application_id',
        'candidate_id',
        'section_key',
        'question_index',
        'question_text',
        'answer_type',
        'answer_variant',
        'parent_question_index',
        'video_path',
        'audio_path',
        'transcript',
        'duration_seconds',
        'adaptive_level',
        'followup_type',
        'followup_prompt',
        'followup_trigger_score',
        'client_context',
        'integrity_flags',
        'tab_switch_count',
        'hidden_duration_seconds',
        'recording_health',
        'recording_metrics',
        'ai_score',
        'ai_feedback',
        'started_at',
        'submitted_at',
        'created_at',
        'updated_at',
    ];

    public function findBySession(int $sessionId): array
    {
        return $this->where('interview_session_id', $sessionId)
            ->orderBy('section_key', 'ASC')
            ->orderBy('question_index', 'ASC')
            ->findAll();
    }
}
