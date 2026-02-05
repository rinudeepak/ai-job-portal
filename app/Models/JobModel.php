<?php  

namespace App\Models;

use CodeIgniter\Model;

class JobModel extends Model
{
    protected $table = 'jobs';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'title',
        'recruiter_id',
        'company',
        'location',
        'description',
        'required_skills',
        'experience_level',
        'min_ai_cutoff_score',
        'openings',
        'status',
        'employment_type'
    ];
    // Count open jobs
    public function getTotalOpenJobs()
    {
        return $this->where('status', 'open')->countAllResults();
    }

}
