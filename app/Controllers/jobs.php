<?php

namespace App\Controllers;

use App\Models\JobModel;

class Jobs extends BaseController
{
    public function index()
    {
        $jobModel = new JobModel();
        
        // Get filter parameters
        $filters = [
            'location' => $this->request->getGet('location'),
            'category' => $this->request->getGet('category'),
            'experience_level' => $this->request->getGet('experience_level'),
            'employment_type' => $this->request->getGet('employment_type'),
            'remote' => $this->request->getGet('remote'),
            'posted_within' => $this->request->getGet('posted_within'),
            'skills_match' => $this->request->getGet('skills_match'),
            'sort' => $this->request->getGet('sort') ?: 'newest'
        ];
        
        // Build query with filters
        $builder = $jobModel->where('status', 'open');
        
        // Apply filters
        if (!empty($filters['location'])) {
            $builder->like('location', $filters['location']);
        }

        if (!empty($filters['category'])) {
            $builder->like("TRIM(category)", trim($filters['category']));
        }
        
        if (!empty($filters['experience_level'])) {
            $builder->where('experience_level', $filters['experience_level']);
        }
        
        if (!empty($filters['employment_type'])) {
            $builder->where('employment_type', $filters['employment_type']);
        }
        
        if (!empty($filters['remote'])) {
            $builder->like('location', 'Remote');
        }
        
        if (!empty($filters['posted_within'])) {
            $days = $this->getPostedWithinDays($filters['posted_within']);
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
        
        
        return view('candidate/job_listing', [
            'jobs' => $jobs,
            'totalJobs' => $totalJobs,
            'filters' => $filters,
            'locations' => $locations,
            'experienceLevels' => $experienceLevels,
            'employmentTypes' => $employmentTypes,
            'categories' => $categories,
            'pager' => $pager
        ]);
    }
    

    
    private function getPostedWithinDays($period)
    {
        switch ($period) {
            case '24h': return 1;
            case '7d': return 7;
            case '30d': return 30;
            default: return 30;
        }
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
