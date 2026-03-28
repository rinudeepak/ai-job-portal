<?php

namespace App\Models;

use CodeIgniter\Model;

class AiInterviewQuestionBankModel extends Model
{
    protected $table = 'ai_interview_question_bank';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'role_key',
        'section_key',
        'question_type',
        'question_text',
        'options_json',
        'correct_answer',
        'difficulty',
        'is_active',
        'created_at',
        'updated_at',
    ];
}

