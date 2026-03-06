<?php

namespace App\Models;

use CodeIgniter\Model;

class RecruiterProfileModel extends Model
{
    protected $table = 'recruiter_profiles';
    protected $primaryKey = 'user_id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id',
        'full_name',
        'phone',
        'designation',
        'company_name_snapshot',
        'created_at',
        'updated_at',
    ];
}
