<?php

namespace App\Models;

use CodeIgniter\Model;

class JobModel extends Model
{
    public const AI_POLICY_OFF = 'OFF';
    public const AI_POLICY_OPTIONAL = 'OPTIONAL';
    public const AI_POLICY_REQUIRED_SOFT = 'REQUIRED_SOFT';
    public const AI_POLICY_REQUIRED_HARD = 'REQUIRED_HARD';
    public const EXTERNAL_SYSTEM_RECRUITER_EMAIL = 'external.jobs@system.local';
    public const EXTERNAL_SYSTEM_RECRUITER_NAME = 'External Jobs';

    protected $table = 'jobs';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'title',
        'category',
        'recruiter_id',
        'company_id',
        'is_external',
        'external_source',
        'external_apply_url',
        'company',
        'location',
        'description',
        'required_skills',
        'experience_level',
        'min_ai_cutoff_score',
        'ai_interview_policy',
        'openings',
        'status',
        'employment_type',
        'salary_range',
        'application_deadline',
    ];

    public static function isExternalJob(array $job): bool
    {
        return (int) ($job['is_external'] ?? 0) === 1;
    }

    public static function normalizeAiPolicy(?string $policy): string
    {
        $value = strtoupper(trim((string) $policy));
        $allowed = [
            self::AI_POLICY_OFF,
            self::AI_POLICY_OPTIONAL,
            self::AI_POLICY_REQUIRED_SOFT,
            self::AI_POLICY_REQUIRED_HARD,
        ];

        return in_array($value, $allowed, true) ? $value : self::AI_POLICY_REQUIRED_HARD;
    }

    public function getTotalOpenJobs()
    {
        return $this->where('status', 'open')->countAllResults();
    }

    /**
     * Get candidate's application behavior profile.
     */
    public function getCandidateBehaviorProfile($candidateId)
    {
        $db = \Config\Database::connect();

        $topCategories = $db->query("\n            SELECT j.category, COUNT(*) as apply_count\n            FROM applications a\n            JOIN jobs j ON a.job_id = j.id\n            WHERE a.candidate_id = ?\n            GROUP BY j.category\n            ORDER BY apply_count DESC\n            LIMIT 5\n        ", [$candidateId])->getResultArray();

        $topExperienceLevels = $db->query("\n            SELECT j.experience_level, COUNT(*) as apply_count\n            FROM applications a\n            JOIN jobs j ON a.job_id = j.id\n            WHERE a.candidate_id = ?\n            GROUP BY j.experience_level\n            ORDER BY apply_count DESC\n            LIMIT 3\n        ", [$candidateId])->getResultArray();

        $topEmploymentTypes = $db->query("\n            SELECT j.employment_type, COUNT(*) as apply_count\n            FROM applications a\n            JOIN jobs j ON a.job_id = j.id\n            WHERE a.candidate_id = ?\n            GROUP BY j.employment_type\n            ORDER BY apply_count DESC\n            LIMIT 3\n        ", [$candidateId])->getResultArray();

        $topLocations = $db->query("\n            SELECT j.location, COUNT(*) as apply_count\n            FROM applications a\n            JOIN jobs j ON a.job_id = j.id\n            WHERE a.candidate_id = ?\n            GROUP BY j.location\n            ORDER BY apply_count DESC\n            LIMIT 5\n        ", [$candidateId])->getResultArray();

        $appliedSkills = $db->query("\n            SELECT j.required_skills\n            FROM applications a\n            JOIN jobs j ON a.job_id = j.id\n            WHERE a.candidate_id = ?\n            ORDER BY a.applied_at DESC\n            LIMIT 20\n        ", [$candidateId])->getResultArray();

        $skillFrequency = [];
        foreach ($appliedSkills as $row) {
            $skills = array_map('trim', explode(',', (string) ($row['required_skills'] ?? '')));
            foreach ($skills as $skill) {
                if ($skill !== '') {
                    $normalized = strtolower($skill);
                    $skillFrequency[$normalized] = ($skillFrequency[$normalized] ?? 0) + 1;
                }
            }
        }
        arsort($skillFrequency);

        return [
            'top_categories' => $topCategories,
            'top_experience_levels' => $topExperienceLevels,
            'top_employment_types' => $topEmploymentTypes,
            'top_locations' => $topLocations,
            'applied_skill_frequency' => array_slice($skillFrequency, 0, 15, true),
        ];
    }

    /**
     * Normalized matching (0-100):
     * skills 60%, experience 20%, location 10%, employment type 10%.
     */
    public function getSuggestedJobsBasic($candidateId, $limit = 10)
    {
        $skillsModel = new \App\Models\CandidateSkillsModel();
        $userModel = new \App\Models\UserModel();
        $db = \Config\Database::connect();

        $skillRow = $skillsModel->where('candidate_id', $candidateId)->first();
        $candidateSkills = $this->tokenizeCsv((string) ($skillRow['skill_name'] ?? ''));
        if (empty($candidateSkills)) {
            return [];
        }

        $profile = $userModel->findCandidateWithProfile((int) $candidateId) ?? [];
        $preferredLocations = $this->tokenizeCsv((string) ($profile['preferred_locations'] ?? ''));

        $experienceRow = $db->query(
            "SELECT SUM(TIMESTAMPDIFF(MONTH, start_date, COALESCE(NULLIF(end_date, ''), CURDATE()))) AS total_experience_months\n             FROM work_experiences\n             WHERE user_id = ?",
            [(int) $candidateId]
        )->getRowArray();
        $candidateMonths = (int) ($experienceRow['total_experience_months'] ?? 0);

        $preferredEmploymentTypes = $this->tokenizeCsv((string) ($profile['preferred_employment_type'] ?? ''));

        $jobs = $this->where('status', 'open')
            ->whereNotIn('id', static function ($builder) use ($candidateId) {
                return $builder->select('job_id')->from('applications')->where('candidate_id', (int) $candidateId);
            })
            ->orderBy('created_at', 'DESC')
            ->findAll(200);

        $ranked = [];
        foreach ($jobs as $job) {
            $requiredSkills = $this->tokenizeCsv((string) ($job['required_skills'] ?? ''));
            if (empty($requiredSkills)) {
                continue;
            }

            $matchedSkills = $this->countTokenOverlap($requiredSkills, $candidateSkills);
            if ($matchedSkills <= 0) {
                continue;
            }

            $skillCoverage = $matchedSkills / max(1, count($requiredSkills));
            $skillScore = $skillCoverage * 60.0;

            $requiredMonths = $this->extractRequiredExperienceMonths((string) ($job['experience_level'] ?? ''));
            if ($requiredMonths === null || $requiredMonths <= 0) {
                $experienceFit = 1.0;
            } else {
                $experienceFit = min(1.0, $candidateMonths / max(1, $requiredMonths));
            }
            $experienceScore = $experienceFit * 20.0;

            $locationScore = 0.0;
            $jobLocation = strtolower(trim((string) ($job['location'] ?? '')));
            if ($jobLocation === '' || empty($preferredLocations)) {
                $locationScore = 5.0;
            } else {
                foreach ($preferredLocations as $preferredLocation) {
                    if ($preferredLocation !== '' && (str_contains($jobLocation, $preferredLocation) || str_contains($preferredLocation, $jobLocation))) {
                        $locationScore = 10.0;
                        break;
                    }
                }
            }

            $employmentScore = 0.0;
            $jobEmploymentType = strtolower(trim((string) ($job['employment_type'] ?? '')));
            if ($jobEmploymentType === '' || empty($preferredEmploymentTypes)) {
                $employmentScore = 5.0;
            } else {
                $employmentScore = in_array($jobEmploymentType, $preferredEmploymentTypes, true) ? 10.0 : 0.0;
            }

            $matchPercent = (float) round(max(0, min(100, $skillScore + $experienceScore + $locationScore + $employmentScore)), 1);
            if ($matchPercent <= 0) {
                continue;
            }

            $job['match_score'] = $matchPercent;
            $job['match_reason'] = 'Matched ' . $matchedSkills . '/' . count($requiredSkills) . ' required skills';
            $ranked[] = $job;
        }

        usort($ranked, static fn (array $a, array $b): int => ((float) ($b['match_score'] ?? 0.0)) <=> ((float) ($a['match_score'] ?? 0.0)));
        return array_slice($ranked, 0, $limit);
    }

    public function upsertExternalJob(int $companyId, string $companyName, array $job, string $sourceUrl): int
    {
        $title    = trim((string) ($job['title'] ?? ''));
        $applyUrl = trim((string) ($job['apply_url'] ?? ''));
        $location = trim((string) ($job['location'] ?? ''));
        $department = trim((string) ($job['department'] ?? ''));
        $description = trim((string) ($job['description'] ?? ($job['summary'] ?? '')));

        if ($title === '' || $applyUrl === '' || !filter_var($applyUrl, FILTER_VALIDATE_URL)) {
            return 0;
        }

        // Reuse the same system recruiter as ExternalJobIngestionService
        $recruiterId = $this->getOrCreateSystemRecruiterId();
        if ($recruiterId <= 0) {
            return 0;
        }

        $payload = [
            'recruiter_id'       => $recruiterId,
            'company_id'         => $companyId > 0 ? $companyId : null,
            'company'            => $companyName,
            'title'              => $title,
            'category'           => $department !== '' ? $department : 'External',
            'location'           => $location !== '' ? $location : 'Not specified',
            'description'        => $description !== '' ? $description : $title,
            'employment_type'    => trim((string) ($job['employment_type'] ?? '')),
            'openings'           => 1,
            'status'             => 'open',
            'is_external'        => 1,
            'external_source'    => $sourceUrl,
            'external_apply_url' => $applyUrl,
            'ai_interview_policy'=> self::AI_POLICY_OFF,
            'min_ai_cutoff_score'=> 0,
        ];

        $existing = $this->where('external_apply_url', $applyUrl)->first();
        if (!$existing && $companyId > 0) {
            $existing = $this->where('company_id', $companyId)
                ->where('title', $title)
                ->where('location', $location)
                ->where('external_source', $sourceUrl)
                ->first();
        }

        if ($existing) {
            $this->update((int) $existing['id'], $payload);
            return (int) $existing['id'];
        }

        try {
            return (int) $this->insert($payload, true);
        } catch (\Throwable $e) {
            // Duplicate apply_url inserted by concurrent request
            $existing = $this->where('external_apply_url', $applyUrl)->first();
            if ($existing) {
                return (int) $existing['id'];
            }
            log_message('error', 'upsertExternalJob failed: ' . $e->getMessage());
            return 0;
        }
    }

    private function getOrCreateSystemRecruiterId(): int
    {
        $db  = \Config\Database::connect();
        $row = $db->table('users')
            ->where('email', self::EXTERNAL_SYSTEM_RECRUITER_EMAIL)
            ->where('role', 'recruiter')
            ->get()
            ->getRowArray();

        if (!empty($row['id'])) {
            return (int) $row['id'];
        }

        try {
            $secret = bin2hex(random_bytes(16));
        } catch (\Throwable $e) {
            $secret = uniqid('ext_', true);
        }

        $now = date('Y-m-d H:i:s');
        $db->table('users')->insert([
            'name'              => self::EXTERNAL_SYSTEM_RECRUITER_NAME,
            'email'             => self::EXTERNAL_SYSTEM_RECRUITER_EMAIL,
            'phone'             => '0000000000',
            'password'          => password_hash($secret, PASSWORD_DEFAULT),
            'role'              => 'recruiter',
            'email_verified_at' => $now,
            'phone_verified_at' => $now,
            'created_at'        => $now,
        ]);

        return (int) $db->insertID();
    }

    /** @return array<int, string> */
    private function tokenizeCsv(string $value): array
    {
        $parts = preg_split('/[,|\\/]+/', strtolower($value)) ?: [];
        $tokens = [];
        foreach ($parts as $part) {
            $token = trim($part);
            if ($token !== '') {
                $tokens[] = $token;
            }
        }

        return array_values(array_unique($tokens));
    }

    /** @param array<int, string> $a @param array<int, string> $b */
    private function countTokenOverlap(array $a, array $b): int
    {
        $matched = [];
        foreach ($a as $left) {
            foreach ($b as $right) {
                if ($left === $right) {
                    $matched[$left] = true;
                    break;
                }
                if (strlen($left) >= 4 && str_contains($right, $left)) {
                    $matched[$left] = true;
                    break;
                }
                if (strlen($right) >= 4 && str_contains($left, $right)) {
                    $matched[$left] = true;
                    break;
                }
            }
        }

        return count($matched);
    }

    private function extractRequiredExperienceMonths(string $experience): ?int
    {
        $value = strtolower(trim($experience));
        if ($value === '') {
            return null;
        }

        if (preg_match('/(\d+(?:\.\d+)?)\s*-\s*(\d+(?:\.\d+)?)/', $value, $matches)) {
            return (int) round(((float) $matches[1]) * 12);
        }

        if (preg_match('/(\d+(?:\.\d+)?)/', $value, $matches)) {
            return (int) round(((float) $matches[1]) * 12);
        }

        return null;
    }
}
