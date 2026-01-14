<?php  

namespace App\Models;

use CodeIgniter\Model;

class JobModel extends Model
{
    protected $table = 'jobs';
    protected $allowedFields = [
        'title',
        'description',
        'required_skills',
        'experience_level',
        'min_ai_cutoff_score',
        'status'
    ];
}
