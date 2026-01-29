<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicationModel extends Model
{
    protected $table = 'applications';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'job_id',
        'candidate_id',
        'status',
        'interview_slot',
        'ai_interview_id',
        'booking_id',
        'applied_at'
    ];
}
