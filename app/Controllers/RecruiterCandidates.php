<?php

namespace App\Controllers;

use App\Models\UserModel;
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
        if (!$candidate || $candidate['role'] !== 'candidate' || empty($candidate['resume_path'])) {
            return redirect()->back()->with('error', 'Resume not found.');
        }

        $filePath = WRITEPATH . $candidate['resume_path'];
        if (!is_file($filePath)) {
            return redirect()->back()->with('error', 'Resume file not found.');
        }

        $applicationId = (int) ($this->request->getGet('application_id') ?? 0);
        $jobId = (int) ($this->request->getGet('job_id') ?? 0);

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
}
