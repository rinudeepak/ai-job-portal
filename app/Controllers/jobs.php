<?php

namespace App\Controllers;

use App\Libraries\AiResumeCoach;
use App\Libraries\AiJobMatcher;
use App\Models\CompanyModel;
use App\Models\CandidateResumeVersionModel;
use App\Models\CandidateSkillsModel;
use App\Models\JobModel;
use App\Models\SavedJobModel;
use App\Models\UserModel;

class Jobs extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'candidate') {
            return redirect()->to(base_url('recruiter/dashboard'))->with('error', 'Access denied.');
        }

        $jobModel = new JobModel();
        $candidateId = (int) session()->get('user_id');

        $filters = [
            'search'           => $this->request->getGet('search'),
            'designation'      => $this->request->getGet('designation'),
            'company'          => $this->request->getGet('company'),
            'location'         => $this->request->getGet('location'),
            'category'         => $this->request->getGet('category'),
            'experience_level' => $this->request->getGet('experience_level'),
            'employment_type'  => $this->request->getGet('employment_type'),
            'work_mode'        => (string) ($this->request->getGet('work_mode') ?? $this->request->getGet('remote') ?? ''),
            'salary_range'     => (string) ($this->request->getGet('salary_range') ?? ''),
            'posted_within'    => $this->request->getGet('posted_within'),
            'skills_match'     => $this->request->getGet('skills_match'),
            'sort'             => $this->request->getGet('sort') ?: 'newest',
        ];

        $builder = $jobModel->where('status', 'open');

        if (!empty($filters['search'])) {
            $builder->groupStart()
                    ->like('title', $filters['search'])
                    ->orLike('company', $filters['search'])
                    ->orLike('required_skills', $filters['search'])
                    ->orLike('description', $filters['search'])
                    ->groupEnd();
        }

        if (!empty($filters['designation'])) {
            $builder->like('title', $filters['designation']);
        }

        if (!empty($filters['company'])) {
            $builder->like('company', $filters['company']);
        }

        if (!empty($filters['location'])) {
            $builder->like('location', $filters['location']);
        }

        if (!empty($filters['category'])) {
            $builder->like('TRIM(category)', trim((string) $filters['category']));
        }

        if (!empty($filters['experience_level'])) {
            if (is_array($filters['experience_level'])) {
                $builder->whereIn('experience_level', $filters['experience_level']);
            } else {
                $builder->where('experience_level', $filters['experience_level']);
            }
        }

        if (!empty($filters['employment_type'])) {
            if (is_array($filters['employment_type'])) {
                $builder->whereIn('employment_type', $filters['employment_type']);
            } else {
                $builder->where('employment_type', $filters['employment_type']);
            }
        }

        if (!empty($filters['work_mode'])) {
            $this->applyWorkModeFilter($builder, (string) $filters['work_mode']);
        }

        if (!empty($filters['salary_range'])) {
            $this->applySalaryRangeFilter($builder, (string) $filters['salary_range']);
        }

        if (!empty($filters['posted_within'])) {
            $days = (int) $filters['posted_within'];
            $builder->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        }

        if (!empty($filters['skills_match']) && $candidateId > 0) {
            $userSkills = $this->getUserSkills($candidateId);
            if (!empty($userSkills)) {
                $builder->groupStart();
                foreach ($userSkills as $skill) {
                    $builder->orLike('required_skills', $skill);
                }
                $builder->groupEnd();
            }
        }

        switch ($filters['sort']) {
            case 'newest':
                $builder->orderBy('created_at', 'DESC');
                break;
            case 'relevance':
                $builder->orderBy('title', 'ASC');
                break;
            case 'location':
                $builder->orderBy('location', 'ASC');
                break;
        }

        $jobs = $builder->paginate(perPage: 10);
        $pager = $jobModel->pager;
        $totalJobs = $pager->getTotal();

        $locations = $jobModel->select('location')
            ->where('status', 'open')
            ->where('location IS NOT NULL')
            ->where('location !=', '')
            ->groupBy('location')
            ->findAll();

        $experienceLevels = $jobModel->select('experience_level')
            ->where('status', 'open')
            ->groupBy('experience_level')
            ->findAll();

        $employmentTypes = $jobModel->select('employment_type')
            ->where('status', 'open')
            ->where('employment_type IS NOT NULL')
            ->where('employment_type !=', '')
            ->groupBy('employment_type')
            ->findAll();

        $categories = $jobModel->select('category')
            ->where('status', 'open')
            ->where('category IS NOT NULL')
            ->where('category !=', '')
            ->groupBy('category')
            ->findAll();

        $recommendationType = strtolower((string) $this->request->getGet('rec'));
        $allowedRecommendationTypes = ['applies', 'skills', 'preferences', 'ai'];
        if (!in_array($recommendationType, $allowedRecommendationTypes, true)) {
            $recommendationType = 'skills';
        }

        $filterContextKeys = [
            'search',
            'designation',
            'company',
            'location',
            'category',
            'experience_level',
            'employment_type',
            'work_mode',
            'salary_range',
            'posted_within',
            'skills_match',
        ];
        $showFilters = false;
        foreach ($filterContextKeys as $searchKey) {
            $value = $filters[$searchKey] ?? null;
            if (is_array($value) && !empty(array_filter($value, static fn ($v) => trim((string) $v) !== ''))) {
                $showFilters = true;
                break;
            }
            if (!is_array($value) && trim((string) $value) !== '') {
                $showFilters = true;
                break;
            }
        }
        $activeTab = $showFilters ? 'all' : 'recommended';

        $suggestedJobsByApplies = [];
        $suggestedJobsBySkills = [];
        $suggestedJobsByPreferences = [];
        $suggestedJobsByAi = [];
        $suggestedJobs = [];

        $candidateSkills = [];
        $candidateInterests = [];
        $behavior = [];

        if ($candidateId > 0 && !$showFilters) {
            $skillsModel = new \App\Models\CandidateSkillsModel();
            $interestsModel = new \App\Models\CandidateInterestsModel();

            $skillRow = $skillsModel->where('candidate_id', $candidateId)->first();
            if ($skillRow && !empty($skillRow['skill_name'])) {
                $candidateSkills = array_values(
                    array_filter(array_map('trim', explode(',', (string) $skillRow['skill_name'])))
                );
            }

            $interestRow = $interestsModel->where('candidate_id', $candidateId)->first();
            if ($interestRow && !empty($interestRow['interest'])) {
                $candidateInterests = array_values(
                    array_filter(array_map('trim', explode(',', (string) $interestRow['interest'])))
                );
            }

            $behavior = $jobModel->getCandidateBehaviorProfile($candidateId);

            $suggestedJobsBySkills = $jobModel->getSuggestedJobsBasic($candidateId, 20);
            $suggestedJobsByApplies = $this->rankJobsByApplicationBehavior($candidateId, $behavior, 20);
            $suggestedJobsByPreferences = $this->rankJobsByPreferences($candidateId, $behavior, $candidateInterests, 20);
            $aiPrimarySuggestions = (new AiJobMatcher())->generateSuggestions($candidateId, 20);
            $suggestedJobsByAi = $this->buildOtherRecommendations(
                $aiPrimarySuggestions,
                $suggestedJobsBySkills,
                $suggestedJobsByPreferences,
                20
            );

            $suggestedJobs = match ($recommendationType) {
                'applies' => $suggestedJobsByApplies,
                'preferences' => $suggestedJobsByPreferences,
                'ai' => $suggestedJobsByAi,
                default => $suggestedJobsBySkills,
            };
        }

        $companyIds = [];
        foreach ($jobs as $job) {
            $id = (int) ($job['company_id'] ?? 0);
            if ($id > 0) {
                $companyIds[] = $id;
            }
        }
        $recommendationSets = [
            $suggestedJobs,
            $suggestedJobsByApplies,
            $suggestedJobsBySkills,
            $suggestedJobsByPreferences,
            $suggestedJobsByAi,
        ];
        foreach ($recommendationSets as $jobSet) {
            foreach ($jobSet as $job) {
                $id = (int) ($job['company_id'] ?? 0);
                if ($id > 0) {
                    $companyIds[] = $id;
                }
            }
        }
        $companyIds = array_values(array_unique($companyIds));

        $companyLogoMap = [];
        if (!empty($companyIds)) {
            $companies = (new CompanyModel())
                ->select('id, logo')
                ->whereIn('id', $companyIds)
                ->findAll();
            foreach ($companies as $company) {
                $companyLogoMap[(int) $company['id']] = (string) ($company['logo'] ?? '');
            }
        }

        foreach ($jobs as $index => $job) {
            $id = (int) ($job['company_id'] ?? 0);
            $jobs[$index]['company_logo'] = $companyLogoMap[$id] ?? '';
        }

        $applyLogos = static function (array $jobSet) use ($companyLogoMap): array {
            foreach ($jobSet as $index => $job) {
                $id = (int) ($job['company_id'] ?? 0);
                $jobSet[$index]['company_logo'] = $companyLogoMap[$id] ?? '';
            }
            return $jobSet;
        };

        $suggestedJobs = $applyLogos($suggestedJobs);
        $suggestedJobsByApplies = $applyLogos($suggestedJobsByApplies);
        $suggestedJobsBySkills = $applyLogos($suggestedJobsBySkills);
        $suggestedJobsByPreferences = $applyLogos($suggestedJobsByPreferences);
        $suggestedJobsByAi = $applyLogos($suggestedJobsByAi);

        $savedJobIds = [];
        if ($candidateId > 0) {
            $displayJobIds = [];
            foreach ($jobs as $job) {
                $displayJobIds[] = (int) ($job['id'] ?? 0);
            }
            foreach ($recommendationSets as $jobSet) {
                foreach ($jobSet as $job) {
                    $displayJobIds[] = (int) ($job['id'] ?? 0);
                }
            }
            $displayJobIds = array_values(array_filter(array_unique($displayJobIds)));

            if (!empty($displayJobIds)) {
                $savedRows = (new SavedJobModel())
                    ->select('job_id')
                    ->where('candidate_id', $candidateId)
                    ->whereIn('job_id', $displayJobIds)
                    ->findAll();
                $savedJobIds = array_map('intval', array_column($savedRows, 'job_id'));
            }
        }

        return view('candidate/smart_jobs', [
            'jobs' => $jobs,
            'totalJobs' => $totalJobs,
            'filters' => $filters,
            'locations' => $locations,
            'experienceLevels' => $experienceLevels,
            'employmentTypes' => $employmentTypes,
            'categories' => $categories,
            'pager' => $pager,
            'activeTab' => $activeTab,
            'recommendationType' => $recommendationType,
            'suggestedJobs' => $suggestedJobs,
            'suggestedJobsByApplies' => $suggestedJobsByApplies,
            'suggestedJobsBySkills' => $suggestedJobsBySkills,
            'suggestedJobsByPreferences' => $suggestedJobsByPreferences,
            'suggestedJobsByAi' => $suggestedJobsByAi,
            'candidateSkills' => $candidateSkills,
            'candidateInterests' => $candidateInterests,
            'behavior' => $behavior,
            'showFilters' => $showFilters,
            'savedJobIds' => $savedJobIds,
        ]);
    }

    private function getUserSkills($userId)
    {
        $skillsModel = model('CandidateSkillsModel');
        $userSkills = $skillsModel->where('candidate_id', $userId)->first();

        if ($userSkills && !empty($userSkills['skill_name'])) {
            return array_map('trim', explode(',', (string) $userSkills['skill_name']));
        }

        return [];
    }

    private function rankJobsByApplicationBehavior(int $candidateId, array $behavior, int $limit): array
    {
        $jobModel = new JobModel();
        $jobs = $jobModel->where('status', 'open')
            ->whereNotIn('id', static function ($builder) use ($candidateId) {
                return $builder->select('job_id')->from('applications')->where('candidate_id', $candidateId);
            })
            ->orderBy('created_at', 'DESC')
            ->findAll(200);

        $topCategories = array_map('strtolower', array_column((array) ($behavior['top_categories'] ?? []), 'category'));
        $topLocations = array_map('strtolower', array_column((array) ($behavior['top_locations'] ?? []), 'location'));
        $topEmploymentTypes = array_map('strtolower', array_column((array) ($behavior['top_employment_types'] ?? []), 'employment_type'));

        $ranked = [];
        foreach ($jobs as $job) {
            $score = 0.0;
            $jobCategory = strtolower(trim((string) ($job['category'] ?? '')));
            $jobLocation = strtolower(trim((string) ($job['location'] ?? '')));
            $jobType = strtolower(trim((string) ($job['employment_type'] ?? '')));

            if ($jobCategory !== '' && in_array($jobCategory, $topCategories, true)) {
                $score += 50;
            }
            foreach ($topLocations as $loc) {
                if ($loc !== '' && (str_contains($jobLocation, $loc) || str_contains($loc, $jobLocation))) {
                    $score += 30;
                    break;
                }
            }
            if ($jobType !== '' && in_array($jobType, $topEmploymentTypes, true)) {
                $score += 20;
            }

            if ($score <= 0) {
                continue;
            }

            $job['match_score'] = round(min(100, $score), 1);
            $job['match_reason'] = '';
            $ranked[] = $job;
        }

        usort($ranked, static fn (array $a, array $b): int => ((float) ($b['match_score'] ?? 0.0)) <=> ((float) ($a['match_score'] ?? 0.0)));
        return array_slice($ranked, 0, $limit);
    }

    private function rankJobsByPreferences(int $candidateId, array $behavior, array $candidateInterests, int $limit): array
    {
        $jobModel = new JobModel();
        $userModel = new \App\Models\UserModel();

        $profile = $userModel->findCandidateWithProfile($candidateId) ?? [];
        $preferredJobTitles = array_values(array_filter(array_map(
            'trim',
            array_map(
                'strtolower',
                preg_split('/[,|\\/]+/', (string) ($profile['preferred_job_titles'] ?? '')) ?: []
            )
        )));
        $preferredLocations = array_filter(array_map('trim', explode(',', strtolower((string) ($profile['preferred_locations'] ?? '')))));
        $preferredEmploymentTypes = array_values(array_filter(array_map(
            'trim',
            array_map(
                'strtolower',
                preg_split('/[,|\\/]+/', (string) ($profile['preferred_employment_type'] ?? '')) ?: []
            )
        )));
        $interests = array_values(array_filter(array_map('strtolower', array_map('trim', $candidateInterests))));

        $jobs = $jobModel->where('status', 'open')
            ->whereNotIn('id', static function ($builder) use ($candidateId) {
                return $builder->select('job_id')->from('applications')->where('candidate_id', $candidateId);
            })
            ->orderBy('created_at', 'DESC')
            ->findAll(200);

        $ranked = [];
        foreach ($jobs as $job) {
            $score = 0.0;
            $blob = strtolower(trim((string) ($job['title'] ?? '') . ' ' . (string) ($job['category'] ?? '') . ' ' . (string) ($job['description'] ?? '')));
            $jobTitle = strtolower(trim((string) ($job['title'] ?? '')));
            foreach ($preferredJobTitles as $preferredJobTitle) {
                if ($preferredJobTitle !== '' && (str_contains($jobTitle, $preferredJobTitle) || str_contains($preferredJobTitle, $jobTitle))) {
                    $score += 35;
                    break;
                }
            }
            foreach ($interests as $interest) {
                if ($interest !== '' && str_contains($blob, $interest)) {
                    $score += 25;
                }
            }

            $jobLocation = strtolower(trim((string) ($job['location'] ?? '')));
            foreach ($preferredLocations as $loc) {
                if ($loc !== '' && (str_contains($jobLocation, $loc) || str_contains($loc, $jobLocation))) {
                    $score += 25;
                    break;
                }
            }

            $jobType = strtolower(trim((string) ($job['employment_type'] ?? '')));
            if ($jobType !== '' && in_array($jobType, $preferredEmploymentTypes, true)) {
                $score += 15;
            }

            if ($score <= 0) {
                continue;
            }

            $job['match_score'] = round(min(100, $score), 1);
            $job['match_reason'] = '';
            $ranked[] = $job;
        }

        usort($ranked, static fn (array $a, array $b): int => ((float) ($b['match_score'] ?? 0.0)) <=> ((float) ($a['match_score'] ?? 0.0)));
        return array_slice($ranked, 0, $limit);
    }

    private function buildOtherRecommendations(array $aiJobs, array $skillsJobs, array $preferencesJobs, int $limit): array
    {
        $merged = [];

        foreach ($aiJobs as $job) {
            $id = (int) ($job['id'] ?? 0);
            if ($id <= 0) {
                continue;
            }

            if (empty($job['match_reason'])) {
                $job['match_reason'] = 'AI-assisted recommendation';
            }
            $job['match_score'] = (float) ($job['match_score'] ?? 0.0);
            $merged[$id] = $job;
        }

        $secondarySets = [$skillsJobs, $preferencesJobs];
        foreach ($secondarySets as $set) {
            foreach ($set as $job) {
                $id = (int) ($job['id'] ?? 0);
                if ($id <= 0 || isset($merged[$id])) {
                    continue;
                }

                $baseScore = (float) ($job['match_score'] ?? 0.0);
                $job['match_score'] = round(min(100, $baseScore * 0.9), 1);
                $job['match_reason'] = 'Matched using profile relevance signals';
                $merged[$id] = $job;
            }
        }

        $result = array_values($merged);
        usort($result, static fn (array $a, array $b): int => ((float) ($b['match_score'] ?? 0.0)) <=> ((float) ($a['match_score'] ?? 0.0)));
        return array_slice($result, 0, $limit);
    }

    public function jobDetail($id)
    {
        if (session()->get('role') !== 'candidate') {
            return redirect()->to(base_url('recruiter/dashboard'))->with('error', 'Access denied.');
        }

        $jobModel = new JobModel();
        $companyModel = new CompanyModel();
        $applicationModel = model('ApplicationModel');
        $candidateId = (int) session()->get('user_id');

        $job = $jobModel
            ->where('id', $id)
            ->where('status', 'open')
            ->first();

        if (!$job) {
            return redirect()->to(base_url('jobs'))
                ->with('error', 'Job not found');
        }

        // External jobs: skip the blank detail page and go straight to the source
        if ((int) ($job['is_external'] ?? 0) === 1) {
            $applyUrl = trim((string) ($job['external_apply_url'] ?? ''));
            if ($applyUrl !== '' && filter_var($applyUrl, FILTER_VALIDATE_URL)) {
                return redirect()->to($applyUrl);
            }
        }

        $application = $applicationModel
            ->where('job_id', $id)
            ->where('candidate_id', $candidateId)
            ->where('status !=', 'withdrawn')
            ->first();

        $alreadyApplied = $application ? true : false;
        $interviewId = null;

        $isSaved = (bool) (new SavedJobModel())
            ->where('candidate_id', $candidateId)
            ->where('job_id', (int) $id)
            ->first();

        $company = null;
        $companyId = (int) ($job['company_id'] ?? 0);
        if ($companyId > 0) {
            $company = $companyModel->find($companyId);
        }

        if (!$company && !empty($job['company'])) {
            $company = $companyModel->where('name', $job['company'])->first();
        }

        $resumeCoach = JobModel::isExternalJob($job) ? [] : $this->buildResumeCoach($candidateId, $job);

        return view('candidate/job_details', [
            'title' => 'Job Details',
            'job' => $job,
            'company' => $company,
            'alreadyApplied' => $alreadyApplied,
            'interviewId' => $interviewId,
            'isSaved' => $isSaved,
            'resumeCoach' => $resumeCoach,
        ]);
    }

    private function buildResumeCoach(int $candidateId, array $job): array
    {
        $user = (new UserModel())->findCandidateWithProfile($candidateId) ?? [];
        $skillsRow = (new CandidateSkillsModel())->where('candidate_id', $candidateId)->first();
        $resumeVersion = (new CandidateResumeVersionModel())->getPreferredVersionForJob($candidateId, (int) ($job['id'] ?? 0));

        $requiredSkills = $this->tokenizeSkills((string) ($job['required_skills'] ?? ''));
        $profileSkills = $this->tokenizeSkills((string) ($skillsRow['skill_name'] ?? ''));
        $resumeSkills = $this->tokenizeSkills((string) ($resumeVersion['highlight_skills'] ?? ''));
        $candidateSkills = array_values(array_unique(array_merge($profileSkills, $resumeSkills)));

        $matchedSkills = array_values(array_intersect($requiredSkills, $candidateSkills));
        $missingSkills = array_values(array_diff($requiredSkills, $candidateSkills));

        $summaryText = strtolower(trim((string) ($resumeVersion['summary'] ?? '')));
        $jobTitle = trim((string) ($job['title'] ?? 'this role'));
        $titleTokens = $this->tokenizeSkills($jobTitle);
        $summaryAlignment = 0;
        foreach ($titleTokens as $token) {
            if ($token !== '' && str_contains($summaryText, strtolower($token))) {
                $summaryAlignment++;
            }
        }

        $profileReadiness = 0;
        if (!empty($user['resume_path'])) {
            $profileReadiness += 6;
        }
        if (!empty($user['bio'])) {
            $profileReadiness += 4;
        }
        if (!empty($user['location'])) {
            $profileReadiness += 2;
        }
        if (!empty($candidateSkills)) {
            $profileReadiness += 3;
        }

        $skillScore = empty($requiredSkills) ? 50 : (int) round((count($matchedSkills) / max(1, count($requiredSkills))) * 50);
        $summaryScore = empty($titleTokens) ? 15 : (int) round((min(count($titleTokens), $summaryAlignment) / max(1, count($titleTokens))) * 20);
        $readinessScore = max(0, min(100, $skillScore + $summaryScore + $profileReadiness + (!empty($resumeVersion) ? 15 : 5)));

        $suggestions = [];
        if (!empty($missingSkills)) {
            $suggestions[] = 'Add missing job keywords like ' . implode(', ', array_slice($missingSkills, 0, 4)) . ' where you have real experience.';
        }
        if ($summaryAlignment === 0) {
            $suggestions[] = 'Rewrite your summary to mention the target role "' . $jobTitle . '" and the strongest matching skills.';
        }
        if (empty($resumeVersion)) {
            $suggestions[] = 'Generate a job-specific AI resume version for this role instead of using a generic resume.';
        }
        if (empty($user['bio'])) {
            $suggestions[] = 'Complete your profile bio so your resume and profile tell the same story to recruiters.';
        }
        if (empty($suggestions)) {
            $suggestions[] = 'Your resume already aligns reasonably well. Focus on sharper achievement bullets and measurable impact.';
        }

        $emphasisSkills = !empty($matchedSkills)
            ? array_slice($matchedSkills, 0, 5)
            : array_slice($requiredSkills, 0, 5);

        $summarySuggestion = 'Tailor your opening summary for ' . $jobTitle . ' by highlighting '
            . (!empty($emphasisSkills) ? implode(', ', array_slice($emphasisSkills, 0, 3)) : 'your most relevant experience')
            . ' and one measurable result from past work.';

        $fallback = [
            'score' => $readinessScore,
            'required_skills' => $requiredSkills,
            'matched_skills' => $matchedSkills,
            'missing_skills' => $missingSkills,
            'emphasis_skills' => $emphasisSkills,
            'suggestions' => $suggestions,
            'summary_suggestion' => $summarySuggestion,
            'resume_version' => $resumeVersion,
            'resume_studio_url' => base_url('candidate/resume-studio?generation_mode=job&job_id=' . (int) ($job['id'] ?? 0)),
            'source' => 'fallback',
        ];

        $resumeContext = [
            'profile' => [
                'headline' => (string) ($user['resume_headline'] ?? ''),
                'bio' => (string) ($user['bio'] ?? ''),
                'location' => (string) ($user['location'] ?? ''),
                'has_resume' => !empty($user['resume_path']),
            ],
            'profile_skills' => $profileSkills,
            'resume_version' => [
                'title' => (string) ($resumeVersion['title'] ?? ''),
                'target_role' => (string) ($resumeVersion['target_role'] ?? ''),
                'summary' => (string) ($resumeVersion['summary'] ?? ''),
                'highlight_skills' => $resumeSkills,
                'content' => (string) ($resumeVersion['content'] ?? ''),
            ],
            'candidate_skills' => $candidateSkills,
        ];

        return (new AiResumeCoach())->generate($candidateId, $job, $resumeContext, $fallback);
    }

    private function tokenizeSkills(string $value): array
    {
        $parts = preg_split('/[,|\\/]+/', strtolower($value)) ?: [];
        $tokens = [];

        foreach ($parts as $part) {
            $trimmed = trim($part);
            if ($trimmed !== '') {
                $tokens[] = $trimmed;
            }
        }

        return array_values(array_unique($tokens));
    }

    private function applyWorkModeFilter($builder, string $workMode): void
    {
        $mode = strtolower(trim($workMode));
        if ($mode === '') {
            return;
        }

        switch ($mode) {
            case 'remote':
                $builder->groupStart()
                    ->like('location', 'Remote')
                    ->orLike('location', 'Work from home')
                    ->orLike('location', 'WFH')
                    ->groupEnd();
                break;

            case 'hybrid':
                $builder->groupStart()
                    ->like('location', 'Hybrid')
                    ->groupEnd();
                break;

            case 'onsite':
            case 'on-site':
            case 'office':
                $builder->groupStart()
                    ->notLike('location', 'Remote')
                    ->notLike('location', 'Hybrid')
                    ->groupEnd();
                break;
        }
    }

    private function applySalaryRangeFilter($builder, string $salaryRange): void
    {
        $range = strtolower(trim($salaryRange));
        if ($range === '') {
            return;
        }

        $salaryExpr = "CAST(TRIM(SUBSTRING_INDEX(REPLACE(COALESCE(salary_range, ''), '-', ' '), ' ', 1)) AS DECIMAL(10,2))";

        switch ($range) {
            case 'under_3':
                $builder->groupStart()
                    ->where('salary_range IS NOT NULL', null, false)
                    ->where('salary_range !=', '')
                    ->where($salaryExpr . ' <', 3, false)
                    ->groupEnd();
                break;

            case '3_5':
                $builder->groupStart()
                    ->where('salary_range IS NOT NULL', null, false)
                    ->where('salary_range !=', '')
                    ->where($salaryExpr . ' >=', 3, false)
                    ->where($salaryExpr . ' <', 5, false)
                    ->groupEnd();
                break;

            case '5_8':
                $builder->groupStart()
                    ->where('salary_range IS NOT NULL', null, false)
                    ->where('salary_range !=', '')
                    ->where($salaryExpr . ' >=', 5, false)
                    ->where($salaryExpr . ' <', 8, false)
                    ->groupEnd();
                break;

            case '8_12':
                $builder->groupStart()
                    ->where('salary_range IS NOT NULL', null, false)
                    ->where('salary_range !=', '')
                    ->where($salaryExpr . ' >=', 8, false)
                    ->where($salaryExpr . ' <', 12, false)
                    ->groupEnd();
                break;

            case '12_plus':
                $builder->groupStart()
                    ->where('salary_range IS NOT NULL', null, false)
                    ->where('salary_range !=', '')
                    ->where($salaryExpr . ' >=', 12, false)
                    ->groupEnd();
                break;
        }
    }
}
