<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Libraries\ResumeParser;
use App\Models\CandidateSkillsModel;
use App\Models\GithubAnalysisModel;
use App\Libraries\GithubAnalyzer;
use App\Models\WorkExperienceModel;
use App\Models\EducationModel;
use App\Models\CertificationModel;


class Candidate extends BaseController
{
    public function markNotificationRead($id)
    {
        $notificationModel = model('NotificationModel');
        $notificationModel->markAsRead($id);
        return redirect()->back();
    }

    public function profile()
    {
        $userId = session()->get('user_id');
        $userModel = model('UserModel');
        $user = $userModel->find($userId);
        $githubModel = model('GithubAnalysisModel');
        $github = $githubModel->where('candidate_id', $userId)->first();
        $skillsModel = model('CandidateSkillsModel');
        $skills = $skillsModel->where('candidate_id', $userId)->first();
        
        $workExpModel = new WorkExperienceModel();
        $educationModel = new EducationModel();
        $certificationModel = new CertificationModel();
        
        $workExperiences = $workExpModel->getByUser($userId);
        $education = $educationModel->getByUser($userId);
        $certifications = $certificationModel->getByUser($userId);
        
        // Get application stats
        $applicationModel = model('ApplicationModel');
        $bookingModel = model('InterviewBookingModel');
        
        $totalApplications = $applicationModel->where('candidate_id', $userId)->countAllResults();
        $totalInterviews = $bookingModel->where('user_id', $userId)->countAllResults();
        $totalOffers = $applicationModel->where('candidate_id', $userId)
                                      ->whereIn('status', ['selected', 'hired'])
                                      ->countAllResults();

        // Calculate profile completion percentage
        $completionFields = [
            'name' => !empty($user['name']),
            'email' => !empty($user['email']),
            'phone' => !empty($user['phone']),
            'profile_photo' => !empty($user['profile_photo']),
            'resume' => !empty($user['resume_path']),
            'github' => !empty($github['github_username']),
            'skills' => !empty($skills['skill_name']),
            'location' => !empty($user['location']),
            'bio' => !empty($user['bio'])
        ];
        
        $completedFields = array_sum($completionFields);
        $totalFields = count($completionFields);
        $completionPercentage = round(($completedFields / $totalFields) * 100);

        return view('candidate/profile', [
            'user' => $user,
            'github' => $github,
            'skills' => $skills,
            'workExperiences' => $workExperiences,
            'education' => $education,
            'certifications' => $certifications,
            'stats' => [
                'applications' => $totalApplications,
                'interviews' => $totalInterviews,
                'offers' => $totalOffers
            ],
            'completion' => [
                'percentage' => $completionPercentage,
                'fields' => $completionFields
            ]
        ]);
    }

