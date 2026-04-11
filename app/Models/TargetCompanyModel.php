<?php

namespace App\Models;

use CodeIgniter\Model;

class TargetCompanyModel extends Model
{
    protected $table      = 'candidate_target_companies';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id', 'company_name', 'careers_platform', 'platform_slug'
    ];
    protected $useTimestamps = false;

public function getUserCompanies(int $userId): array
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getUserCompaniesWithJobs(int $userId, TargetCompanyJobService $service): array
    {
        $companies = $this->getUserCompanies($userId);
        $withJobs = [];
        foreach ($companies as $company) {
            $jobs = $service->fetchJobs($company['company_name'], 
                $company['careers_platform'] ?? '', 
                $company['platform_slug'] ?? '', 5);
            $withJobs[] = array_merge($company, [
                'recent_jobs' => $jobs,
                'jobs_count' => count($jobs)
            ]);
        }
        return $withJobs;
    }

    public function addCompany(int $userId, string $companyName, string $platform = '', string $slug = ''): bool
    {
        // Prevent duplicates
        $existing = $this->where('user_id', $userId)
                         ->where('company_name', $companyName)
                         ->first();
        if ($existing) {
            return false;
        }

        return $this->insert([
            'user_id'          => $userId,
            'company_name'     => $companyName,
            'careers_platform' => $platform ?: null,
            'platform_slug'    => $slug ?: null,
        ]) !== false;
    }

    public function removeCompany(int $userId, int $id): bool
    {
        return $this->where('user_id', $userId)->delete($id);
    }
}