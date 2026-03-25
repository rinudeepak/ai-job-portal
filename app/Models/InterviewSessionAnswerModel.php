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
        'video_path',
        'audio_path',
        'transcript',
        'duration_seconds',
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
