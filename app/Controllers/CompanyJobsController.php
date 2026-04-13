<?php

namespace App\Controllers;

use App\Libraries\JobAggregator;

class CompanyJobsController extends BaseController
{
    private $jobAggregator;
    private $jobModel;
    private $companyModel;

    public function __construct()
    {
        $this->jobAggregator = new JobAggregator();
        $this->jobModel = model('JobModel');
        $this->companyModel = model('CompanyModel');
    }

    /**
     * Search jobs by company
     */
    public function searchByCompany(string $companyName = '')
    {
        if (empty($companyName)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Company name is required'
            ]);
        }

        $companyName = urldecode($companyName);
        $page = (int) $this->request->getGet('page') ?? 1;
        $limit = (int) $this->request->getGet('limit') ?? 25;

        // Get internal jobs first
        $internalJobs = $this->getInternalJobsByCompany($companyName);

        // Get external jobs from Indeed
        $externalJobs = $this->jobAggregator->fetchJobsByCompany($companyName, $limit);

        // Merge and deduplicate
        $allJobs = array_merge($internalJobs, $externalJobs);

        return $this->response->setJSON([
            'status' => 'success',
            'company' => $companyName,
            'internal_count' => count($internalJobs),
            'external_count' => count($externalJobs),
            'total_count' => count($allJobs),
            'jobs' => $allJobs
        ]);
    }

    /**
     * Get internal jobs by company
     */
    private function getInternalJobsByCompany(string $companyName): array
    {
        // Find company
        $company = $this->companyModel
            ->where('LOWER(name)', 'LIKE', '%' . strtolower($companyName) . '%')
            ->first();

        if (!$company) {
            return [];
        }

        // Get jobs for this company
        return $this->jobModel
            ->where('company_id', $company['id'])
            ->where('status', 'active')
            ->orderBy('posted_at', 'DESC')
            ->findAll();
    }

    /**
     * View company jobs page
     */
    public function viewCompanyJobs(string $companyName = '')
    {
        if (empty($companyName)) {
            return redirect()->to('jobs');
        }

        $companyName = urldecode($companyName);

        // Get internal jobs
        $internalJobs = $this->getInternalJobsByCompany($companyName);

        // Get external jobs from Indeed
        $externalJobs = $this->jobAggregator->fetchJobsByCompany($companyName, 50);

        // Get company info
        $company = $this->companyModel
            ->where('LOWER(name)', 'LIKE', '%' . strtolower($companyName) . '%')
            ->first();

        $data = [
            'title' => "Jobs at {$companyName}",
            'company_name' => $companyName,
            'company' => $company,
            'internal_jobs' => $internalJobs,
            'external_jobs' => $externalJobs,
            'total_jobs' => count($internalJobs) + count($externalJobs)
        ];

        return view('candidate/company_jobs', $data);
    }

    /**
     * Search jobs by keyword and company
     */
    public function search()
    {
        $keyword = $this->request->getGet('q') ?? '';
        $company = $this->request->getGet('company') ?? '';
        $limit = (int) $this->request->getGet('limit') ?? 25;

        if (empty($keyword) && empty($company)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Keyword or company is required'
            ]);
        }

        $jobs = $this->jobAggregator->searchJobs($keyword, $company, $limit);

        return $this->response->setJSON([
            'status' => 'success',
            'keyword' => $keyword,
            'company' => $company,
            'count' => count($jobs),
            'jobs' => $jobs
        ]);
    }

    /**
     * Get job details
     */
    public function getJobDetails(string $jobKey = '')
    {
        if (empty($jobKey)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Job key is required'
            ]);
        }

        $job = $this->jobAggregator->getJobDetails($jobKey);

        if (empty($job)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Job not found'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'job' => $job
        ]);
    }
}
