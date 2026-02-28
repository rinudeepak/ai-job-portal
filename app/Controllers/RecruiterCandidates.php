<?php

namespace App\Controllers;

use App\Libraries\ResumeTemplateRenderer;
use App\Models\UserModel;
use App\Models\ApplicationModel;
use App\Models\CandidateResumeVersionModel;
use App\Models\WorkExperienceModel;
use App\Models\EducationModel;
use App\Models\CertificationModel;
use App\Models\CandidateSkillsModel;
use App\Models\GithubAnalysisModel;
use App\Models\RecruiterCandidateActionModel;
use App\Models\NotificationModel;
use App\Models\RecruiterCandidateMessageModel;
use App\Models\RecruiterCandidateNoteModel;

class RecruiterCandidates extends BaseController
{
    private const ACTION_DEDUPE_HOURS = 24;

    public function index()
    {
        if (session()->get('role') !== 'recruiter') {
            return redirect()->to(base_url('login'))->with('error', 'Unauthorized');
        }

        $userModel = model('UserModel');
        $filters = [
            'keyword' => trim((string) $this->request->getGet('keyword')),
            'skills' => trim((string) $this->request->getGet('skills')),
            'location' => trim((string) $this->request->getGet('location')),
            'exp_min' => trim((string) $this->request->getGet('exp_min')),
            'exp_max' => trim((string) $this->request->getGet('exp_max')),
            'resume' => trim((string) $this->request->getGet('resume')),
        ];

        $expMinYears = is_numeric($filters['exp_min']) ? max(0, (float) $filters['exp_min']) : null;
        $expMaxYears = is_numeric($filters['exp_max']) ? max(0, (float) $filters['exp_max']) : null;
        if ($expMinYears !== null && $expMaxYears !== null && $expMinYears > $expMaxYears) {
            [$expMinYears, $expMaxYears] = [$expMaxYears, $expMinYears];
        }
        $expMinMonths = $expMinYears !== null ? (int) round($expMinYears * 12) : null;
        $expMaxMonths = $expMaxYears !== null ? (int) round($expMaxYears * 12) : null;

        if (!in_array($filters['resume'], ['', 'yes', 'no'], true)) {
            $filters['resume'] = '';
        }

        $experienceSubQuery = '(SELECT user_id, SUM(TIMESTAMPDIFF(MONTH, start_date, COALESCE(NULLIF(end_date, \'\'), CURDATE()))) AS total_experience_months FROM work_experiences GROUP BY user_id) candidate_experience';

        $builder = $userModel
            ->select('users.id, users.name, users.email, users.location, users.resume_path, users.profile_photo, users.created_at, MAX(candidate_skills.skill_name) as skill_name, COALESCE(candidate_experience.total_experience_months, 0) as total_experience_months')
            ->join('candidate_skills', 'candidate_skills.candidate_id = users.id', 'left')
            ->join($experienceSubQuery, 'candidate_experience.user_id = users.id', 'left', false)
            ->where('users.role', 'candidate')
            ->groupBy('users.id')
            ->orderBy('users.created_at', 'DESC');

        if ($filters['keyword'] !== '') {
            $builder->groupStart()
                ->like('users.name', $filters['keyword'])
                ->orLike('users.email', $filters['keyword'])
                ->orLike('candidate_skills.skill_name', $filters['keyword'])
                ->groupEnd();
        }

        if ($filters['skills'] !== '') {
            $builder->like('candidate_skills.skill_name', $filters['skills']);
        }

        if ($filters['location'] !== '') {
            $builder->like('users.location', $filters['location']);
        }

        if ($expMinMonths !== null) {
            $builder->where('COALESCE(candidate_experience.total_experience_months, 0) >= ' . $expMinMonths, null, false);
        }

        if ($expMaxMonths !== null) {
            $builder->where('COALESCE(candidate_experience.total_experience_months, 0) <= ' . $expMaxMonths, null, false);
        }

        if ($filters['resume'] === 'yes') {
            $builder->where('users.resume_path IS NOT NULL', null, false)
                ->where('users.resume_path <>', '');
        } elseif ($filters['resume'] === 'no') {
            $builder->groupStart()
                ->where('users.resume_path IS NULL', null, false)
                ->orWhere('users.resume_path =', '')
                ->groupEnd();
        }

        $candidates = $builder->paginate(12);
        $pager = $userModel->pager;

        foreach ($candidates as &$candidate) {
            $candidate['experience_display'] = $this->formatExperienceDisplay((int) ($candidate['total_experience_months'] ?? 0));
        }
        unset($candidate);

        return view('recruiter/candidates/index', [
            'candidates' => $candidates,
            'pager' => $pager,
            'filters' => [
                'keyword' => $filters['keyword'],
                'skills' => $filters['skills'],
                'location' => $filters['location'],
                'exp_min' => $expMinYears !== null ? (string) $expMinYears : '',
                'exp_max' => $expMaxYears !== null ? (string) $expMaxYears : '',
                'resume' => $filters['resume'],
            ],
        ]);
    }

