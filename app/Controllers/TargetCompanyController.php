<?php

namespace App\Controllers;

use App\Models\TargetCompanyModel;
use App\Libraries\TargetCompanyJobService;

class TargetCompanyController extends BaseController
{
    protected $model;
    protected $service;

    public function __construct()
    {
        $this->model   = new TargetCompanyModel();
        $this->service = new TargetCompanyJobService();
    }

    // Add a target company
    public function add()
    {
        $userId      = (int) session()->get('user_id');
        $companyName = trim((string) $this->request->getPost('company_name'));
        $limit       = (int) ($this->request->getPost('limit') ?: 10);
        $limit       = max(1, min(100, $limit));

        if ($companyName === '') {
            return redirect()->back()->with('error', 'Company name is required.');
        }

        // Auto-resolve platform/slug from known list
        $resolved = $this->service->resolveCompany($companyName);
        $platform = $resolved['platform'] ?? '';
        $slug     = $resolved['slug'] ?? '';

        $added = $this->model->addCompany($userId, $companyName, $platform, $slug);

        if (!$added) {
            return redirect()->back()->with('error', "{$companyName} is already in your target list.");
        }

        $jobs = $this->service->fetchJobs($companyName, $platform, $slug, $limit);
        session()->setFlashdata('target_company_jobs', $jobs);
        session()->setFlashdata('target_company_name', $companyName);
        session()->setFlashdata('target_company_limit', $limit);

        $message = "{$companyName} added to your target companies!";
        if (empty($jobs)) {
            $message .= ' No jobs were found yet; the company has been added and you can refresh jobs anytime.';
        } else {
            $message .= ' Jobs were loaded and are shown below.';
        }

        return redirect()->back()->with('success', $message);
    }

    // Remove a target company
    public function remove(int $id)
    {
        $userId = (int) session()->get('user_id');
        $this->model->removeCompany($userId, $id);
        return redirect()->back()->with('success', 'Company removed from your target list.');
    }

    // Fetch live jobs for a company via AJAX
    public function fetchJobs()
    {
        $companyName = trim((string) $this->request->getPost('company_name'));
        $platform    = trim((string) $this->request->getPost('platform'));
        $slug        = trim((string) $this->request->getPost('slug'));
        $limit       = (int) ($this->request->getPost('limit') ?: 10);
        $limit       = max(1, min(100, $limit));

        if ($companyName === '') {
            return $this->response->setJSON(['error' => 'Company name required', 'jobs' => []]);
        }

        $jobs = $this->service->fetchJobs($companyName, $platform, $slug, $limit);
        $isKnown = $this->service->isKnownCompany($companyName);

        return $this->response->setJSON([
            'company' => $companyName,
            'count'   => count($jobs),
            'jobs'    => $jobs,
            'supported' => $isKnown || !empty($jobs),
        ]);
    }

    // Autocomplete known companies
    public function suggest()
    {
        $names = $this->service->getKnownCompanyNames();
        return $this->response->setJSON($names);
    }

    public function myTargets()
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'candidate') {
            return $this->response->setJSON(['error' => 'Candidate access only']);
        }
        $userId = (int) session()->get('user_id');
        $companies = $this->model->getUserCompaniesWithJobs($userId, $this->service);
        return $this->response->setJSON([
            'success' => true,
            'companies' => $companies
        ]);
    }

    public function refreshTarget(int $id)
    {
        if (!session()->get('logged_in') || session()->get('role') !== 'candidate') {
            return $this->response->setJSON(['error' => 'Access denied']);
        }
        $userId = (int) session()->get('user_id');
        $company = $this->model->where('id', $id)->where('user_id', $userId)->first();
        if (!$company) {
            return $this->response->setJSON(['error' => 'Target not found']);
        }
        $jobs = $this->service->fetchJobs($company['company_name'], 
            $company['careers_platform'] ?? '', 
            $company['platform_slug'] ?? '', 10);
        return $this->response->setJSON([
            'success' => true,
            'company_id' => $id,
            'jobs' => $jobs,
            'count' => count($jobs)
        ]);
    }
}