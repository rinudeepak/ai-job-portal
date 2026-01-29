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
