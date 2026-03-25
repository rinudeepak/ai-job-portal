<?php

namespace App\Models;

use CodeIgniter\Model;

class InterviewEvaluationLogModel extends Model
{
    protected $table = 'interview_evaluation_logs';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'interview_session_id',
        'status',
        'provider',
        'request_payload',
        'response_payload',
        'error_message',
        'started_at',
        'completed_at',
        'created_at',
        'updated_at',
    ];
}
