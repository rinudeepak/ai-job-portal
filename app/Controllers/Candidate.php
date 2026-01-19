<?php

namespace App\Controllers;

// use App\Models\UserModel;
use App\Libraries\ResumeParser;
use App\Models\CandidateSkillsModel;
use App\Libraries\GithubAnalyzer;


class Candidate extends BaseController
{
    public function profile()
    {
        return view('candidate/profile');
    }

    // public function saveProfile()
    // {
    //     $session = session();

    //     $model = new UserModel();

    //     $model->update($session->get('user_id'), [
    //         'github_username' => $this->request->getPost('github_username'),
    //         'linkedin_link' => $this->request->getPost('linkedin_link')
    //         // 'resume'          => $resumePath
    //     ]);



    //     return redirect()->back()->with('profile_success', 'Profile Updated Successfully');
    // }

    public function resumeUpload()
    {
        $file = $this->request->getFile('resume');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'No file uploaded or invalid file'
            ]);
        }

        // Allow PDF, DOCX, TXT
        $allowedTypes = ['pdf', 'docx', 'txt'];

        if (!in_array(strtolower($file->getExtension()), $allowedTypes)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Only PDF, DOCX or TXT files allowed'
            ]);
        }

        // Move file to writable/uploads/resumes
        $uploadPath = WRITEPATH . 'uploads/resumes/';

        if (!$file->move($uploadPath)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'File upload failed'
            ]);
        }

        $filePath = $uploadPath . $file->getName();

        // Parse resume
        $parser = new ResumeParser();
        $result = $parser->parse($filePath);
        $skillModel = new CandidateSkillsModel();

        $candidateId = session()->get('user_id');
        //if user uploads the same resume twice, skills will be inserted again.preventing duplicate entry
        $skillModel->where('candidate_id', $candidateId)->delete();

        foreach ($result['skills'] as $skill) {
            $skillModel->insert([
                'candidate_id' => $candidateId,
                'skill_name' => $skill['name']

            ]);
        }
        return redirect()->back()->with('upload_success', 'Resume Uploaded Successfully');

    }
    public function analyzeGithubSkills()
    {
        $session = session();
        $username = $this->request->getPost('github_username');

        if (!$username) {
            return redirect()->back()->with('error', 'GitHub username required');
        }

        $github = new GithubAnalyzer();
        $data = $github->analyze($username);

        if (empty($data['languages'])) {
            return redirect()->back()->with('error', 'GitHub profile not found or API blocked');
        }

        $db = \Config\Database::connect();

        $db->table('candidate_github_stats')->insert([
            'candidate_id' => $session->get('user_id'),
            'github_username' => $username,
            'repo_count' => $data['repo_count'],
            'commit_count' => $data['commit_count'],
            'languages_used' => implode(',', $data['languages']),
            'github_score' => min(10, round($data['commit_count'] / 20))
        ]);

        return redirect()->back()->with('success', 'GitHub profile analyzed successfully');
    }



}
