<?php

namespace App\Models;

use CodeIgniter\Model;

class GithubAnalysisModel extends Model
{
    protected $table = 'candidate_github_analysis';

    protected $allowedFields = [
        'candidate_id',
        'repo_count',
        'languages',
        'commits',
        'github_score'
    ];
}
