<?php

namespace App\Controllers;

use App\Models\ApplicationModel;
use App\Models\CandidateSkillsModel;
use App\Models\InterviewSessionAnswerModel;
use App\Models\InterviewSessionModel;
use App\Models\JobModel;
use CodeIgniter\HTTP\ResponseInterface;

class AiInterviewController extends BaseController
{
    public function start(int $applicationId): ResponseInterface
    {
        if (session()->get('role') !== 'candidate') {
            return redirect()->to(base_url('recruiter/dashboard'))->with('error', 'Access denied.');
        }

        $candidateId = (int) (session()->get('user_id') ?? 0);
        if ($candidateId <= 0) {
            return redirect()->to('/login')->with('error', 'Please login to continue');
        }

        $application = $this->getInterviewApplication($candidateId, $applicationId);
        if (empty($application)) {
            return redirect()->to(base_url('candidate/applications'))->with('error', 'Application not found.');
        }

        if (in_array((string) ($application['status'] ?? ''), ['rejected', 'withdrawn', 'selected', 'hired'], true)) {
            return redirect()->to(base_url('candidate/applications'))->with('error', 'This interview flow is only available for active applications.');
        }

        if (strtoupper((string) ($application['ai_interview_policy'] ?? JobModel::AI_POLICY_REQUIRED_HARD)) === JobModel::AI_POLICY_OFF) {
            return redirect()->to('/candidate/applications')
                ->with('error', 'AI interview is not required for this job.');
        }

        $flow = $this->buildInterviewFlow($application);

        return $this->response->setBody(view('candidate/ai_interview_flow', [
            'application' => $application,
            'interviewFlow' => $flow,
        ]));
    }

    public function legacyRedirect(int $applicationId): ResponseInterface
    {
        return redirect()->to('/interview/start/' . $applicationId)
            ->with('info', 'Interview flow now runs in the browser.');
    }

