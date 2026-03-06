<?php

namespace App\Models;

use CodeIgniter\Model;

class CandidateProfileModel extends Model
{
    protected $table = 'candidate_profiles';
    protected $primaryKey = 'user_id';
    protected $useAutoIncrement = false;
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id',
        'headline',
        'location',
        'bio',
        'resume_path',
        'profile_photo',
        'key_skills',
        'preferred_locations',
        'current_salary',
        'expected_salary',
        'notice_period',
        'created_at',
        'updated_at',
    ];
}
