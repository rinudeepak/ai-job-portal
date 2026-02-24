<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class RecruiterApplications extends BaseController
{
    public function index()
    {
        $jobModel = model('JobModel');
        $applicationModel = model('ApplicationModel');
        $currentUserId = session()->get('user_id');

        // Get recruiter's jobs with application counts
        $jobs = $jobModel
            ->select('jobs.*, COUNT(applications.id) as application_count')
            ->join('applications', 'applications.job_id = jobs.id', 'left')
            ->where('jobs.recruiter_id', $currentUserId)
            ->groupBy('jobs.id')
            ->orderBy('jobs.created_at', 'DESC')
            ->findAll();

        return view('recruiter/applications/index', ['jobs' => $jobs]);
    }

    public function viewByJob($jobId)
    {
        $jobModel = model('JobModel');
        $applicationModel = model('ApplicationModel');
        $currentUserId = session()->get('user_id');

        // Verify job belongs to recruiter
        $job = $jobModel->where('id', $jobId)->where('recruiter_id', $currentUserId)->first();
        if (!$job) {
            return redirect()->to('recruiter/applications')->with('error', 'Job not found');
        }

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
                continue;
            }

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
        unset($application);

        $filters['score_min'] = $scoreMin !== null ? (string) $scoreMin : '';
        $filters['score_max'] = $scoreMax !== null ? (string) $scoreMax : '';

        return view('recruiter/applications/view_by_job', [
            'job' => $job,
            'applications' => $applications,
            'filters' => $filters,
            'statusOptions' => $validStatuses,
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

    private function updateApplicationStatus(int $applicationId, string $status)
    {
        $applicationModel = model('ApplicationModel');
        $currentUserId = session()->get('user_id');

        $application = $applicationModel
            ->select('applications.*, jobs.recruiter_id')
            ->join('jobs', 'jobs.id = applications.job_id')
            ->where('applications.id', $applicationId)
            ->first();

        if (!$application || (int) $application['recruiter_id'] !== (int) $currentUserId) {
            return redirect()->back()->with('error', 'Application not found');
        }

        if ($application['status'] === 'interview_slot_booked') {
            return redirect()->back()->with('error', 'Booked interview applications cannot be changed here');
        }

        $applicationModel->update($applicationId, ['status' => $status]);

        $stageModel = model('StageHistoryModel');
        $stageModel->moveToStage($applicationId, $status === 'shortlisted' ? 'Shortlisted (Recruiter Override)' : 'Rejected (Recruiter Override)');

        return redirect()->back()->with('success', 'Application status updated to ' . ucwords(str_replace('_', ' ', $status)));
    }
}