    /**
     * Lightweight interview bootstrap endpoint for the browser flow.
     */
    public function startInterview(?int $applicationId = null): ResponseInterface
    {
        if ($applicationId === null) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Application id is required.',
            ]);
        }

        $candidateId = (int) (session()->get('user_id') ?? 0);
        if ($candidateId <= 0 || session()->get('role') !== 'candidate') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        $application = $this->getInterviewApplication($candidateId, (int) $applicationId);
        if (empty($application)) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Application not found',
            ]);
        }

        if (!\Config\Database::connect()->tableExists('interview_sessions')) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Interview session storage is not available. Run migrations first.',
            ]);
        }

        $flow = $this->buildInterviewFlow($application);
        $maxTurns = 0;
        foreach ((array) ($flow['sections'] ?? []) as $section) {
            $maxTurns += count((array) ($section['questions'] ?? []));
        }

        $sessionModel = new InterviewSessionModel();
        $activeSession = $sessionModel
            ->where('application_id', (int) $applicationId)
            ->where('user_id', $candidateId)
            ->where('status', 'active')
            ->orderBy('id', 'DESC')
            ->first();

        $now = date('Y-m-d H:i:s');
        if (!$activeSession) {
            $sessionId = 'ai_' . (int) $applicationId . '_' . bin2hex(random_bytes(6));
            $sessionRow = [
                'user_id' => $candidateId,
                'application_id' => (int) $applicationId,
                'job_id' => (int) ($application['job_id'] ?? 0),
                'resume_version_id' => (int) ($application['resume_version_id'] ?? 0) ?: null,
                'session_id' => $sessionId,
                'position' => (string) ($application['job_title'] ?? 'AI Interview'),
                'conversation_history' => json_encode([]),
                'turn' => 1,
                'max_turns' => max(1, $maxTurns),
                'status' => 'active',
                'ai_decision' => 'pending',
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $sessionModel->insert($sessionRow);
            $activeSession = $sessionModel->find((int) $sessionModel->getInsertID());
        } else {
            $sessionModel->update((int) $activeSession['id'], ['updated_at' => $now]);
        }

        $this->syncApplicationStatus((int) $applicationId, 'ai_interview_started');

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Interview session started',
            'application_id' => $applicationId,
            'interview_session_id' => (int) ($activeSession['id'] ?? 0),
            'session_uid' => (string) ($activeSession['session_id'] ?? ''),
        ]);
    }

    public function saveAnswer(int $applicationId): ResponseInterface
    {
        $candidateId = (int) (session()->get('user_id') ?? 0);
        if ($candidateId <= 0 || session()->get('role') !== 'candidate') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        if (!\Config\Database::connect()->tableExists('interview_session_answers')) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Interview answers storage is not available. Run migrations first.',
            ]);
        }

        $application = $this->getInterviewApplication($candidateId, $applicationId);
        if (empty($application)) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Application not found',
            ]);
        }

        $payload = $this->request->getJSON(true);
        if (!is_array($payload)) {
            $payload = $this->request->getPost();
        }

        $sessionId = (int) ($payload['interview_session_id'] ?? 0);
        $sectionKey = strtolower(trim((string) ($payload['section_key'] ?? '')));
        $questionIndex = (int) ($payload['question_index'] ?? -1);
        $questionText = trim((string) ($payload['question_text'] ?? ''));

        if ($sessionId <= 0 || $sectionKey === '' || $questionIndex < 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Missing answer metadata',
            ]);
        }

        $sessionModel = new InterviewSessionModel();
        $session = $sessionModel
            ->where('id', $sessionId)
            ->where('application_id', $applicationId)
            ->where('user_id', $candidateId)
            ->first();

        if (!$session) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Interview session not found',
            ]);
        }

        $answerType = strtolower((string) ($payload['answer_type'] ?? 'mixed'));
        if (!in_array($answerType, ['video', 'audio', 'text', 'mixed'], true)) {
            $answerType = 'mixed';
        }

        $answerModel = new InterviewSessionAnswerModel();
        $now = date('Y-m-d H:i:s');
        $record = [
            'interview_session_id' => $sessionId,
            'application_id' => $applicationId,
            'candidate_id' => $candidateId,
            'section_key' => $sectionKey,
            'question_index' => $questionIndex,
            'question_text' => $questionText,
            'answer_type' => $answerType,
            'duration_seconds' => (int) ($payload['duration_seconds'] ?? 0) ?: null,
            'transcript' => trim((string) ($payload['transcript'] ?? '')) ?: null,
            'started_at' => !empty($payload['started_at']) ? date('Y-m-d H:i:s', (int) $payload['started_at']) : null,
            'submitted_at' => $now,
            'updated_at' => $now,
        ];

        $existing = $answerModel
            ->where('interview_session_id', $sessionId)
            ->where('section_key', $sectionKey)
            ->where('question_index', $questionIndex)
            ->first();

        if ($existing) {
            $answerModel->update((int) $existing['id'], $record);
            $answerRow = $answerModel->find((int) $existing['id']);
        } else {
            $record['created_at'] = $now;
            $answerModel->insert($record);
            $answerRow = $answerModel->find((int) $answerModel->getInsertID());
        }

        $savedCount = $answerModel->where('interview_session_id', $sessionId)->countAllResults();
        $sessionModel->update($sessionId, [
            'turn' => max(1, $savedCount),
            'updated_at' => $now,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Answer saved',
            'saved_answer_id' => (int) ($answerRow['id'] ?? 0),
            'saved_count' => $savedCount,
        ]);
    }

    public function completeInterview(int $applicationId): ResponseInterface
    {
        $candidateId = (int) (session()->get('user_id') ?? 0);
        if ($candidateId <= 0 || session()->get('role') !== 'candidate') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        if (!\Config\Database::connect()->tableExists('interview_sessions')) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Interview session storage is not available. Run migrations first.',
            ]);
        }

        $application = $this->getInterviewApplication($candidateId, $applicationId);
        if (empty($application)) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Application not found',
            ]);
        }

        $payload = $this->request->getJSON(true);
        if (!is_array($payload)) {
            $payload = $this->request->getPost();
        }

        $sessionId = (int) ($payload['interview_session_id'] ?? 0);
        $sessionModel = new InterviewSessionModel();
        $builder = $sessionModel->where('application_id', $applicationId)->where('user_id', $candidateId);
        if ($sessionId > 0) {
            $builder->where('id', $sessionId);
        }
        $session = $builder->orderBy('id', 'DESC')->first();

        if (!$session) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Interview session not found',
            ]);
        }

        $now = date('Y-m-d H:i:s');
        $sessionModel->update((int) $session['id'], [
            'status' => 'completed',
            'completed_at' => $now,
            'updated_at' => $now,
        ]);

        $this->syncApplicationStatus($applicationId, 'ai_interview_completed');

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Interview session completed',
            'interview_session_id' => (int) $session['id'],
        ]);
    }

    private function getInterviewApplication(int $candidateId, int $applicationId): ?array
    {
        $db = \Config\Database::connect();
        $applicationModel = new ApplicationModel();

        $hasPolicyColumn = $db->fieldExists('ai_interview_policy', 'jobs');
        $hasResumeVersions = $db->tableExists('candidate_resume_versions') && $db->fieldExists('resume_version_id', 'applications');

        $policySelect = $hasPolicyColumn
            ? 'jobs.ai_interview_policy'
            : "'REQUIRED_HARD' as ai_interview_policy";

        $resumeSelect = $hasResumeVersions
            ? 'candidate_resume_versions.title as resume_version_title,
                candidate_resume_versions.target_role as resume_version_target_role,
                candidate_resume_versions.summary as resume_version_summary,
                candidate_resume_versions.highlight_skills as resume_version_highlight_skills,
                candidate_resume_versions.content as resume_version_content,
                candidate_resume_versions.updated_at as resume_version_updated_at,'
            : "'' as resume_version_title, '' as resume_version_target_role, '' as resume_version_summary, '' as resume_version_highlight_skills, '' as resume_version_content, NULL as resume_version_updated_at,";

        $builder = $applicationModel
            ->select('
                applications.*,
                jobs.title as job_title,
                jobs.company,
                jobs.description as job_description,
                jobs.required_skills,
                jobs.experience_level,
                ' . $resumeSelect . '
                ' . $policySelect . '
            ')
            ->join('jobs', 'jobs.id = applications.job_id', 'left')
            ->where('applications.candidate_id', $candidateId)
            ->where('applications.id', $applicationId);

        if ($hasResumeVersions) {
            $builder->join('candidate_resume_versions', 'candidate_resume_versions.id = applications.resume_version_id', 'left');
        }

        return $builder->first() ?: null;
    }

    private function buildInterviewFlow(array $application): array
    {
        $jobTitle = trim((string) ($application['job_title'] ?? 'the role'));
        $companyName = trim((string) ($application['company'] ?? ''));
        $resumeTitle = trim((string) ($application['resume_version_title'] ?? ''));
        $resumeSummary = trim((string) ($application['resume_version_summary'] ?? ''));

        $requiredSkills = $this->tokenizeCsv((string) ($application['required_skills'] ?? ''));
        $resumeSkills = $this->tokenizeCsv((string) ($application['resume_version_highlight_skills'] ?? ''));
        $candidateSkillsRow = (new CandidateSkillsModel())
            ->select('skill_name')
            ->where('candidate_id', (int) ($application['candidate_id'] ?? 0))
            ->first() ?? [];
        $candidateSkills = $this->tokenizeCsv((string) ($candidateSkillsRow['skill_name'] ?? ''));
        $focusSkills = array_values(array_unique(array_filter(array_merge($requiredSkills, $resumeSkills, $candidateSkills))));
        $focusSkills = array_slice($focusSkills, 0, 6);

        $sections = [
            [
                'key' => 'reasoning',
                'title' => 'Reasoning',
                'subtitle' => 'Show how you think through unfamiliar problems.',
                'time_limit' => 60,
                'questions' => [
                    [
                        'question' => 'How would you approach your first week in ' . $jobTitle . ' at ' . ($companyName !== '' ? $companyName : 'this company') . '?',
                        'hint' => 'Explain how you would learn the role, prioritize work, and reduce risk quickly.',
                    ],
                    [
                        'question' => 'When you have incomplete requirements, how do you decide what to do first?',
                        'hint' => 'Talk through your decision-making process and what information you ask for.',
                    ],
                ],
            ],
            [
                'key' => 'logical',
                'title' => 'Logical',
                'subtitle' => 'Break down decisions and tradeoffs with structure.',
                'time_limit' => 60,
                'questions' => [
                    [
                        'question' => 'A production issue appears after release. What steps do you take first?',
                        'hint' => 'Walk through your debugging and escalation process clearly.',
                    ],
                    [
                        'question' => 'How would you choose between a faster solution and a cleaner long-term solution?',
                        'hint' => 'Balance speed, maintainability, and business impact in your answer.',
                    ],
                ],
            ],
            [
                'key' => 'technical',
                'title' => 'Technical',
                'subtitle' => 'Connect your resume and the role with practical examples.',
                'time_limit' => 60,
                'questions' => [],
            ],
        ];

        $technicalQuestions = [
            'Which project from your resume best proves you can succeed as a ' . $jobTitle . '?',
            'How have you applied ' . ($focusSkills[0] ?? 'your core technical skills') . ' in a real project?',
            'Explain a technical challenge you solved recently and the outcome.',
        ];

        if ($resumeSummary !== '') {
            $technicalQuestions[] = 'Based on your resume summary, what is the strongest value you bring to this role?';
        }

        foreach ($technicalQuestions as $index => $questionText) {
            $sectionQuestion = [
                'question' => $questionText,
                'hint' => 'Tie your answer to a concrete example, result, or technical decision.',
            ];

            if ($index < 2 && !empty($focusSkills[$index])) {
                $sectionQuestion['hint'] = 'Reference how you used ' . $focusSkills[$index] . ' in work that maps to this role.';
            }

            $sections[2]['questions'][] = $sectionQuestion;
        }

        return [
            'title' => $jobTitle . ' AI Interview Flow',
            'intro' => 'A simple step-by-step interview flow. Each section gives you one question at a time, a 60-second timer, and video + voice recording in the browser.',
            'resume_title' => $resumeTitle,
            'resume_summary' => $resumeSummary,
            'job_title' => $jobTitle,
            'company_name' => $companyName,
            'focus_skills' => $focusSkills,
            'sections' => $sections,
            'timer_seconds' => 60,
        ];
    }

    private function tokenizeCsv(string $value): array
    {
        $parts = preg_split('/[,;|\/]+/', $value) ?: [];

        return array_values(array_filter(array_map(static function ($part): string {
            return trim((string) $part);
        }, $parts)));
    }

    private function syncApplicationStatus(int $applicationId, string $targetStatus): void
    {
        $statusType = $this->getApplicationsStatusType();
        if ($statusType === '' || strpos($statusType, $targetStatus) === false) {
            return;
        }

        $applicationModel = new ApplicationModel();
        $application = $applicationModel->find($applicationId);
        if (!$application) {
            return;
        }

        $currentStatus = (string) ($application['status'] ?? '');
        if ($targetStatus === 'ai_interview_started') {
            if (in_array($currentStatus, ['rejected', 'withdrawn', 'selected', 'hired', 'ai_interview_completed'], true)) {
                return;
            }
        }

        if ($targetStatus === 'ai_interview_completed') {
            if (in_array($currentStatus, ['rejected', 'withdrawn', 'selected', 'hired'], true)) {
                return;
            }
        }

        $applicationModel->update($applicationId, ['status' => $targetStatus]);
    }

    private function getApplicationsStatusType(): string
    {
        try {
            $row = \Config\Database::connect()
                ->query("SHOW COLUMNS FROM applications LIKE 'status'")
                ->getRowArray();
            return strtolower((string) ($row['Type'] ?? ''));
        } catch (\Throwable $e) {
            return '';
        }
    }

    /**
     * Validate incoming name, email and resume.
     */
    private function validateInput(?string $name, ?string $email, $resume): array
    {
        $errors = [];

        if (empty($name)) {
            $errors['name'] = 'Name is required';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        }

        if (!$resume || !$resume->isValid()) {
            $errors['resume'] = 'Resume file is required';
        }

        return $errors;
    }

    /**
     * Build multipart payload for Python API.
     */
    private function buildPayload(string $name, string $email, $resume): array
    {
        // $file = new \CURLFile(
        //     $resume->getTempName(),
        //     $resume->getClientMimeType(),
        //     $resume->getClientName()
        // );

        // return [
        //     'name' => $name,
        //     'email' => $email,
        //     'resume' => $file,
        // ];

        return [];
    }

    /**
     * Perform cURL call to Python service.
     * Returns: [httpCode, rawBody, curlError]
     */
    private function callPythonApi(array $payload): array
    {
        // $url = (string) env('PY_AI_API_URL');
        // $token = (string) env('PY_AI_API_TOKEN');

        // $ch = curl_init($url);

        // $headers = [
        //     'Accept: application/json',
        //     'Authorization: Bearer ' . $token,
        //     // Do not set Content-Type manually for multipart/form-data.
        // ];

        // curl_setopt_array($ch, [
        //     CURLOPT_POST => true,
        //     CURLOPT_POSTFIELDS => $payload,
        //     CURLOPT_HTTPHEADER => $headers,
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_CONNECTTIMEOUT => 10,
        //     CURLOPT_TIMEOUT => 60,
        //     CURLOPT_SSL_VERIFYPEER => true,
        //     CURLOPT_SSL_VERIFYHOST => 2,
        //     CURLOPT_FOLLOWLOCATION => false,
        // ]);

        // $rawBody = curl_exec($ch);
        // $curlError = curl_error($ch);
        // $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        // curl_close($ch);

        // return [$httpCode, (string) $rawBody, $curlError ?: null];

        return [200, '{"message":"template"}', null];
    }

    /**
     * Normalize downstream API response into app response format.
     */
    private function formatApiResponse(int $httpCode, string $rawBody, ?string $curlError): ResponseInterface
    {
        if ($curlError) {
            return $this->response->setStatusCode(502)->setJSON([
                'success' => false,
                'message' => 'Python service unreachable',
                'error' => $curlError,
            ]);
        }

        $decoded = json_decode($rawBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return $this->response->setStatusCode(502)->setJSON([
                'success' => false,
                'message' => 'Invalid response from Python service',
            ]);
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return $this->response->setStatusCode(200)->setJSON([
                'success' => true,
                'data' => $decoded,
            ]);
        }

        return $this->response->setStatusCode($httpCode > 0 ? $httpCode : 500)->setJSON([
            'success' => false,
            'message' => $decoded['message'] ?? 'Python API error',
            'data' => $decoded,
        ]);
    }
}
