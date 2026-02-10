<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\WorkExperienceModel;
use App\Models\EducationModel;
use App\Models\CertificationModel;
use App\Models\CandidateSkillsModel;
use App\Models\GithubAnalysisModel;

class RecruiterCandidates extends BaseController
{
    public function viewProfile($candidateId)
    {
        $userModel = new UserModel();
        $candidate = $userModel->find($candidateId);
        
        if (!$candidate || $candidate['role'] !== 'candidate') {
            return redirect()->back()->with('error', 'Candidate not found');
        }
        
        $workExpModel = new WorkExperienceModel();
        $educationModel = new EducationModel();
        $certificationModel = new CertificationModel();
        $skillsModel = new CandidateSkillsModel();
        $githubModel = new GithubAnalysisModel();
        
        $workExperiences = $workExpModel->getByUser($candidateId);
        $education = $educationModel->getByUser($candidateId);
        $certifications = $certificationModel->getByUser($candidateId);
        $skills = $skillsModel->where('candidate_id', $candidateId)->first();
        $github = $githubModel->where('candidate_id', $candidateId)->first();
        
        return view('recruiter/candidate_profile', [
            'candidate' => $candidate,
            'workExperiences' => $workExperiences,
            'education' => $education,
            'certifications' => $certifications,
            'skills' => $skills,
            'github' => $github
        ]);
    }
}
