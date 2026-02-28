<?php

namespace App\Controllers;

use App\Models\CompanyModel;
use App\Models\JobModel;

class Recruiter extends BaseController
{
    public function postJob()
    {
        $redirect = $this->ensureVerifiedRecruiter();
        if ($redirect !== null) {
            return $redirect;
        }

        return view('recruiter/post_job');
    }

    public function saveJob()
    {
        $redirect = $this->ensureVerifiedRecruiter();
        if ($redirect !== null) {
            return $redirect;
        }

        $session = session();

        $model = new JobModel();
        $userModel = model('UserModel');
        $companyModel = new CompanyModel();

        $title = trim((string) $this->request->getPost('title'));
        $category = trim((string) $this->request->getPost('category'));
        $description = trim((string) $this->request->getPost('description'));
        $location = trim((string) $this->request->getPost('location'));
        $requiredSkills = trim((string) $this->request->getPost('required_skills'));
        $experienceLevel = trim((string) $this->request->getPost('experience_level'));
        $aiInterviewPolicy = JobModel::normalizeAiPolicy($this->request->getPost('ai_interview_policy'));
        $minAiCutoffRaw = trim((string) $this->request->getPost('min_ai_cutoff_score'));
        $minAiCutoff = $minAiCutoffRaw === '' ? null : (int) $minAiCutoffRaw;
        $openings = (int) $this->request->getPost('openings');

        $currentUserId = (int) $session->get('user_id');
        $user = $userModel->find($currentUserId);
        $companyId = (int) ($user['company_id'] ?? 0);
        $companyRow = $companyId > 0 ? $companyModel->find($companyId) : null;
        $company = trim((string) ($companyRow['name'] ?? $user['company_name'] ?? ''));

        if ($title === '' || $category === '' || $description === '' || $location === '') {
            return redirect()->back()->withInput()->with('error', 'Title, category, description and location are required.');
        }

        if ($company === '') {
            return redirect()->back()->withInput()->with(
                'error',
                'Please set your company name in Company Profile before posting jobs.'
            );
        }

        if ($openings <= 0) {
            return redirect()->back()->withInput()->with('error', 'Openings must be greater than 0.');
        }

        if ($aiInterviewPolicy !== JobModel::AI_POLICY_OFF) {
            if ($minAiCutoff === null) {
                return redirect()->back()->withInput()->with('error', 'Minimum AI cutoff score is required when AI interview is enabled.');
            }

            if ($minAiCutoff < 0 || $minAiCutoff > 100) {
                return redirect()->back()->withInput()->with('error', 'Minimum AI cutoff score must be between 0 and 100.');
            }
        } else {
            $minAiCutoff = 0;
        }

        $riskReasons = $this->runAutoJobChecks(
            $currentUserId,
            $title,
            $company,
            $location,
            $description
        );
        if (!empty($riskReasons)) {
            return redirect()->back()->withInput()->with('error', 'Job blocked by automated checks: ' . implode(' | ', $riskReasons));
        }

        $data = [
            'title' => $title,
            'category' => $category,
            'company_id' => $companyId > 0 ? $companyId : null,
            'company' => $company,
            'description' => $description,
            'location' => $location,
            'required_skills' => $requiredSkills,
            'experience_level' => $experienceLevel,
            'min_ai_cutoff_score' => $minAiCutoff,
            'openings' => $openings,
            'recruiter_id' => $currentUserId,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Keep backward compatibility if DB is not migrated yet.
        $db = \Config\Database::connect();
        if ($db->fieldExists('ai_interview_policy', 'jobs')) {
            $data['ai_interview_policy'] = $aiInterviewPolicy;
        }

        $model->insert($data);
        $jobId = (int) $model->getInsertID();
        if ($jobId > 0) {
            $job = $model->find($jobId);
            if (!empty($job)) {
                (new \App\Libraries\JobAlertService())->processNewJob($job);
            }
        }

        return redirect()->to(base_url('recruiter/jobs'))->with('success', 'Job Posted Successfully');
    }

    private function ensureVerifiedRecruiter()
    {
        $session = session();
        if (!$session->get('logged_in')) {
            return redirect()->to(base_url('login'));
        }

        if ($session->get('role') !== 'recruiter') {
            return redirect()->to(base_url('candidate/dashboard'))->with('error', 'Only recruiters can access this page.');
        }

        $userModel = model('UserModel');
        $user = $userModel->find($session->get('user_id'));
        if (!$user) {
            return redirect()->to(base_url('login'))->with('error', 'User not found.');
        }

        if (empty($user['phone_verified_at'])) {
            return redirect()->to(base_url('recruiter/verification?email=' . urlencode((string) $user['email'])))
                ->with('error', 'Verify your phone OTP before posting jobs.');
        }

        return null;
    }

    private function runAutoJobChecks(int $recruiterId, string $title, string $company, string $location, string $description): array
    {
        $reasons = [];
        $jobModel = model('JobModel');
        $db = \Config\Database::connect();

        // 1) Limit number of jobs/day per recruiter.
        $todayCount = $jobModel
            ->where('recruiter_id', $recruiterId)
            ->where('DATE(created_at)', date('Y-m-d'))
            ->countAllResults();
        if ($todayCount >= 15) {
            $reasons[] = 'Daily posting limit reached (15 jobs/day).';
        }

        // 2) Duplicate detection for active postings.
        $duplicate = $jobModel
            ->where('recruiter_id', $recruiterId)
            ->where('LOWER(title)', strtolower($title))
            ->where('LOWER(company)', strtolower($company))
            ->where('LOWER(location)', strtolower($location))
            ->where('status', 'open')
            ->first();
        if ($duplicate) {
            $reasons[] = 'Duplicate active job detected for same title/company/location.';
        }

        // 3) Scam keyword detection.
        $content = strtolower($title . ' ' . $description);
        $blockedPhrases = [
            'pay to join',
            'registration fee',
            'security deposit',
            'earn money fast',
            'quick money',
            'investment required',
            'whatsapp only',
            'telegram only',
            'no interview direct joining'
        ];
        foreach ($blockedPhrases as $phrase) {
            if (str_contains($content, $phrase)) {
                $reasons[] = 'Blocked phrase detected: "' . $phrase . '"';
                break;
            }
        }

        // 4) Suspicious salary text detection in description.
        if (preg_match('/(salary|ctc|pay)\s*[:\-]?\s*(rs\.?|inr)?\s*([0-9]{1,9})/i', $description, $m)) {
            $amount = (int) ($m[3] ?? 0);
            if ($amount > 50000000 || ($amount > 0 && $amount < 3000)) {
                $reasons[] = 'Suspicious salary amount detected.';
            }
        }

        return $reasons;
    }
}
