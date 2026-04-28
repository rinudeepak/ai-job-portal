<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\AtsScoreService;
use App\Models\NotificationModel;
use App\Models\RecruiterCandidateMessageModel;

class RecruiterApplications extends BaseController
{
    public function index()
    {
        if (session()->get('role') !== 'recruiter') {
            return redirect()->to(base_url('candidate/dashboard'))->with('error', 'Access denied.');
        }
        
        return redirect()->to(base_url('recruiter/jobs'));
    }

    public function viewByJob($jobId)
    {
        $jobModel = model('JobModel');
        $applicationModel = model('ApplicationModel');
        $currentUserId = session()->get('user_id');

        // Verify job belongs to recruiter
        $job = $jobModel->where('id', $jobId)->where('recruiter_id', $currentUserId)->first();
        if (!$job) {
            return redirect()->to('recruiter/jobs')->with('error', 'Job not found');
        }
        $filters = [
            'skills' => trim((string) $this->request->getGet('skills')),
            'experience' => trim((string) $this->request->getGet('experience')),
            'location' => trim((string) $this->request->getGet('location')),
            'status' => trim((string) $this->request->getGet('status')),
            'score_min' => $this->request->getGet('score_min'),
            'score_max' => $this->request->getGet('score_max'),
            'ats_min' => $this->request->getGet('ats_min'),
            'ats_max' => $this->request->getGet('ats_max'),
            'sort' => trim((string) $this->request->getGet('sort')),
        ];

        $scoreMin = is_numeric($filters['score_min']) ? (float) $filters['score_min'] : null;
        $scoreMax = is_numeric($filters['score_max']) ? (float) $filters['score_max'] : null;
        if ($scoreMin !== null) {
            $scoreMin = max(0, min(10, $scoreMin));
        }
        if ($scoreMax !== null) {
            $scoreMax = max(0, min(10, $scoreMax));
        }
        if ($scoreMin !== null && $scoreMax !== null && $scoreMin > $scoreMax) {
            [$scoreMin, $scoreMax] = [$scoreMax, $scoreMin];
        }
        $atsMin = is_numeric($filters['ats_min']) ? (int) $filters['ats_min'] : null;
        $atsMax = is_numeric($filters['ats_max']) ? (int) $filters['ats_max'] : null;
        if ($atsMin !== null) {
            $atsMin = max(0, min(100, $atsMin));
        }
        if ($atsMax !== null) {
            $atsMax = max(0, min(100, $atsMax));
        }
        if ($atsMin !== null && $atsMax !== null && $atsMin > $atsMax) {
            [$atsMin, $atsMax] = [$atsMax, $atsMin];
        }
        $validSort = ['applied_desc', 'ats_desc', 'ats_asc'];
        if (!in_array($filters['sort'], $validSort, true)) {
            $filters['sort'] = 'applied_desc';
        }

        $validStatuses = [
            'applied',
            'pending',
            'shortlisted',
            'interview_slot_booked',
            'selected',
            'rejected',
        ];
        if ($filters['status'] !== '' && !in_array($filters['status'], $validStatuses, true)) {
            $filters['status'] = '';
        }

        // Pre-aggregate total experience (months) per candidate from work_experiences.
        $experienceSubQuery = '(SELECT user_id, SUM(TIMESTAMPDIFF(MONTH, start_date, COALESCE(NULLIF(end_date, \'\'), CURDATE()))) AS total_experience_months FROM work_experiences GROUP BY user_id) candidate_experience';

        $db = \Config\Database::connect();
        $resumeSkillsSelect = $db->tableExists('candidate_resume_versions') 
            ? ', candidate_resume_versions.highlight_skills as resume_version_skills' 
            : ', "" as resume_version_skills';

        // Get applications for this job with optional filters
        $builder = $applicationModel
            ->select('applications.*, users.name, users.email, candidate_profiles.location as candidate_location, candidate_profiles.resume_path as resume_path, 
                    candidate_profiles.preferred_job_titles, candidate_profiles.preferred_locations, candidate_profiles.preferred_employment_type, candidate_profiles.key_skills,
                    0 as overall_rating, candidate_skills.skill_name, ' . $resumeSkillsSelect . ',
                    COALESCE(candidate_experience.total_experience_months, 0) as total_experience_months, recruiter_candidate_notes.tags as recruiter_tags, recruiter_candidate_notes.notes as recruiter_notes')
            ->join('users', 'users.id = applications.candidate_id', 'left')
            ->join('candidate_profiles', 'candidate_profiles.user_id = applications.candidate_id', 'left')
            ->join('candidate_skills', 'candidate_skills.candidate_id = applications.candidate_id', 'left')
            ->join('candidate_resume_versions', 'candidate_resume_versions.id = applications.resume_version_id', 'left')
            ->join(
                'recruiter_candidate_notes',
                'recruiter_candidate_notes.candidate_id = applications.candidate_id AND recruiter_candidate_notes.recruiter_id = ' . (int) $currentUserId,
                'left',
                false
            )
            ->join($experienceSubQuery, 'candidate_experience.user_id = applications.candidate_id', 'left', false)
            ->where('applications.job_id', $jobId);

        if ($filters['skills'] !== '') {
            $builder->like('candidate_skills.skill_name', $filters['skills']);
        }

        if ($filters['experience'] !== '') {
            preg_match('/\d+(\.\d+)?/', $filters['experience'], $matches);
            if (!empty($matches[0])) {
                $minMonths = (int) round(((float) $matches[0]) * 12);
                $builder->where('COALESCE(candidate_experience.total_experience_months, 0) >= ' . $minMonths, null, false);
            }
        }

        if ($filters['location'] !== '') {
            $builder->where(
                'candidate_profiles.location LIKE ' . $builder->db->escape('%' . $filters['location'] . '%'),
                null,
                false
            );
        }

        if ($filters['status'] !== '') {
            $builder->where('applications.status', $filters['status']);
        }

        if ($scoreMin !== null && $scoreMin > 0) {
            $builder->where('1 = 0', null, false);
        }

        $applications = $builder
            ->groupBy('applications.id')
            ->orderBy('applications.applied_at', 'DESC')
            ->findAll();

        $applicationIds = array_values(array_filter(array_map(static fn (array $application): int => (int) ($application['id'] ?? 0), $applications)));
        $atsScoreService = new AtsScoreService();
        foreach ($applications as &$application) {
            $months = (int) ($application['total_experience_months'] ?? 0);
            if ($months <= 0) {
                $application['experience_display'] = '-';
            } else {
                $years = floor($months / 12);
                $remainingMonths = $months % 12;

                if ($years > 0 && $remainingMonths > 0) {
                    $application['experience_display'] = $years . 'y ' . $remainingMonths . 'm';
                } elseif ($years > 0) {
                    $application['experience_display'] = $years . 'y';
                } else {
                    $application['experience_display'] = $remainingMonths . 'm';
                }
            }
            $analysis = $atsScoreService->analyzeCandidateJob(
                (int) ($application['candidate_id'] ?? 0),
                $job,
                (int) ($application['resume_version_id'] ?? 0)
            );
            $application['ats_score'] = (int) ($analysis['score'] ?? 0);
            $application['can_manual_decision'] = $this->canTakeManualDecision((string) ($application['status'] ?? ''));
            $application['questionnaire_preview'] = $this->buildQuestionnairePreview(
                (string) ($application['questionnaire_responses'] ?? '')
            );
        }
        unset($application);

        if ($atsMin !== null || $atsMax !== null) {
            $applications = array_values(array_filter($applications, static function (array $application) use ($atsMin, $atsMax): bool {
                $score = (int) ($application['ats_score'] ?? 0);
                if ($atsMin !== null && $score < $atsMin) {
                    return false;
                }
                if ($atsMax !== null && $score > $atsMax) {
                    return false;
                }
                return true;
            }));
        }

        if ($filters['sort'] === 'ats_desc') {
            usort($applications, static fn (array $a, array $b) => ((int) ($b['ats_score'] ?? 0)) <=> ((int) ($a['ats_score'] ?? 0)));
        } elseif ($filters['sort'] === 'ats_asc') {
            usort($applications, static fn (array $a, array $b) => ((int) ($a['ats_score'] ?? 0)) <=> ((int) ($b['ats_score'] ?? 0)));
        } else {
            usort($applications, static fn (array $a, array $b) => strcmp((string) ($b['applied_at'] ?? ''), (string) ($a['applied_at'] ?? '')));
        }

        $filters['score_min'] = $scoreMin !== null ? (string) $scoreMin : '';
        $filters['score_max'] = $scoreMax !== null ? (string) $scoreMax : '';
        $filters['ats_min'] = $atsMin !== null ? (string) $atsMin : '';
        $filters['ats_max'] = $atsMax !== null ? (string) $atsMax : '';

        return view('recruiter/applications/view_by_job', [
            'job' => $job,
            'applications' => $applications,
            'filters' => $filters,
            'statusOptions' => $validStatuses,
            'unread_count' => model('NotificationModel')->getUnreadCount($currentUserId)
        ]);
    }

    public function shortlist($applicationId)
    {
        return $this->updateApplicationStatus($applicationId, 'shortlisted');
    }

    private function buildQuestionnairePreview(string $rawResponses): string
    {
        if (trim($rawResponses) === '') {
            return '';
        }

        $decoded = json_decode($rawResponses, true);
        if (!is_array($decoded)) {
            return '';
        }

        $parts = [];
        foreach ($decoded as $row) {
            if (!is_array($row)) {
                continue;
            }

            $label = trim((string) ($row['label'] ?? ''));
            $answer = trim((string) ($row['answer'] ?? ''));
            if ($label === '' || $answer === '') {
                continue;
            }

            $parts[] = $label . ': ' . $answer;
            if (count($parts) >= 2) {
                break;
            }
        }

        return implode(' | ', $parts);
    }

    public function reject($applicationId)
    {
        return $this->updateApplicationStatus($applicationId, 'rejected');
    }

    public function bulkAction($jobId)
    {
        $applicationModel = model('ApplicationModel');
        $jobModel = model('JobModel');
        $stageModel = model('StageHistoryModel');
        $currentUserId = (int) session()->get('user_id');

        $job = $jobModel->where('id', (int) $jobId)->where('recruiter_id', $currentUserId)->first();
        if (!$job) {
            return redirect()->to(base_url('recruiter/jobs'))->with('error', 'Job not found');
        }

        $applicationIds = $this->request->getPost('application_ids');
        $bulkAction = trim((string) $this->request->getPost('bulk_action'));
        $messageText = trim((string) $this->request->getPost('bulk_message'));

        if (!is_array($applicationIds) || empty($applicationIds)) {
            return redirect()->back()->with('error', 'Please select at least one candidate.');
        }

        $applicationIds = array_values(array_unique(array_map('intval', $applicationIds)));
        $applicationIds = array_filter($applicationIds, static fn ($id) => $id > 0);

        if (empty($applicationIds)) {
            return redirect()->back()->with('error', 'Invalid selection.');
        }

        if (!in_array($bulkAction, ['shortlist', 'reject', 'message'], true)) {
            return redirect()->back()->with('error', 'Invalid bulk action.');
        }

        $applications = $applicationModel
            ->select('applications.*, jobs.recruiter_id, users.name as candidate_name')
            ->join('jobs', 'jobs.id = applications.job_id')
            ->join('users', 'users.id = applications.candidate_id', 'left')
            ->where('applications.job_id', (int) $jobId)
            ->whereIn('applications.id', $applicationIds)
            ->findAll();

        if (empty($applications)) {
            return redirect()->back()->with('error', 'No matching applications found.');
        }

        if ($bulkAction === 'message') {
            if ($messageText === '') {
                return redirect()->back()->with('error', 'Please enter a message for selected candidates.');
            }
            if (mb_strlen($messageText) > 1000) {
                return redirect()->back()->with('error', 'Message is too long. Max 1000 characters.');
            }

            $messageModel = new RecruiterCandidateMessageModel();
            $notificationModel = new NotificationModel();
            $recruiterName = (string) (session()->get('user_name') ?? 'Recruiter');
            $sent = 0;

            foreach ($applications as $application) {
                if ((int) $application['recruiter_id'] !== $currentUserId) {
                    continue;
                }

                $messageModel->insert([
                    'candidate_id' => (int) $application['candidate_id'],
                    'recruiter_id' => $currentUserId,
                    'application_id' => (int) $application['id'],
                    'job_id' => (int) $application['job_id'],
                    'sender_id' => $currentUserId,
                    'sender_role' => 'recruiter',
                    'message' => $messageText,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);

                $notificationModel->insert([
                    'user_id' => (int) $application['candidate_id'],
                    'application_id' => (int) $application['id'],
                    'type' => 'recruiter_message',
                    'title' => 'Message from Recruiter',
                    'message' => "{$recruiterName} sent you a message. Open conversation to read it.",
                    'action_link' => base_url('candidate/messages/' . $currentUserId . '?application_id=' . (int) $application['id']),
                    'is_read' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $sent++;
            }

            return redirect()->back()->with('success', "Message sent to {$sent} selected candidate(s).");
        }

        $targetStatus = $bulkAction === 'shortlist' ? 'shortlisted' : 'rejected';
        $updated = 0;
        $skipped = 0;
        $notificationModel = new NotificationModel();

        foreach ($applications as $application) {
            if ((int) $application['recruiter_id'] !== $currentUserId) {
                $skipped++;
                continue;
            }

            if (($application['status'] ?? '') === 'interview_slot_booked') {
                $skipped++;
                continue;
            }

            if (!$this->canTakeManualDecision((string) ($application['status'] ?? ''))) {
                $skipped++;
                continue;
            }

            $applicationModel->update((int) $application['id'], ['status' => $targetStatus]);
            $stageModel->moveToStage(
                (int) $application['id'],
                $targetStatus === 'shortlisted' ? 'Shortlisted (Recruiter Override)' : 'Rejected (Recruiter Override)'
            );
            $this->notifyApplicationStatusChange(
                $notificationModel,
                (int) $application['candidate_id'],
                (int) $application['id'],
                $targetStatus
            );
            $updated++;
        }

        if ($updated === 0) {
            return redirect()->back()->with('error', 'No selected applications were eligible for this action.');
        }

        $statusLabel = ucwords(str_replace('_', ' ', $targetStatus));
        $suffix = $skipped > 0 ? " ({$skipped} skipped)" : '';
        return redirect()->back()->with('success', "Bulk {$statusLabel} applied to {$updated} candidate(s){$suffix}.");
    }

    private function updateApplicationStatus(int $applicationId, string $status)
    {
        $applicationModel = model('ApplicationModel');
        $notificationModel = model('NotificationModel');
        $currentUserId = session()->get('user_id');
        $isAjax = $this->request->isAJAX();

        $application = $applicationModel
            ->select('applications.*, jobs.recruiter_id')
            ->join('jobs', 'jobs.id = applications.job_id')
            ->where('applications.id', $applicationId)
            ->first();

        if (!$application || (int) $application['recruiter_id'] !== (int) $currentUserId) {
            if ($isAjax) {
                return $this->respondApplicationStatus(false, 'Application not found', null, 404);
            }

            return redirect()->back()->with('error', 'Application not found');
        }

        if ($application['status'] === 'interview_slot_booked') {
            if ($isAjax) {
                return $this->respondApplicationStatus(false, 'Booked interview applications cannot be changed here', null, 422);
            }

            return redirect()->back()->with('error', 'Booked interview applications cannot be changed here');
        }

        if (!$this->canTakeManualDecision((string) ($application['status'] ?? ''))) {
            $message = 'This application is not eligible for recruiter action right now.';
            if ($isAjax) {
                return $this->respondApplicationStatus(false, $message, null, 422);
            }

            return redirect()->back()->with('error', $message);
        }

        $applicationModel->update($applicationId, ['status' => $status]);

        $stageModel = model('StageHistoryModel');
        $stageModel->moveToStage($applicationId, $status === 'shortlisted' ? 'Shortlisted' : 'Rejected');

        $this->notifyApplicationStatusChange(
            $notificationModel,
            (int) $application['candidate_id'],
            $applicationId,
            $status
        );

        $statusLabel = ucwords(str_replace('_', ' ', $status));
        if ($isAjax) {
            return $this->respondApplicationStatus(true, 'Application status updated to ' . $statusLabel, [
                'application_id' => $applicationId,
                'status' => $status,
                'status_label' => $statusLabel,
                'status_badge' => $this->getApplicationStatusBadgeClass($status),
            ]);
        }

        return redirect()->back()->with('success', 'Application status updated to ' . $statusLabel);
    }

    private function respondApplicationStatus(bool $success, string $message, ?array $data = null, int $statusCode = 200)
    {
        $payload = [
            'success' => $success,
            'message' => $message,
            'csrf_token_name' => csrf_token(),
            'csrf_hash' => csrf_hash(),
        ];

        if ($data !== null) {
            $payload = array_merge($payload, $data);
        }

        return $this->response->setStatusCode($statusCode)->setJSON($payload);
    }

    private function getApplicationStatusBadgeClass(string $status): string
    {
        $statusColors = [
            'pending' => 'warning',
            'applied' => 'warning',
            'shortlisted' => 'success',
            'hold' => 'secondary',
            'interview_slot_booked' => 'success',
            'selected' => 'success',
            'rejected' => 'danger',
        ];

        return $statusColors[$status] ?? 'secondary';
    }

    private function canTakeManualDecision(string $applicationStatus): bool
    {
        return in_array($applicationStatus, ['applied', 'pending', 'hold'], true);
    }

    private function notifyApplicationStatusChange(
        NotificationModel $notificationModel,
        int $candidateId,
        int $applicationId,
        string $status
    ): void {
        $label = ucwords(str_replace('_', ' ', $status));
        $type = in_array($status, ['selected', 'hired'], true) ? 'offer_sent' : 'application_status_changed';
        $message = match ($status) {
            'shortlisted' => 'Good news! Your application has been shortlisted.',
            'selected' => 'Congratulations! Your application has moved to the offer stage.',
            'hired' => 'Congratulations! Your application has been marked as hired.',
            'rejected' => 'Your application has been updated to Rejected.',
            'hold' => 'Your application has been placed on hold for future review.',
            default => 'Your application status was updated to ' . $label . '.',
        };

        $notificationModel->createNotification(
            $candidateId,
            $applicationId,
            $type,
            $message,
            base_url('candidate/applications'),
            true
        );
    }

}
    