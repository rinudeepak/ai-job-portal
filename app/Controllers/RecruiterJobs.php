<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JobModel;

class RecruiterJobs extends BaseController
{
    public function index()
    {
        $jobModel = model('JobModel');
        $applicationModel = model('ApplicationModel');
        $currentUserId = session()->get('user_id');

        $jobs = $jobModel
            ->select('jobs.*, COUNT(applications.id) as application_count')
            ->join('applications', 'applications.job_id = jobs.id', 'left')
            ->where('jobs.recruiter_id', $currentUserId)
            ->groupBy('jobs.id')
            ->orderBy('jobs.created_at', 'DESC')
            ->findAll();

        return view('recruiter/jobs/index', ['jobs' => $jobs]);
    }

    public function edit($jobId)
    {
        $jobModel = model('JobModel');
        $currentUserId = session()->get('user_id');

        $job = $jobModel->where('id', $jobId)->where('recruiter_id', $currentUserId)->first();
        
        if (!$job) {
            return redirect()->to('recruiter/jobs')->with('error', 'Job not found');
        }

        return view('recruiter/jobs/edit', ['job' => $job]);
    }

    public function update($jobId)
    {
        $jobModel = model('JobModel');
        $currentUserId = session()->get('user_id');

        $job = $jobModel->where('id', $jobId)->where('recruiter_id', $currentUserId)->first();
        
        if (!$job) {
            return redirect()->to('recruiter/jobs')->with('error', 'Job not found');
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'location' => $this->request->getPost('location'),
            'salary' => $this->request->getPost('salary'),
            'required_skills' => $this->request->getPost('required_skills'),
            'openings' => $this->request->getPost('openings')
        ];

        // Keep backward compatibility if DB is not migrated yet.
        $db = \Config\Database::connect();
        if ($db->fieldExists('ai_interview_policy', 'jobs')) {
            $data['ai_interview_policy'] = JobModel::normalizeAiPolicy($this->request->getPost('ai_interview_policy'));
        }

        $jobModel->update($jobId, $data);

        return redirect()->to('recruiter/jobs')->with('success', 'Job updated successfully');
    }

    public function close($jobId)
    {
        $jobModel = model('JobModel');
        $currentUserId = session()->get('user_id');

        $job = $jobModel->where('id', $jobId)->where('recruiter_id', $currentUserId)->first();
        
        if (!$job) {
            return redirect()->to('recruiter/jobs')->with('error', 'Job not found');
        }

        $jobModel->update($jobId, ['status' => 'closed']);

        return redirect()->to('recruiter/jobs')->with('success', 'Job closed successfully');
    }

    public function reopen($jobId)
    {
        $jobModel = model('JobModel');
        $currentUserId = session()->get('user_id');

        $job = $jobModel->where('id', $jobId)->where('recruiter_id', $currentUserId)->first();
        
        if (!$job) {
            return redirect()->to('recruiter/jobs')->with('error', 'Job not found');
        }

        $jobModel->update($jobId, ['status' => 'open']);

        return redirect()->to('recruiter/jobs')->with('success', 'Job reopened successfully');
    }
}
