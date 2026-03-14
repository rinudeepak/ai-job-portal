<?php 

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $allowedFields = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'google_id',
        'company_id',
        'email_verification_token',
        'email_verified_at',
        'password_reset_token',
        'password_reset_expires_at',
        'phone_otp',
        'phone_otp_expires_at',
        'phone_verified_at',
        'onboarding_completed',
        'onboarding_step',
        'onboarding_completed_at',
    ];

    public function findCandidateWithProfile(int $userId): ?array
    {
        $row = $this->select(
            "users.*,
            candidate_profiles.location AS location,
            candidate_profiles.bio AS bio,
            candidate_profiles.gender AS gender,
            candidate_profiles.date_of_birth AS date_of_birth,
            candidate_profiles.resume_path AS resume_path,
            candidate_profiles.profile_photo AS profile_photo,
            candidate_profiles.headline AS resume_headline,
            candidate_profiles.key_skills AS key_skills,
            candidate_profiles.preferred_job_titles AS preferred_job_titles,
            candidate_profiles.preferred_locations AS preferred_locations,
            candidate_profiles.preferred_employment_type AS preferred_employment_type,
            candidate_profiles.current_salary AS current_salary,
            candidate_profiles.expected_salary AS expected_salary,
            candidate_profiles.notice_period AS notice_period,
            candidate_profiles.allow_public_recruiter_visibility AS allow_public_recruiter_visibility,
            candidate_profiles.job_alerts_enabled AS job_alerts_enabled,
            candidate_profiles.job_alert_notify_in_app AS job_alert_notify_in_app,
            candidate_profiles.job_alert_notify_email AS job_alert_notify_email,
            candidate_profiles.is_fresher_candidate AS is_fresher_candidate"
        )
            ->join('candidate_profiles', 'candidate_profiles.user_id = users.id', 'left')
            ->where('users.id', $userId)
            ->where('users.role', 'candidate')
            ->first();

        return $row ?: null;
    }

    public function findRecruiterWithProfile(int $userId): ?array
    {
        $row = $this->select(
            "users.*,
            recruiter_profiles.full_name AS recruiter_full_name,
            recruiter_profiles.phone AS recruiter_phone,
            recruiter_profiles.designation AS recruiter_designation,
            COALESCE(companies.name, recruiter_profiles.company_name_snapshot) AS company_name"
        )
            ->join('recruiter_profiles', 'recruiter_profiles.user_id = users.id', 'left')
            ->join('companies', 'companies.id = users.company_id', 'left')
            ->where('users.id', $userId)
            ->where('users.role', 'recruiter')
            ->first();

        return $row ?: null;
    }

    public function upsertCandidateProfile(int $userId, array $data): bool
    {
        $db = \Config\Database::connect();
        if (!$db->tableExists('candidate_profiles')) {
            return true;
        }

        $profileModel = new CandidateProfileModel();
        $existing = $profileModel->find($userId);
        $payload = $this->filterCandidateProfileData($data);
        if (empty($payload)) {
            return true;
        }

        $now = date('Y-m-d H:i:s');
        if ($existing) {
            $payload['updated_at'] = $now;
            return (bool) $profileModel->update($userId, $payload);
        }

        $payload['user_id'] = $userId;
        $payload['created_at'] = $now;
        $payload['updated_at'] = $now;
        return (bool) $profileModel->insert($payload);
    }

    public function upsertRecruiterProfile(int $userId, array $data): bool
    {
        $db = \Config\Database::connect();
        if (!$db->tableExists('recruiter_profiles')) {
            return true;
        }

        $profileModel = new RecruiterProfileModel();
        $existing = $profileModel->find($userId);
        $payload = $this->filterRecruiterProfileData($data);
        if (empty($payload)) {
            return true;
        }

        $now = date('Y-m-d H:i:s');
        if ($existing) {
            $payload['updated_at'] = $now;
            return (bool) $profileModel->update($userId, $payload);
        }

        $payload['user_id'] = $userId;
        $payload['created_at'] = $now;
        $payload['updated_at'] = $now;
        return (bool) $profileModel->insert($payload);
    }

    private function filterCandidateProfileData(array $data): array
    {
        $map = [
            'resume_headline' => 'headline',
            'location' => 'location',
            'bio' => 'bio',
            'gender' => 'gender',
            'date_of_birth' => 'date_of_birth',
            'resume_path' => 'resume_path',
            'profile_photo' => 'profile_photo',
            'key_skills' => 'key_skills',
            'preferred_job_titles' => 'preferred_job_titles',
            'preferred_locations' => 'preferred_locations',
            'preferred_employment_type' => 'preferred_employment_type',
            'current_salary' => 'current_salary',
            'expected_salary' => 'expected_salary',
            'notice_period' => 'notice_period',
            'allow_public_recruiter_visibility' => 'allow_public_recruiter_visibility',
            'job_alerts_enabled' => 'job_alerts_enabled',
            'job_alert_notify_in_app' => 'job_alert_notify_in_app',
            'job_alert_notify_email' => 'job_alert_notify_email',
            'is_fresher_candidate' => 'is_fresher_candidate',
        ];

        $payload = [];
        foreach ($map as $source => $target) {
            if (array_key_exists($source, $data)) {
                $payload[$target] = $data[$source];
            }
        }

        return $payload;
    }

    private function filterRecruiterProfileData(array $data): array
    {
        $map = [
            'name' => 'full_name',
            'phone' => 'phone',
            'designation' => 'designation',
            'company_name' => 'company_name_snapshot',
            'company_name_snapshot' => 'company_name_snapshot',
        ];

        $payload = [];
        foreach ($map as $source => $target) {
            if (array_key_exists($source, $data)) {
                $payload[$target] = $data[$source];
            }
        }

        return $payload;
    }
}
