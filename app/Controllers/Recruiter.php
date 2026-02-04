<?php

namespace App\Controllers;

use App\Models\JobModel;

class Recruiter extends BaseController
{
    public function postJob()
    {
        return view('recruiter/post_job');
    }

    public function saveJob()
    {
        $session = session();

        $model = new JobModel();

        $model->insert([
            'title' => $this->request->getPost('title'),
            'company' => $this->request->getPost('company'),
            'description' => $this->request->getPost('description'),
            'location' => $this->request->getPost('location'),
            'required_skills' => $this->request->getPost('required_skills'),
            'experience_level' => $this->request->getPost('experience_level'),
            'min_ai_cutoff_score' => $this->request->getPost('min_ai_cutoff_score'),
            'openings' => $this->request->getPost('openings'),
            'recruiter_id' => $session->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->back()->with('success', 'Job Posted Successfully');
    }
}
