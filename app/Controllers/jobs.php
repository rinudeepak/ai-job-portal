<?php

namespace App\Controllers;

use App\Models\JobModel;

class Jobs extends BaseController
{
    public function index()
    {
        $jobModel = new JobModel();

        $data['jobs'] = $jobModel->where('status', 'open')->findAll();
        $data['totalJobs'] = $jobModel->getTotalOpenJobs();


        return view('candidate/job_listing', $data);
        
    }
    public function jobDetail($id)
    {
        $jobModel = new JobModel();

        $job = $jobModel
            ->where('id', $id)
            ->where('status', 'open')
            ->first();

        // If job not found
        if (!$job) {
            return redirect()->to(base_url('candidate/jobs'))
                ->with('error', 'Job not found');
        }

        return view('candidate/job_details', [
            'title' => 'Job Details',
            'job'   => $job
        ]);
    }

}
