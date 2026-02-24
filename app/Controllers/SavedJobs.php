<?php

namespace App\Controllers;

use App\Models\CompanyModel;
use App\Models\JobModel;
use App\Models\SavedJobModel;

class SavedJobs extends BaseController
{
    public function index()
    {
        $candidateId = (int) session()->get('user_id');
        if ($candidateId <= 0) {
            return redirect()->to(base_url('login'));
        }

        $savedRows = (new SavedJobModel())
            ->select('saved_jobs.created_at as saved_at, jobs.*')
            ->join('jobs', 'jobs.id = saved_jobs.job_id', 'inner')
            ->where('saved_jobs.candidate_id', $candidateId)
            ->where('jobs.status', 'open')
            ->orderBy('saved_jobs.created_at', 'DESC')
            ->findAll();

        $companyIds = [];
        foreach ($savedRows as $job) {
            $id = (int) ($job['company_id'] ?? 0);
            if ($id > 0) {
                $companyIds[] = $id;
            }
        }

        $companyLogoMap = [];
        $companyIds = array_values(array_unique($companyIds));
        if (!empty($companyIds)) {
            $companies = (new CompanyModel())
                ->select('id, logo')
                ->whereIn('id', $companyIds)
                ->findAll();
            foreach ($companies as $company) {
                $companyLogoMap[(int) $company['id']] = (string) ($company['logo'] ?? '');
            }
        }

        foreach ($savedRows as $index => $job) {
            $id = (int) ($job['company_id'] ?? 0);
            $savedRows[$index]['company_logo'] = $companyLogoMap[$id] ?? '';
        }

        return view('candidate/saved_jobs', [
            'title' => 'Saved Jobs',
            'jobs' => $savedRows,
        ]);
    }

    public function save(int $jobId)
    {
        $candidateId = (int) session()->get('user_id');
        if ($candidateId <= 0) {
            return redirect()->to(base_url('login'));
        }

        $jobExists = (new JobModel())
            ->select('id')
            ->where('id', $jobId)
            ->where('status', 'open')
            ->first();

        if (!$jobExists) {
            return redirect()->back()->with('error', 'Job not found.');
        }

        $savedJobModel = new SavedJobModel();
        $alreadySaved = $savedJobModel
            ->where('candidate_id', $candidateId)
            ->where('job_id', $jobId)
            ->first();

        if (!$alreadySaved) {
            $savedJobModel->insert([
                'candidate_id' => $candidateId,
                'job_id' => $jobId,
            ]);
        }

        return redirect()->back();
    }

    public function unsave(int $jobId)
    {
        $candidateId = (int) session()->get('user_id');
        if ($candidateId <= 0) {
            return redirect()->to(base_url('login'));
        }

        (new SavedJobModel())
            ->where('candidate_id', $candidateId)
            ->where('job_id', $jobId)
            ->delete();

        return redirect()->back();
    }
}
