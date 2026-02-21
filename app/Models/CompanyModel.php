<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'name',
        'logo',
        'website',
        'industry',
        'size',
        'hq',
        'branches',
        'short_description',
        'what_we_do',
        'mission_values',
        'contact_email',
        'contact_phone',
        'contact_public',
    ];
}

