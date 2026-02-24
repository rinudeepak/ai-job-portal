<?php 

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $allowedFields = [
        'name',
        'email',
        'preferred_language',
        'phone',
        'password',
        'role',
        'google_id',
        'company_name',
        'company_id',
        'email_verification_token',
        'email_verified_at',
        'phone_otp',
        'phone_otp_expires_at',
        'phone_verified_at',
        'resume_path',
        'profile_photo',
        'location',
        'bio'
    ];
}
