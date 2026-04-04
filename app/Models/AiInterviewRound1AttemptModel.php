<?php

namespace App\Models;

use CodeIgniter\Model;

class AiInterviewRound1AttemptModel extends Model
{
    protected $table = 'ai_interview_round1_attempts';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'interview_session_id',
        'application_id',
        'candidate_id',
        'question_bank_id',
        'section_key',
        'question_type',
        'question_text',
        'selected_answer',
        'correct_answer',
        'is_correct',
        'score',
        'max_score',
        'client_context',
        'integrity_flags',
        'paste_event_count',
        'pasted_character_count',
        'copy_paste_detected',
        'large_insert_count',
        'large_insert_character_count',
        'large_insert_detected',
        'answered_at',
        'created_at',
        'updated_at',
    ];

    public function findBySession(int $sessionId): array
    {
        return $this->where('interview_session_id', $sessionId)
            ->orderBy('id', 'ASC')
            ->findAll();
    }
}
