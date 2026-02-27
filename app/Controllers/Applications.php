<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Models\JobModel;

class Applications extends BaseController
{
    public function apply($jobId)
    {
        $session = session();

        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        $candidateId = $session->get('user_id');
        
        // Check if resume is uploaded
        $userModel = model('UserModel');
        $user = $userModel->find($candidateId);
        
        if (empty($user['resume_path'])) {
            return redirect()->to(base_url('candidate/profile'))->with('error', 'Please upload your resume to continue your job application. You have been redirected to your profile page.');
        }
        
        $model = new ApplicationModel();

        $alreadyApplied = $model
            ->where('job_id', $jobId)
            ->where('candidate_id', $candidateId)
            ->first();

        if ($alreadyApplied) {
            return redirect()->back()->with('error', 'You have already applied for this job');
        }

        // Check skill mismatch - compare with resume AND github skills
        $jobModel = model('JobModel');
        $skillsModel = model('CandidateSkillsModel');
        $githubModel = model('GithubAnalysisModel');
        
        $job = $jobModel->find($jobId);
        $candidateSkills = $skillsModel->where('candidate_id', $candidateId)->first();
        $githubStats = $githubModel->where('candidate_id', $candidateId)->first();
        
        $jobTitle = strtolower($job['title'] ?? '');
        $jobSkills = strtolower($job['required_skills'] ?? '');
        $resumeSkills = strtolower($candidateSkills['skill_name'] ?? '');
        $githubLanguages = strtolower($githubStats['languages_used'] ?? '');
        
        // Combine resume and github skills
        $allCandidateSkills = $resumeSkills . ' ' . $githubLanguages;
        
        // Detect mismatch: job requires skills candidate doesn't have
        $hasJobTitleSkill = stripos($allCandidateSkills, $jobTitle) !== false;
        $hasRequiredSkills = false;
        
        // Check if candidate has any of the required skills
        $requiredSkillsList = explode(',', $jobSkills);
        foreach ($requiredSkillsList as $skill) {
            $skill = trim($skill);
            if (!empty($skill) && stripos($allCandidateSkills, $skill) !== false) {
                $hasRequiredSkills = true;
                break;
            }
        }
        
        $mismatch = !empty($jobTitle) && !empty($allCandidateSkills) && 
                    (!$hasJobTitleSkill && !$hasRequiredSkills);

        $aiPolicy = JobModel::normalizeAiPolicy($job['ai_interview_policy'] ?? JobModel::AI_POLICY_REQUIRED_HARD);
        $initialStatus = $aiPolicy === JobModel::AI_POLICY_OFF ? 'shortlisted' : 'applied';

        $model->insert([
            'job_id' => $jobId,
            'candidate_id' => $candidateId,
            'status' => $initialStatus,
            'applied_at' => date('Y-m-d H:i:s')
        ]);
        
        $applicationId = $model->getInsertID();
        $stageModel = model('StageHistoryModel');
        $stageModel->moveToStage($applicationId, 'Applied');
        if ($initialStatus === 'shortlisted') {
            $stageModel->moveToStage($applicationId, 'Shortlisted (AI Policy OFF)');
        }

        if ($mismatch) {
            // Store multiple suggestions as array
            $suggestions = $session->get('career_suggestions') ?? [];
            
            // Check if this job title already suggested
            $alreadySuggested = false;
            foreach ($suggestions as $existing) {
                if ($existing['job_title'] === $job['title']) {
                    $alreadySuggested = true;
                    break;
                }
            }
            
            // Add new suggestion if not already present
            if (!$alreadySuggested) {
                $suggestions[] = [
                    'job_title' => $job['title'],
                    'created_at' => time(),
                    'expires_at' => time() + (2 * 24 * 60 * 60)
                ];
                $session->set('career_suggestions', $suggestions);
            }
            
            return redirect()->to('candidate/dashboard')->with('success', $this->getApplySuccessMessage($aiPolicy));
        }

        return redirect()->back()->with('success', $this->getApplySuccessMessage($aiPolicy));
    }

    private function getApplySuccessMessage(string $aiPolicy): string
    {
        if ($aiPolicy === JobModel::AI_POLICY_OFF) {
            return 'Job applied successfully. This job skips AI interview and moved to shortlist stage.';
        }

        if ($aiPolicy === JobModel::AI_POLICY_OPTIONAL) {
            return 'Job applied successfully. AI interview is optional for this job.';
        }

        return 'Job applied successfully';
    }
}