    public function resumeUpload()
    {
        $session = session();

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

        $allowedTypes = ['pdf', 'docx', 'doc'];

        if (!in_array(strtolower($file->getExtension()), $allowedTypes)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Only PDF, DOCX or DOC files allowed'
            ]);
        }

        $uploadPath = WRITEPATH . 'uploads/resumes/';

        if (!$file->move($uploadPath)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'File upload failed'
            ]);
        }

        $filePath = $uploadPath . $file->getName();

        $candidateModel = new UserModel();
        $candidateModel->update($candidateId, [
            'resume_path' => 'uploads/resumes/' . $file->getName()
        ]);

        $parser = new ResumeParser();
        $result = $parser->parse($filePath);
        $skillModel = new CandidateSkillsModel();

        $skillModel->where('candidate_id', $candidateId)->delete();

        $skillNames = [];
        foreach ($result['skills'] as $skill) {
            $skillName = trim($skill['name']);
            $skillNames[] = $skillName;
        }

        if (!empty($skillNames)) {
            $skillModel->insert([
                'candidate_id' => $candidateId,
                'skill_name' => implode(', ', $skillNames)
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

    public function appliedJobs()
    {
        $candidateId = session()->get('user_id');

        $db = \Config\Database::connect();

        $jobs = $db->table('applications a')
            ->select('a.id as application_id, j.title, a.status, j.id as job_id')
            ->join('jobs j', 'j.id = a.job_id')
            ->where('a.candidate_id', $candidateId)
            ->get()
            ->getResultArray();

        return view('candidate/applied_jobs', [
            'jobs' => $jobs
        ]);
    }

    public function downloadResume()
    {
        $userId = session()->get('user_id');
        $userModel = model('UserModel');
        $user = $userModel->find($userId);
        
        if (!$user || empty($user['resume_path'])) {
            return redirect()->back()->with('error', 'No resume found');
        }
        
        $filePath = WRITEPATH . $user['resume_path'];
        
        if (!file_exists($filePath)) {
            return redirect()->back()->with('error', 'Resume file not found');
        }
        
        return $this->response->download($filePath, null);
    }

    public function previewResume()
    {
        $userId = session()->get('user_id');
        $userModel = model('UserModel');
        $user = $userModel->find($userId);
        
        if (!$user || empty($user['resume_path'])) {
            return $this->response->setJSON(['error' => 'No resume found']);
        }
        
        $filePath = WRITEPATH . $user['resume_path'];
        
        if (!file_exists($filePath)) {
            return $this->response->setJSON(['error' => 'Resume file not found']);
        }
        
        $ext = pathinfo($filePath, PATHINFO_EXTENSION);
        
        // For DOCX/DOC, trigger download instead
        if (in_array(strtolower($ext), ['docx', 'doc'])) {
            return $this->response->setJSON([
                'error' => 'Preview not available for Word documents. Click Download to view the file.'
            ]);
        }
        
        // For PDF, serve directly
        $fileUrl = base_url('candidate/serve-resume');
        return $this->response->setJSON(['url' => $fileUrl]);
    }

    public function serveResume()
    {
        $userId = session()->get('user_id');
        $userModel = model('UserModel');
        $user = $userModel->find($userId);
        
        if (!$user || empty($user['resume_path'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Resume not found');
        }
        
        $filePath = WRITEPATH . $user['resume_path'];
        
        if (!file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Resume file not found');
        }
        
        $mimeType = mime_content_type($filePath);
        
        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline')
            ->setBody(file_get_contents($filePath));
    }

    public function updatePersonal()
    {
        $userId = session()->get('user_id');
        $userModel = model('UserModel');
        
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'location' => $this->request->getPost('location'),
            'bio' => $this->request->getPost('bio'),
            'work_experience' => $this->request->getPost('work_experience')
        ];
        
        $userModel->update($userId, $data);
        session()->set('user_name', $data['name']);
        
        return redirect()->back()->with('personal_success', 'Personal information updated successfully');
    }

    public function uploadPhoto()
    {
        $userId = session()->get('user_id');
        $file = $this->request->getFile('profile_photo');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'No file uploaded or invalid file');
        }
        
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (!in_array(strtolower($file->getExtension()), $allowedTypes)) {
            return redirect()->back()->with('error', 'Only JPG, PNG, GIF files allowed');
        }
        
        $uploadPath = FCPATH . 'uploads/profiles/';
        
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }
        
        $newName = $userId . '_' . time() . '.' . $file->getExtension();
        
        if (!$file->move($uploadPath, $newName)) {
            return redirect()->back()->with('error', 'File upload failed');
        }
        
        $userModel = model('UserModel');
        $userModel->update($userId, ['profile_photo' => 'uploads/profiles/' . $newName]);
        
        return redirect()->back()->with('success', 'Profile photo updated successfully');
    }

    public function addSkill()
    {
        $userId = session()->get('user_id');
        $skillName = $this->request->getPost('skill_name');
        
        if (empty($skillName)) {
            return redirect()->back()->with('error', 'Skill name is required');
        }
        
        $skillsModel = model('CandidateSkillsModel');
        $existingSkills = $skillsModel->where('candidate_id', $userId)->first();
        
        if ($existingSkills) {
            $currentSkills = $existingSkills['skill_name'];
            $updatedSkills = $currentSkills . ', ' . trim($skillName);
            
            $skillsModel->update($existingSkills['id'], [
                'skill_name' => $updatedSkills
            ]);
        } else {
            $skillsModel->insert([
                'candidate_id' => $userId,
                'skill_name' => trim($skillName)
            ]);
        }
        
        return redirect()->back()->with('success', 'Skill added successfully');
    }

    public function addWorkExperience()
    {
        $userId = session()->get('user_id');
        $workExpModel = new WorkExperienceModel();
        
        $data = [
            'user_id' => $userId,
            'job_title' => $this->request->getPost('job_title'),
            'company_name' => $this->request->getPost('company_name'),
            'employment_type' => $this->request->getPost('employment_type') ?: 'Full-time',
            'location' => $this->request->getPost('location'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'is_current' => $this->request->getPost('is_current') ? 1 : 0,
            'description' => $this->request->getPost('description')
        ];
        
        try {
            $id = $this->request->getPost('id');
            if ($id) {
                $workExpModel->update($id, $data);
            } else {
                $workExpModel->insert($data);
            }
            return redirect()->back()->with('success', 'Work experience saved successfully');
        } catch (\Exception $e) {
            log_message('error', 'Work experience save error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to save work experience: ' . $e->getMessage());
        }
    }

    public function deleteWorkExperience($id)
    {
        $userId = session()->get('user_id');
        $workExpModel = new WorkExperienceModel();
        
        $exp = $workExpModel->find($id);
        if ($exp && $exp['user_id'] == $userId) {
            $workExpModel->delete($id);
        }
        
        return redirect()->back()->with('success', 'Work experience deleted');
    }

    public function addEducation()
    {
        $userId = session()->get('user_id');
        $educationModel = new EducationModel();
        
        $data = [
            'user_id' => $userId,
            'degree' => $this->request->getPost('degree'),
            'field_of_study' => $this->request->getPost('field_of_study'),
            'institution' => $this->request->getPost('institution'),
            'start_year' => $this->request->getPost('start_year'),
            'end_year' => $this->request->getPost('end_year'),
            'grade' => $this->request->getPost('grade')
        ];
        
        try {
            $id = $this->request->getPost('id');
            if ($id) {
                $educationModel->update($id, $data);
            } else {
                $educationModel->insert($data);
            }
            return redirect()->back()->with('success', 'Education saved successfully');
        } catch (\Exception $e) {
            log_message('error', 'Education save error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to save education: ' . $e->getMessage());
        }
    }

    public function deleteEducation($id)
    {
        $userId = session()->get('user_id');
        $educationModel = new EducationModel();
        
        $edu = $educationModel->find($id);
        if ($edu && $edu['user_id'] == $userId) {
            $educationModel->delete($id);
        }
        
        return redirect()->back()->with('success', 'Education deleted');
    }

    public function addCertification()
    {
        $userId = session()->get('user_id');
        $certificationModel = new CertificationModel();
        
        $data = [
            'user_id' => $userId,
            'certification_name' => $this->request->getPost('certification_name'),
            'issuing_organization' => $this->request->getPost('issuing_organization'),
            'issue_date' => $this->request->getPost('issue_date'),
            'expiry_date' => $this->request->getPost('expiry_date'),
            'credential_id' => $this->request->getPost('credential_id'),
            'credential_url' => $this->request->getPost('credential_url')
        ];
        
        try {
            $id = $this->request->getPost('id');
            if ($id) {
                $certificationModel->update($id, $data);
            } else {
                $certificationModel->insert($data);
            }
            return redirect()->back()->with('success', 'Certification saved successfully');
        } catch (\Exception $e) {
            log_message('error', 'Certification save error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to save certification: ' . $e->getMessage());
        }
    }

    public function deleteCertification($id)
    {
        $userId = session()->get('user_id');
        $certificationModel = new CertificationModel();
        
        $cert = $certificationModel->find($id);
        if ($cert && $cert['user_id'] == $userId) {
            $certificationModel->delete($id);
        }
        
        return redirect()->back()->with('success', 'Certification deleted');
    }
}