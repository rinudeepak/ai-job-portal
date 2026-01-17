<?php

namespace App\Controllers;

use App\Models\UserModel;

class Candidate extends BaseController
{
    public function profile()
    {
        return view('candidate/profile');
    }

    public function saveProfile()
    {
        $session = session();

        $model = new UserModel();
        $file = $this->request->getFile('resume');

        $resumePath = null;
        if ($file && $file->isValid()) {
            $resumePath = $file->getRandomName();
            $file->move('uploads/resumes/', $resumePath);
        }

        $model->update($session->get('user_id'), [
            'github_username' => $this->request->getPost('github_username'),
            'linkedin_link'   => $this->request->getPost('linkedin_link'),
            'resume'          => $resumePath
        ]);



        return redirect()->back()->with('success', 'Profile Updated Successfully');
    }
}
