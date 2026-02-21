<?php

namespace App\Controllers;

use App\Models\CompanyModel;
use App\Models\JobModel;

class Jobs extends BaseController
{
    public function index()
    {
        $jobModel = new JobModel();
        $candidateId = session()->get('user_id');
        
        // Get filter parameters
        $filters = [
            'search'           => $this->request->getGet('search'),
            'location'         => $this->request->getGet('location'),
            'category'         => $this->request->getGet('category'),
            'experience_level' => $this->request->getGet('experience_level'),
            'employment_type'  => $this->request->getGet('employment_type'),
            'remote'           => $this->request->getGet('remote'),
            'posted_within'    => $this->request->getGet('posted_within'),
            'skills_match'     => $this->request->getGet('skills_match'),
            'sort'             => $this->request->getGet('sort') ?: 'newest',
        ];
        
        // Build query with filters
        $builder = $jobModel->where('status', 'open');
        
        // Keep all open jobs visible in listings.
        // Re-apply is already blocked in the job details/apply flow.
        
        // Apply filters
        if (!empty($filters['search'])) {
            $builder->groupStart()
                    ->like('title', $filters['search'])
                    ->orLike('company', $filters['search'])
                    ->orLike('required_skills', $filters['search'])
                    ->orLike('description', $filters['search'])
                    ->groupEnd();
        }

        if (!empty($filters['location'])) {
            $builder->like('location', $filters['location']);
        }

        if (!empty($filters['category'])) {
            $builder->like("TRIM(category)", trim($filters['category']));
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
        
        if (!empty($filters['remote'])) {
            $builder->like('location', 'Remote');
        }
        
        if (!empty($filters['posted_within'])) {
            $days = (int)$filters['posted_within'];
            $builder->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        }
        
        // Skills match filter
        if (!empty($filters['skills_match']) && session()->get('user_id')) {
            $userSkills = $this->getUserSkills(session()->get('user_id'));
            if (!empty($userSkills)) {
                $builder->groupStart();
                foreach ($userSkills as $skill) {
                    $builder->orLike('required_skills', $skill);
                }
                $builder->groupEnd();
            }
        }
        
        // Apply sorting
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
        
        // Get unique locations and experience levels for filter options
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
    
        
        // ── Suggested / "For You" tab data ──────────────────────────────────
        $activeTab      = $this->request->getGet('tab') === 'suggested' ? 'suggested' : 'all';
        $useAi          = (bool) $this->request->getGet('ai'); // ?ai=1 to enable AI
        $suggestedJobs  = [];
        $candidateSkills    = [];
        $candidateInterests = [];
        $behavior           = [];

        if ($candidateId) {
            $skillsModel    = new \App\Models\CandidateSkillsModel();
            $interestsModel = new \App\Models\CandidateInterestsModel();

            // Skills: stored as a comma-separated string in one row — explode correctly
            $skillRow = $skillsModel->where('candidate_id', $candidateId)->first();
            if ($skillRow && !empty($skillRow['skill_name'])) {
                $candidateSkills = array_values(
                    array_filter(array_map('trim', explode(',', $skillRow['skill_name'])))
                );
            }

            // Interests: stored as one comma-separated row per candidate
            $interestRow = $interestsModel->where('candidate_id', $candidateId)->first();
            $candidateInterests = [];
            if ($interestRow && !empty($interestRow['interest'])) {
                $candidateInterests = array_values(
                    array_filter(array_map('trim', explode(',', $interestRow['interest'])))
                );
            }

            $behavior = $jobModel->getCandidateBehaviorProfile($candidateId);

            // Only compute suggestions when the "For You" tab is active (or always — cheap enough)
            if ($useAi) {
                $matcher       = new \App\Libraries\AiJobMatcher();
                $suggestedJobs = $matcher->generateSuggestions($candidateId, 20);
            } else {
                $suggestedJobs = $jobModel->getSuggestedJobsBasic($candidateId, 20);
            }
        }
        // ────────────────────────────────────────────────────────────────────

        // Attach company logo for job cards.
        $companyIds = [];
        foreach ($jobs as $job) {
            $id = (int) ($job['company_id'] ?? 0);
            if ($id > 0) {
                $companyIds[] = $id;
            }
        }
        foreach ($suggestedJobs as $job) {
            $id = (int) ($job['company_id'] ?? 0);
            if ($id > 0) {
                $companyIds[] = $id;
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
        foreach ($suggestedJobs as $index => $job) {
            $id = (int) ($job['company_id'] ?? 0);
            $suggestedJobs[$index]['company_logo'] = $companyLogoMap[$id] ?? '';
        }

        return view('candidate/smart_jobs', [
            'jobs'               => $jobs,
            'totalJobs'          => $totalJobs,
            'filters'            => $filters,
            'locations'          => $locations,
            'experienceLevels'   => $experienceLevels,
            'employmentTypes'    => $employmentTypes,
            'categories'         => $categories,
            'pager'              => $pager,
            'activeTab'          => $activeTab,
            'suggestedJobs'      => $suggestedJobs,
            'candidateSkills'    => $candidateSkills,
            'candidateInterests' => $candidateInterests,
            'behavior'           => $behavior,
            'useAi'              => $useAi,
        ]);
    }
    

    
    private function getUserSkills($userId)
    {
        $skillsModel = model('CandidateSkillsModel');
        $userSkills = $skillsModel->where('candidate_id', $userId)->first();
        
        if ($userSkills && !empty($userSkills['skill_name'])) {
            return array_map('trim', explode(',', $userSkills['skill_name']));
        }
        
        return [];
    }
    public function jobDetail($id)
    {
        $jobModel = new JobModel();
        $applicationModel = model('ApplicationModel');
        $interviewModel = model('InterviewSessionModel');

        $job = $jobModel
            ->where('id', $id)
            ->where('status', 'open')
            ->first();

        if (!$job) {
            return redirect()->to(base_url('candidate/jobs'))
                ->with('error', 'Job not found');
        }

        // Get application (only once)
        $application = $applicationModel
            ->where('job_id', $id)
            ->where('candidate_id', session()->get('user_id'))
            ->first();

        $alreadyApplied = $application ? true : false;

        $interviewId = null;

        if ($application) {
            $interview = $interviewModel
                ->where('application_id', $application['id'])
                ->first();

            $interviewId = $interview ? $interview['id'] : null;
        }

        return view('candidate/job_details', [
            'title' => 'Job Details',
            'job' => $job,
            'alreadyApplied' => $alreadyApplied,
            'interviewId' => $interviewId
        ]);
    }

}
