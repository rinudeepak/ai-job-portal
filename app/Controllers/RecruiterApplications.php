<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JobModel;
use App\Models\NotificationModel;
use App\Models\RecruiterCandidateMessageModel;

class RecruiterApplications extends BaseController
{
    public function index()
    {
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
        $aiPolicy = JobModel::normalizeAiPolicy($job['ai_interview_policy'] ?? JobModel::AI_POLICY_REQUIRED_HARD);

        $filters = [
            'skills' => trim((string) $this->request->getGet('skills')),
            'experience' => trim((string) $this->request->getGet('experience')),
            'location' => trim((string) $this->request->getGet('location')),
            'status' => trim((string) $this->request->getGet('status')),
            'score_min' => $this->request->getGet('score_min'),
            'score_max' => $this->request->getGet('score_max'),
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

        $validStatuses = [
            'applied',
            'pending',
            'ai_interview_started',
            'ai_interview_completed',
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

        // Get applications for this job with optional filters
        $builder = $applicationModel
            ->select('applications.*, users.name, users.email, users.location as candidate_location, MAX(interview_sessions.overall_rating) as overall_rating, candidate_skills.skill_name, COALESCE(candidate_experience.total_experience_months, 0) as total_experience_months')
            ->join('users', 'users.id = applications.candidate_id', 'left')
            ->join('interview_sessions', 'interview_sessions.application_id = applications.id', 'left')
            ->join('candidate_skills', 'candidate_skills.candidate_id = applications.candidate_id', 'left')
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
            $builder->like('users.location', $filters['location']);
        }

        if ($filters['status'] !== '') {
            $builder->where('applications.status', $filters['status']);
        }

        if ($scoreMin !== null) {
            $builder->having('MAX(interview_sessions.overall_rating) >=', $scoreMin);
        }

        if ($scoreMax !== null) {
            $builder->having('MAX(interview_sessions.overall_rating) <=', $scoreMax);
        }

        $applications = $builder
            ->groupBy('applications.id')
            ->orderBy('applications.applied_at', 'DESC')
            ->findAll();

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
            $application['can_manual_decision'] = $this->canTakeManualDecision($aiPolicy, (string) ($application['status'] ?? ''));
        }
        unset($application);

        $filters['score_min'] = $scoreMin !== null ? (string) $scoreMin : '';
        $filters['score_max'] = $scoreMax !== null ? (string) $scoreMax : '';

        return view('recruiter/applications/view_by_job', [
            'job' => $job,
            'applications' => $applications,
            'filters' => $filters,
            'statusOptions' => $validStatuses,
            'aiPolicy' => $aiPolicy,
            'isAiCompulsory' => $this->isAiCompulsory($aiPolicy),
        ]);
    }

    public function shortlist($applicationId)
    {
        return $this->updateApplicationStatus($applicationId, 'shortlisted');
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
            ->select('applications.*, jobs.recruiter_id, jobs.ai_interview_policy, users.name as candidate_name')
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

        foreach ($applications as $application) {
            if ((int) $application['recruiter_id'] !== $currentUserId) {
                $skipped++;
                continue;
            }

            if (($application['status'] ?? '') === 'interview_slot_booked') {
                $skipped++;
                continue;
            }

            $aiPolicy = JobModel::normalizeAiPolicy($application['ai_interview_policy'] ?? JobModel::AI_POLICY_REQUIRED_HARD);
            if (!$this->canTakeManualDecision($aiPolicy, (string) ($application['status'] ?? ''))) {
                $skipped++;
                continue;
            }

            $applicationModel->update((int) $application['id'], ['status' => $targetStatus]);
            $stageModel->moveToStage(
                (int) $application['id'],
                $targetStatus === 'shortlisted' ? 'Shortlisted (Recruiter Override)' : 'Rejected (Recruiter Override)'
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
        $currentUserId = session()->get('user_id');

        $application = $applicationModel
            ->select('applications.*, jobs.recruiter_id, jobs.ai_interview_policy')
            ->join('jobs', 'jobs.id = applications.job_id')
            ->where('applications.id', $applicationId)
            ->first();

        if (!$application || (int) $application['recruiter_id'] !== (int) $currentUserId) {
            return redirect()->back()->with('error', 'Application not found');
        }

        if ($application['status'] === 'interview_slot_booked') {
            return redirect()->back()->with('error', 'Booked interview applications cannot be changed here');
        }

        $aiPolicy = JobModel::normalizeAiPolicy($application['ai_interview_policy'] ?? JobModel::AI_POLICY_REQUIRED_HARD);
        if (!$this->canTakeManualDecision($aiPolicy, (string) ($application['status'] ?? ''))) {
            return redirect()->back()->with('error', 'For AI compulsory jobs, recruiter decision is allowed only after AI interview completion.');
        }

        $applicationModel->update($applicationId, ['status' => $status]);

        $stageModel = model('StageHistoryModel');
        $stageModel->moveToStage($applicationId, $status === 'shortlisted' ? 'Shortlisted (Recruiter Override)' : 'Rejected (Recruiter Override)');

        return redirect()->back()->with('success', 'Application status updated to ' . ucwords(str_replace('_', ' ', $status)));
    }

    private function isAiCompulsory(string $aiPolicy): bool
    {
        return in_array(
            JobModel::normalizeAiPolicy($aiPolicy),
            [JobModel::AI_POLICY_REQUIRED_HARD, JobModel::AI_POLICY_REQUIRED_SOFT],
            true
        );
    }

    private function canTakeManualDecision(string $aiPolicy, string $applicationStatus): bool
    {
        if (in_array($applicationStatus, ['interview_slot_booked', 'selected'], true)) {
            return false;
        }

        if (!$this->isAiCompulsory($aiPolicy)) {
            return true;
        }

        return in_array($applicationStatus, ['ai_interview_completed', 'shortlisted', 'rejected'], true);
    }
}
