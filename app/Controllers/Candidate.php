<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\ResumeParser;
use App\Models\CandidateSkillsModel;
use App\Models\GithubAnalysisModel;
use App\Libraries\GithubAnalyzer;


class Candidate extends BaseController
{
    public function profile()
    {
        return view('candidate/profile');
    }



    public function resumeUpload()
    {
        $session = session();

        // 1️⃣ Check login
        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }


        $candidateId = $session->get('user_id');
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

        // Save resume file path in DB
        $candidateModel = new UserModel();
        $candidateModel->update($candidateId, [
            'resume_path' => 'uploads/resumes/' . $file->getName()
        ]);

        // Parse resume
        $parser = new ResumeParser();
        $result = $parser->parse($filePath);
        $skillModel = new CandidateSkillsModel();


        //if user uploads the same resume twice, skills will be inserted again.delete the previous one
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
        $candidateId = $session->get('user_id');
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
        //if user update the github username, deleting previous entries
        $githubModel = new GithubAnalysisModel();

        $githubModel->where('candidate_id', $candidateId)->delete();

        $githubModel->insert([
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