    public function viewProfile($candidateId)
    {
        if (session()->get('role') !== 'recruiter') {
            return redirect()->to(base_url('login'))->with('error', 'Unauthorized');
        }

        $userModel = new UserModel();
        $candidate = $userModel->find($candidateId);
        
        if (!$candidate || $candidate['role'] !== 'candidate') {
            return redirect()->back()->with('error', 'Candidate not found');
        }

        $applicationId = (int) ($this->request->getGet('application_id') ?? 0);
        $jobId = (int) ($this->request->getGet('job_id') ?? 0);
        $recruiterId = (int) session()->get('user_id');
        $recruiterName = (string) (session()->get('user_name') ?? 'A recruiter');
        $actionModel = new RecruiterCandidateActionModel();

        $wasLogged = $actionModel->logAction(
            (int) $candidateId,
            $recruiterId,
            RecruiterCandidateActionModel::ACTION_PROFILE_VIEWED,
            $applicationId > 0 ? $applicationId : null,
            $jobId > 0 ? $jobId : null,
            self::ACTION_DEDUPE_HOURS
        );

        if ($wasLogged) {
            $this->notifyCandidateAction(
                (int) $candidateId,
                $applicationId > 0 ? $applicationId : null,
                'recruiter_profile_viewed',
                'Profile Viewed',
                "{$recruiterName} viewed your profile."
            );
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
        $messages = (new RecruiterCandidateMessageModel())->getThread(
            (int) $candidateId,
            (int) $recruiterId,
            $applicationId > 0 ? $applicationId : null
        );
        $recruiterNote = (new RecruiterCandidateNoteModel())->getByCandidateAndRecruiter(
            (int) $candidateId,
            (int) $recruiterId
        );
        
        return view('recruiter/candidate_profile', [
            'candidate' => $candidate,
            'workExperiences' => $workExperiences,
            'education' => $education,
            'certifications' => $certifications,
            'skills' => $skills,
            'github' => $github,
            'messages' => $messages,
            'recruiterNote' => $recruiterNote,
        ]);
    }

    public function viewContact($candidateId)
    {
        if (session()->get('role') !== 'recruiter') {
            return redirect()->to(base_url('login'))->with('error', 'Unauthorized');
        }

        $candidate = (new UserModel())->find($candidateId);
        if (!$candidate || $candidate['role'] !== 'candidate') {
            return redirect()->back()->with('error', 'Candidate not found');
        }

        $applicationId = (int) ($this->request->getGet('application_id') ?? 0);
        $jobId = (int) ($this->request->getGet('job_id') ?? 0);

        $wasLogged = (new RecruiterCandidateActionModel())->logAction(
            (int) $candidateId,
            (int) session()->get('user_id'),
            RecruiterCandidateActionModel::ACTION_CONTACT_VIEWED,
            $applicationId > 0 ? $applicationId : null,
            $jobId > 0 ? $jobId : null,
            self::ACTION_DEDUPE_HOURS
        );

        if ($wasLogged) {
            $recruiterName = (string) (session()->get('user_name') ?? 'A recruiter');
            $this->notifyCandidateAction(
                (int) $candidateId,
                $applicationId > 0 ? $applicationId : null,
                'recruiter_contact_viewed',
                'Contact Viewed',
                "{$recruiterName} viewed your contact details."
            );
        }

        $redirectUrl = base_url('recruiter/candidate/' . $candidateId)
            . '?show_contact=1'
            . '&application_id=' . $applicationId
            . '&job_id=' . $jobId;

        return redirect()->to($redirectUrl);
    }

    public function downloadResume($candidateId)
    {
        if (session()->get('role') !== 'recruiter') {
            return redirect()->to(base_url('login'))->with('error', 'Unauthorized');
        }

        $candidate = (new UserModel())->find($candidateId);
        if (!$candidate || $candidate['role'] !== 'candidate') {
            return redirect()->back()->with('error', 'Resume not found.');
        }

        $applicationId = (int) ($this->request->getGet('application_id') ?? 0);
        $jobId = (int) ($this->request->getGet('job_id') ?? 0);
        $submittedResumeVersion = $this->getSubmittedResumeVersion((int) $candidateId, $applicationId);

        $wasLogged = (new RecruiterCandidateActionModel())->logAction(
            (int) $candidateId,
            (int) session()->get('user_id'),
            RecruiterCandidateActionModel::ACTION_RESUME_DOWNLOADED,
            $applicationId > 0 ? $applicationId : null,
            $jobId > 0 ? $jobId : null,
            self::ACTION_DEDUPE_HOURS
        );

        if ($wasLogged) {
            $recruiterName = (string) (session()->get('user_name') ?? 'A recruiter');
            $this->notifyCandidateAction(
                (int) $candidateId,
                $applicationId > 0 ? $applicationId : null,
                'recruiter_resume_downloaded',
                'Resume Downloaded',
                "{$recruiterName} downloaded your resume."
            );
        }

        if ($submittedResumeVersion) {
            $renderer = new ResumeTemplateRenderer();
            $pdfPath = $renderer->createPdfFile((string) ($submittedResumeVersion['content'] ?? ''), [
                'name' => (string) ($candidate['name'] ?? 'Candidate'),
                'target_role' => (string) ($submittedResumeVersion['target_role'] ?? ''),
                'summary' => (string) ($submittedResumeVersion['summary'] ?? ''),
                'highlight_skills' => array_values(array_filter(array_map('trim', explode(',', (string) ($submittedResumeVersion['highlight_skills'] ?? ''))))),
            ], (string) (($candidate['name'] ?? 'candidate') . '-' . ($submittedResumeVersion['target_role'] ?? 'resume')));

            return $this->response->download($pdfPath, null)->setFileName(basename($pdfPath));
        }

        if (empty($candidate['resume_path'])) {
            return redirect()->back()->with('error', 'Resume file not found.');
        }

        $filePath = WRITEPATH . $candidate['resume_path'];
        if (!is_file($filePath)) {
            return redirect()->back()->with('error', 'Resume file not found.');
        }

        return $this->response->download($filePath, null);
    }

    public function sendMessage($candidateId)
    {
        if (session()->get('role') !== 'recruiter') {
            return redirect()->to(base_url('login'))->with('error', 'Unauthorized');
        }

        $candidate = (new UserModel())->find($candidateId);
        if (!$candidate || $candidate['role'] !== 'candidate') {
            return redirect()->back()->with('error', 'Candidate not found');
        }

        $message = trim((string) $this->request->getPost('message'));
        $applicationId = (int) ($this->request->getPost('application_id') ?? 0);
        $jobId = (int) ($this->request->getPost('job_id') ?? 0);

        if ($message === '') {
            return redirect()->back()->with('error', 'Message cannot be empty.');
        }

        if (mb_strlen($message) > 1000) {
            return redirect()->back()->with('error', 'Message is too long. Max 1000 characters.');
        }

        $recruiterName = (string) (session()->get('user_name') ?? 'Recruiter');
        $messageModel = new RecruiterCandidateMessageModel();

        $messageModel->insert([
            'candidate_id' => (int) $candidateId,
            'recruiter_id' => (int) session()->get('user_id'),
            'application_id' => $applicationId > 0 ? $applicationId : null,
            'job_id' => $jobId > 0 ? $jobId : null,
            'sender_id' => (int) session()->get('user_id'),
            'sender_role' => 'recruiter',
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        $this->notifyCandidateAction(
            (int) $candidateId,
            $applicationId > 0 ? $applicationId : null,
            'recruiter_message',
            'Message from Recruiter',
            "{$recruiterName} sent you a message. Open conversation to read it.",
            base_url('candidate/messages/' . (int) session()->get('user_id') . ($applicationId > 0 ? '?application_id=' . $applicationId : ''))
        );

        $redirectUrl = base_url('recruiter/candidate/' . $candidateId)
            . '?application_id=' . $applicationId
            . '&job_id=' . $jobId
            . '&show_contact=' . (int) ($this->request->getPost('show_contact') ?? 0);

        return redirect()->to($redirectUrl)->with('success', 'Message sent to candidate.');
    }

    public function saveNotes($candidateId)
    {
        if (session()->get('role') !== 'recruiter') {
            return redirect()->to(base_url('login'))->with('error', 'Unauthorized');
        }

        $candidate = (new UserModel())->find($candidateId);
        if (!$candidate || $candidate['role'] !== 'candidate') {
            return redirect()->back()->with('error', 'Candidate not found');
        }

        $recruiterId = (int) session()->get('user_id');
        $rawTags = trim((string) $this->request->getPost('tags'));
        $notes = trim((string) $this->request->getPost('notes'));

        if (mb_strlen($rawTags) > 255) {
            return redirect()->back()->with('error', 'Tags are too long. Max 255 characters.');
        }

        if (mb_strlen($notes) > 5000) {
            return redirect()->back()->with('error', 'Notes are too long. Max 5000 characters.');
        }

        $tags = $this->normalizeTags($rawTags);
        $noteModel = new RecruiterCandidateNoteModel();
        $existing = $noteModel->getByCandidateAndRecruiter((int) $candidateId, $recruiterId);

        $data = [
            'candidate_id' => (int) $candidateId,
            'recruiter_id' => $recruiterId,
            'tags' => $tags,
            'notes' => $notes,
        ];

        if ($existing) {
            $noteModel->update((int) $existing['id'], $data);
        } else {
            $noteModel->insert($data);
        }

        $applicationId = (int) ($this->request->getPost('application_id') ?? 0);
        $jobId = (int) ($this->request->getPost('job_id') ?? 0);
        $showContact = (int) ($this->request->getPost('show_contact') ?? 0);

        $redirectUrl = base_url('recruiter/candidate/' . $candidateId)
            . '?application_id=' . $applicationId
            . '&job_id=' . $jobId
            . '&show_contact=' . $showContact;

        return redirect()->to($redirectUrl)->with('success', 'Recruiter notes saved.');
    }

    private function notifyCandidateAction(
        int $candidateId,
        ?int $applicationId,
        string $type,
        string $title,
        string $message,
        ?string $actionLink = null
    ): void {
        $notificationModel = new NotificationModel();
        $notificationModel->insert([
            'user_id' => $candidateId,
            'application_id' => $applicationId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'action_link' => $actionLink ?? base_url('candidate/applications'),
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function normalizeTags(string $rawTags): string
    {
        if ($rawTags === '') {
            return '';
        }

        $parts = preg_split('/[,]+/', $rawTags) ?: [];
        $clean = [];
        foreach ($parts as $part) {
            $tag = trim($part);
            if ($tag === '') {
                continue;
            }
            if (mb_strlen($tag) > 40) {
                $tag = mb_substr($tag, 0, 40);
            }
            $clean[] = $tag;
        }

        $unique = [];
        $seen = [];
        foreach ($clean as $tag) {
            $key = mb_strtolower($tag);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = $tag;
        }

        return implode(', ', $unique);
    }

    private function formatExperienceDisplay(int $months): string
    {
        if ($months <= 0) {
            return '-';
        }

        $years = intdiv($months, 12);
        $remainingMonths = $months % 12;

        if ($years > 0 && $remainingMonths > 0) {
            return $years . 'y ' . $remainingMonths . 'm';
        }

        if ($years > 0) {
            return $years . 'y';
        }

        return $remainingMonths . 'm';
    }

    private function getSubmittedResumeVersion(int $candidateId, int $applicationId): ?array
    {
        $db = \Config\Database::connect();
        if ($applicationId <= 0 || !$db->tableExists('candidate_resume_versions') || !$db->fieldExists('resume_version_id', 'applications')) {
            return null;
        }

        $application = (new ApplicationModel())
            ->select('applications.id, applications.resume_version_id, jobs.recruiter_id')
            ->join('jobs', 'jobs.id = applications.job_id', 'inner')
            ->where('applications.id', $applicationId)
            ->where('applications.candidate_id', $candidateId)
            ->where('jobs.recruiter_id', (int) session()->get('user_id'))
            ->first();

        if (!$application || empty($application['resume_version_id'])) {
            return null;
        }

        return (new CandidateResumeVersionModel())->find((int) $application['resume_version_id']);
    }
}
