<?php

namespace App\Models;

use CodeIgniter\Model;

class JobAlertModel extends Model
{
    protected $table = 'job_alerts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'candidate_id',
        'role_keywords',
        'location_keywords',
        'skills_keywords',
        'salary_min',
        'salary_max',
        'notify_email',
        'notify_in_app',
        'is_active',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false;
}

