<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class RecruiterApplications extends BaseController
{
    public function index()
    {
        $jobModel = model('JobModel');
        $applicationModel = model('ApplicationModel');
        $currentUserId = session()->get('user_id');

        // Get recruiter's jobs with application counts
        $jobs = $jobModel
            ->select('jobs.*, COUNT(applications.id) as application_count')
            ->join('applications', 'applications.job_id = jobs.id', 'left')
            ->where('jobs.recruiter_id', $currentUserId)
            ->groupBy('jobs.id')
            ->orderBy('jobs.created_at', 'DESC')
            ->findAll();

        return view('recruiter/applications/index', ['jobs' => $jobs]);
    }

    public function viewByJob($jobId)
    {
        $jobModel = model('JobModel');
        $applicationModel = model('ApplicationModel');
        $currentUserId = session()->get('user_id');

        // Verify job belongs to recruiter
        $job = $jobModel->where('id', $jobId)->where('recruiter_id', $currentUserId)->first();
        if (!$job) {
            return redirect()->to('recruiter/applications')->with('error', 'Job not found');
        }

        // Get applications for this job
        $applications = $applicationModel
            ->select('applications.*, users.name, users.email, interview_sessions.overall_rating')
            ->join('users', 'users.id = applications.candidate_id', 'left')
            ->join('interview_sessions', 'interview_sessions.application_id = applications.id', 'left')
            ->where('applications.job_id', $jobId)
            ->orderBy('applications.applied_at', 'DESC')
            ->findAll();

        return view('recruiter/applications/view_by_job', [
            'job' => $job,
            'applications' => $applications
        ]);
    }
}
