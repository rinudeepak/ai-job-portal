<?php

namespace App\Controllers;

use App\Libraries\AiInterviewTranscriber;
use App\Libraries\UsageAnalyticsService;
use App\Models\AiInterviewRound1AttemptModel;
use App\Models\ApplicationModel;
use App\Models\CandidateSkillsModel;
use App\Models\InterviewSessionAnswerModel;
use App\Models\InterviewSessionModel;
use App\Models\JobModel;
use CodeIgniter\HTTP\ResponseInterface;

class AiInterviewController extends BaseController
{
    private const INTERVIEW_SESSION_EXPIRY_SECONDS = 86400;

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
        $sessionModel = new InterviewSessionModel();

        $activeSession = $sessionModel
            ->where('application_id', $applicationId)
            ->where('user_id', $candidateId)
            ->where('status', 'active')
            ->orderBy('id', 'DESC')
            ->first();
        if ($activeSession && $this->isInterviewSessionExpired($activeSession)) {
            $activeSession = $this->expireInterviewSession($sessionModel, $activeSession);
        }

        $terminalSession = $sessionModel
            ->where('application_id', $applicationId)
            ->where('user_id', $candidateId)
            ->whereIn('status', ['submitted', 'under_review', 'finalized', 'candidate_notified', 'completed', 'pending_evaluation', 'evaluated', 'expired'])
            ->orderBy('id', 'DESC')
            ->first();

        return $this->response->setBody(view('candidate/ai_interview_flow', [
            'application'       => $application,
            'interviewFlow'     => $flow,
            'interviewCompleted' => !empty($terminalSession) && (string) ($terminalSession['status'] ?? '') !== 'expired',
            'interviewExpired'   => !empty($terminalSession) && (string) ($terminalSession['status'] ?? '') === 'expired',
            'completedSession'  => $terminalSession ?? [],
            'expiredSession'    => $terminalSession ?? [],
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
        foreach ((array) ($flow['round2_sections'] ?? $flow['sections'] ?? []) as $section) {
            $maxTurns += count((array) ($section['questions'] ?? []));
        }
        $round1Total = count((array) ($flow['round1_questions'] ?? []));

        $sessionModel = new InterviewSessionModel();
        $activeSession = $sessionModel
            ->where('application_id', (int) $applicationId)
            ->where('user_id', $candidateId)
            ->where('status', 'active')
            ->orderBy('id', 'DESC')
            ->first();

        if ($activeSession && $this->isInterviewSessionExpired($activeSession)) {
            $activeSession = $this->expireInterviewSession($sessionModel, $activeSession);
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'expired_session' => true,
                'message' => 'This interview session has expired and can no longer be resumed. Please start a new application flow if available.',
            ]);
        }

        if (strtoupper($this->request->getMethod()) === 'GET') {
            return $this->response->setJSON([
                'success' => true,
                'application_id' => $applicationId,
                'snapshot' => $activeSession
                    ? $this->buildInterviewResumeSnapshot($application, $flow, $activeSession)
                    : null,
            ]);
        }

        $maxAttempts    = 1;
        $attemptCount   = $sessionModel
            ->where('application_id', (int) $applicationId)
            ->where('user_id', $candidateId)
            ->countAllResults();

        if (!$activeSession && $attemptCount >= $maxAttempts) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'You have reached the maximum number of interview attempts (' . $maxAttempts . ') for this application.',
            ]);
        }

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
                'conversation_history' => json_encode([
                    'events' => [],
                    'adaptive_state' => [
                        'pending_followup' => null,
                        'asked_followups' => 0,
                    ],
                ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'turn' => 1,
                'max_turns' => max(1, $maxTurns),
                'status' => 'active',
                'ai_decision' => 'pending',
                'round1_total_questions' => $round1Total,
                'round1_answered' => 0,
                'interview_total_seconds' => 1800,
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
            'round1_total_questions' => $round1Total,
            'snapshot' => $this->buildInterviewResumeSnapshot($application, $flow, $activeSession),
        ]);
    }

    public function saveRound1Answer(int $applicationId): ResponseInterface
    {
        $candidateId = (int) (session()->get('user_id') ?? 0);
        if ($candidateId <= 0 || session()->get('role') !== 'candidate') {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Unauthorized',
            ]);
        }

        if (!\Config\Database::connect()->tableExists('ai_interview_round1_attempts')) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Round 1 storage is not available. Run migrations first.',
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
        $sectionKey = strtolower(trim((string) ($payload['section_key'] ?? 'reasoning')));
        $questionType = strtolower(trim((string) ($payload['question_type'] ?? 'mcq')));
        $questionText = trim((string) ($payload['question_text'] ?? ''));
        $selectedAnswer = trim((string) ($payload['selected_answer'] ?? ''));
        $correctAnswer = trim((string) ($payload['correct_answer'] ?? ''));
        $clientContext = $this->normalizeJsonPayloadField($payload['client_context'] ?? null);
        $integrityFlags = $this->normalizeJsonPayloadField($payload['integrity_flags'] ?? null);
        $pasteEventCount = max(0, (int) ($payload['paste_event_count'] ?? 0));
        $pastedCharacterCount = max(0, (int) ($payload['pasted_character_count'] ?? 0));
        $copyPasteDetected = !empty($payload['copy_paste_detected']) ? 1 : 0;
        $largeInsertCount = max(0, (int) ($payload['large_insert_count'] ?? 0));
        $largeInsertCharacterCount = max(0, (int) ($payload['large_insert_character_count'] ?? 0));
        $largeInsertDetected = !empty($payload['large_insert_detected']) ? 1 : 0;

        if ($sessionId <= 0 || $questionText === '' || $selectedAnswer === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Missing round 1 answer fields',
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

        $isCorrect = null;
        $score    = 0.0;
        $maxScore = 10.0;

        if ($correctAnswer !== '') {
            $isCorrect = strtolower(trim($selectedAnswer)) === strtolower(trim($correctAnswer)) ? 1 : 0;
            $score     = $this->scoreRound1Answer($questionType, $selectedAnswer, $correctAnswer);
        }

        $attemptModel = new AiInterviewRound1AttemptModel();
        $now = date('Y-m-d H:i:s');
        $record = [
            'interview_session_id' => $sessionId,
            'application_id' => $applicationId,
            'candidate_id' => $candidateId,
            'section_key' => in_array($sectionKey, ['reasoning', 'logical', 'fill_blank'], true) ? $sectionKey : 'reasoning',
            'question_type' => in_array($questionType, ['mcq', 'fill_blank'], true) ? $questionType : 'mcq',
            'question_text' => $questionText,
            'selected_answer' => $selectedAnswer,
            'correct_answer' => $correctAnswer !== '' ? $correctAnswer : null,
            'is_correct' => $isCorrect,
            'score' => $score,
            'max_score' => $maxScore,
            'answered_at' => $now,
            'updated_at' => $now,
        ];

        $round1Table = \Config\Database::connect();
        if ($round1Table->fieldExists('client_context', 'ai_interview_round1_attempts')) {
            $record['client_context'] = $clientContext;
        }
        if ($round1Table->fieldExists('integrity_flags', 'ai_interview_round1_attempts')) {
            $record['integrity_flags'] = $integrityFlags;
        }
        if ($round1Table->fieldExists('paste_event_count', 'ai_interview_round1_attempts')) {
            $record['paste_event_count'] = $pasteEventCount;
        }
        if ($round1Table->fieldExists('pasted_character_count', 'ai_interview_round1_attempts')) {
            $record['pasted_character_count'] = $pastedCharacterCount;
        }
        if ($round1Table->fieldExists('copy_paste_detected', 'ai_interview_round1_attempts')) {
            $record['copy_paste_detected'] = $copyPasteDetected;
        }
        if ($round1Table->fieldExists('large_insert_count', 'ai_interview_round1_attempts')) {
            $record['large_insert_count'] = $largeInsertCount;
        }
        if ($round1Table->fieldExists('large_insert_character_count', 'ai_interview_round1_attempts')) {
            $record['large_insert_character_count'] = $largeInsertCharacterCount;
        }
        if ($round1Table->fieldExists('large_insert_detected', 'ai_interview_round1_attempts')) {
            $record['large_insert_detected'] = $largeInsertDetected;
        }

        $existing = $attemptModel
            ->where('interview_session_id', $sessionId)
            ->where('question_text', $questionText)
            ->first();

        if ($existing) {
            $attemptModel->update((int) $existing['id'], $record);
        } else {
            $record['created_at'] = $now;
            $attemptModel->insert($record);
        }

        $attempts = $attemptModel->findBySession($sessionId);
        $answered = count($attempts);
        $obtained = array_sum(array_map(static fn ($row) => (float) ($row['score'] ?? 0), $attempts));
        $possible = array_sum(array_map(static fn ($row) => (float) ($row['max_score'] ?? 0), $attempts));
        $round1Score = $possible > 0 ? round(($obtained / $possible) * 100, 2) : 0.0;

        $sessionModel->update($sessionId, [
            'round1_answered' => $answered,
            'round1_score' => $round1Score,
            'updated_at' => $now,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Round 1 answer saved',
            'round1_answered' => $answered,
            'round1_score' => $round1Score,
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

        $payload = [];
        $contentType = strtolower((string) $this->request->getHeaderLine('Content-Type'));
        if (strpos($contentType, 'application/json') !== false) {
            try {
                $decoded = $this->request->getJSON(true);
                if (is_array($decoded)) {
                    $payload = $decoded;
                }
            } catch (\Throwable $e) {
                $payload = [];
            }
        }
        if (!is_array($payload) || empty($payload)) {
            $payload = $this->request->getPost();
        }

        $sessionId = (int) ($payload['interview_session_id'] ?? 0);
        $sectionKey = strtolower(trim((string) ($payload['section_key'] ?? '')));
        $questionIndex = (int) ($payload['question_index'] ?? -1);
        $questionText = trim((string) ($payload['question_text'] ?? ''));
        $answerVariant = strtolower(trim((string) ($payload['answer_variant'] ?? 'base')));
        if (!in_array($answerVariant, ['base', 'followup'], true)) {
            $answerVariant = 'base';
        }
        $parentQuestionIndex = array_key_exists('parent_question_index', $payload)
            ? (int) ($payload['parent_question_index'] ?? -1)
            : null;
        if ($parentQuestionIndex !== null && $parentQuestionIndex < 0) {
            $parentQuestionIndex = null;
        }

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

        $videoUploadPath = null;
        $serverTranscript = null;
        $videoBlob = $this->request->getFile('video_blob');
        if ($videoBlob && $videoBlob->isValid() && !$videoBlob->hasMoved()) {
            $videoUploadPath = $this->storeInterviewMedia(
                $videoBlob,
                $candidateId,
                $sessionId,
                $sectionKey,
                $questionIndex
            );

            $absolutePath = WRITEPATH . $videoUploadPath;
            $serverTranscript = $this->transcribeUploadedAnswer($absolutePath);
        }

        $rawTranscript = trim((string) ($payload['transcript'] ?? ''));
        $transcript = $this->selectBestTranscript($rawTranscript, $serverTranscript);
        $durationSeconds = (int) ($payload['duration_seconds'] ?? 0) ?: null;
        $clientContext = $this->normalizeJsonPayloadField($payload['client_context'] ?? null);
        $integrityFlags = $this->normalizeJsonPayloadField($payload['integrity_flags'] ?? null);
        $recordingMetrics = $this->normalizeJsonPayloadField($payload['recording_metrics'] ?? null);
        $tabSwitchCount = max(0, (int) ($payload['tab_switch_count'] ?? 0));
        $hiddenDurationSeconds = max(0, (int) ($payload['hidden_duration_seconds'] ?? 0));
        $recordingHealth = trim((string) ($payload['recording_health'] ?? ''));
        $evaluation = $this->evaluateAnswer($sectionKey, $questionText, $transcript, $durationSeconds);

        $answerModel = new InterviewSessionAnswerModel();
        $now = date('Y-m-d H:i:s');
        $record = [
            'interview_session_id' => $sessionId,
            'application_id' => $applicationId,
            'candidate_id' => $candidateId,
            'section_key' => $sectionKey,
            'question_index' => $questionIndex,
            'question_text' => $questionText,
            'answer_variant' => $answerVariant,
            'parent_question_index' => $parentQuestionIndex,
            'answer_type' => $answerType,
            'duration_seconds' => $durationSeconds,
            'transcript' => $transcript,
            'client_context' => $clientContext,
            'integrity_flags' => $integrityFlags,
            'tab_switch_count' => $tabSwitchCount,
            'hidden_duration_seconds' => $hiddenDurationSeconds,
            'recording_health' => $recordingHealth !== '' ? $recordingHealth : null,
            'recording_metrics' => $recordingMetrics,
            'ai_score' => $evaluation['score'],
            'ai_feedback' => $evaluation['feedback'],
            'started_at' => !empty($payload['started_at']) ? date('Y-m-d H:i:s', (int) $payload['started_at']) : null,
            'submitted_at' => $now,
            'updated_at' => $now,
        ];
        if ($videoUploadPath !== null) {
            $record['video_path'] = $videoUploadPath;
        }

        $existing = $answerModel
            ->where('interview_session_id', $sessionId)
            ->where('section_key', $sectionKey)
            ->where('question_index', $questionIndex)
            ->where('answer_variant', $answerVariant)
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

        $nextAction = 'advance';
        $followupPayload = null;
        $score = (float) ($evaluation['score'] ?? 0.0);

        if (in_array($sectionKey, ['reasoning', 'logical', 'technical'], true)) {
            if ($answerVariant === 'base') {
                $followupPayload = $this->createAdaptiveFollowupQuestion(
                    $application,
                    $session,
                    $sectionKey,
                    $questionIndex,
                    $questionText,
                    $transcript,
                    $score,
                    (string) ($evaluation['feedback'] ?? ''),
                    $durationSeconds
                );

                if (!empty($followupPayload['question_text'])) {
                    $nextAction = 'followup';
                    $currentState = $this->getAdaptiveInterviewState($session);
                    $currentState['pending_followup'] = [
                        'section_key' => $sectionKey,
                        'question_index' => $questionIndex,
                        'parent_question_index' => $questionIndex,
                        'question_text' => (string) $followupPayload['question_text'],
                        'followup_type' => (string) ($followupPayload['followup_type'] ?? 'clarify'),
                        'adaptive_level' => (string) ($followupPayload['adaptive_level'] ?? 'medium'),
                        'trigger_score' => $score,
                        'base_question_text' => $questionText,
                        'reason' => (string) ($followupPayload['reason'] ?? ''),
                    ];
                    $currentState['asked_followups'] = (int) ($currentState['asked_followups'] ?? 0) + 1;
                    $this->saveAdaptiveInterviewState($sessionId, $currentState);
                } else {
                    $this->clearAdaptiveFollowupState($sessionId, $session);
                }
            } else {
                $this->clearAdaptiveFollowupState($sessionId, $session);
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Answer saved',
            'saved_answer_id' => (int) ($answerRow['id'] ?? 0),
            'saved_count' => $savedCount,
            'ai_score' => $evaluation['score'],
            'ai_feedback' => $evaluation['feedback'],
            'ai_available' => (bool) ($evaluation['available'] ?? false),
            'next_action' => $nextAction,
            'followup_question' => $followupPayload,
        ]);
    }

    public function logIntegrityEvent(int $applicationId): ResponseInterface
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
        $eventType = strtolower(trim((string) ($payload['event_type'] ?? '')));
        if ($sessionId <= 0 || $eventType === '') {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Missing integrity event metadata',
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

        $details = $this->normalizePayloadArray($payload['details'] ?? []);
        $severity = strtolower(trim((string) ($payload['severity'] ?? 'warning')));
        if (!in_array($severity, ['info', 'warning', 'critical'], true)) {
            $severity = 'warning';
        }

        $history = $this->loadInterviewHistory($session);
        $events = (array) ($history['integrity_events'] ?? []);
        $summary = (array) ($history['integrity_summary'] ?? []);
        $now = date('Y-m-d H:i:s');
        $event = [
            'event_type' => $eventType,
            'severity' => $severity,
            'details' => $details,
            'created_at' => $now,
        ];
        $events[] = $event;
        if (count($events) > 100) {
            $events = array_slice($events, -100);
        }

        $summary['warning_count'] = (int) ($summary['warning_count'] ?? 0);
        $summary['tab_switch_count'] = (int) ($summary['tab_switch_count'] ?? 0);
        $summary['hidden_duration_seconds'] = (int) ($summary['hidden_duration_seconds'] ?? 0);
        $summary['reconnect_count'] = (int) ($summary['reconnect_count'] ?? 0);

        $tabSwitchCount = (int) ($details['tab_switch_count'] ?? 0);
        $hiddenDurationSeconds = (int) ($details['hidden_duration_seconds'] ?? 0);

        if (in_array($eventType, ['tab_hidden', 'tab_visible', 'blur', 'focus'], true)) {
            $summary['tab_switch_count'] += max(1, $tabSwitchCount > 0 ? $tabSwitchCount : 1);
        }
        if ($hiddenDurationSeconds > 0) {
            $summary['hidden_duration_seconds'] += $hiddenDurationSeconds;
        }
        if (in_array($eventType, ['resume', 'reconnect', 'session_reconnected', 'online'], true)) {
            $summary['reconnect_count'] += 1;
            $session['last_resume_at'] = $now;
        }
        if ($severity !== 'info') {
            $summary['warning_count'] += 1;
        }

        $summary['last_event_type'] = $eventType;
        $summary['last_event_at'] = $now;

        $integrityFlags = $this->normalizePayloadArray($payload['integrity_flags'] ?? []);
        $eventFlags = $this->normalizePayloadArray($details['flags'] ?? []);
        $mergedFlags = array_values(array_unique(array_filter(array_map('strval', array_merge($integrityFlags, $eventFlags)))));

        $history['integrity_events'] = $events;
        $history['integrity_summary'] = $summary;
        $history['events'] = (array) ($history['events'] ?? []);
        $history['events'][] = [
            'type' => 'integrity:' . $eventType,
            'created_at' => $now,
            'details' => $details,
        ];

        $sessionModel->update($sessionId, [
            'conversation_history' => json_encode($history, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'integrity_events' => json_encode($events, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'integrity_flags' => json_encode($mergedFlags, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'integrity_warning_count' => (int) ($summary['warning_count'] ?? 0),
            'tab_switch_count' => (int) ($summary['tab_switch_count'] ?? 0),
            'hidden_duration_seconds' => (int) ($summary['hidden_duration_seconds'] ?? 0),
            'reconnect_count' => (int) ($summary['reconnect_count'] ?? 0),
            'last_integrity_ping_at' => $now,
            'last_resume_at' => $session['last_resume_at'] ?? null,
            'updated_at' => $now,
        ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Integrity event recorded',
            'integrity_warning_count' => (int) ($summary['warning_count'] ?? 0),
            'tab_switch_count' => (int) ($summary['tab_switch_count'] ?? 0),
            'hidden_duration_seconds' => (int) ($summary['hidden_duration_seconds'] ?? 0),
            'reconnect_count' => (int) ($summary['reconnect_count'] ?? 0),
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
        $round1Score = $this->calculateRound1Score((int) $session['id']);
        $sectionScores = $this->calculateRound2SectionScores((int) $session['id']);
        $round2Score = $this->calculateRound2Score((int) $session['id']);
        $overallRating = null;
        $technicalScore = null;
        $communicationScore = null;
        $problemSolvingScore = null;
        $summary = 'AI scoring is unavailable until the recruiter reviews this interview.';
        $strengths = null;
        $concerns = null;
        $aiDecision = 'pending';

        if ($round2Score !== null) {
            $overallRating = round((($round1Score * 0.4) + ($round2Score * 0.6)) / 10, 2);
            $technicalScore = (float) ($sectionScores['technical'] ?? $round2Score);
            $communicationScore = (float) ($sectionScores['logical'] ?? $round2Score);
            $problemSolvingScore = (float) ($sectionScores['reasoning'] ?? $round2Score);
            $aiDecision = $this->resolveAiDecision($overallRating);
            $summary = $this->buildRecommendationSummary(
                $overallRating,
                $technicalScore,
                $communicationScore,
                $problemSolvingScore
            );
            [$strengths, $concerns] = $this->buildStrengthsAndConcerns(
                $technicalScore,
                $communicationScore,
                $problemSolvingScore
            );
        }

        $sessionModel->update((int) $session['id'], [
            'status' => 'submitted',
            'completed_at' => $now,
            'round1_score' => $round1Score,
            'round2_score' => $round2Score,
            'overall_rating' => $overallRating,
            'section_scores' => $round2Score !== null ? json_encode($sectionScores, JSON_UNESCAPED_SLASHES) : null,
            'technical_score' => $technicalScore,
            'communication_score' => $communicationScore,
            'problem_solving_score' => $problemSolvingScore,
            'recommendation_summary' => $summary,
            'strengths' => $strengths !== null ? json_encode($strengths, JSON_UNESCAPED_SLASHES) : null,
            'concerns' => $concerns !== null ? json_encode($concerns, JSON_UNESCAPED_SLASHES) : null,
            'ai_decision' => $aiDecision,
            'updated_at' => $now,
        ]);

        model('StageHistoryModel')->moveToStage($applicationId, 'AI Interview Submitted');

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
        $roleContext = $this->buildRoleQuestionContext($jobTitle, $focusSkills);
        $interviewerPersona = $this->buildInterviewerPersona($jobTitle, $companyName, $focusSkills, $roleContext);

        $round2Sections = [
            [
                'key' => 'reasoning',
                'title' => 'Reasoning',
                'subtitle' => 'Show how you think through unfamiliar problems.',
                'time_limit' => 60,
                'questions' => [
                    [
                        'question' => 'How would you approach your first week in ' . $jobTitle . ' at ' . ($companyName !== '' ? $companyName : 'this company') . '?',
                        'hint' => 'Explain how you would learn the role, prioritize work, and reduce risk quickly.',
                        'difficulty' => 'medium',
                        'adaptive_focus' => $focusSkills[0] ?? 'role ramp-up',
                    ],
                    [
                        'question' => 'When you have incomplete requirements, how do you decide what to do first?',
                        'hint' => 'Talk through your decision-making process and what information you ask for.',
                        'difficulty' => 'medium',
                        'adaptive_focus' => $focusSkills[1] ?? 'decision-making',
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
                        'difficulty' => 'medium',
                        'adaptive_focus' => $focusSkills[1] ?? 'incident response',
                    ],
                    [
                        'question' => 'How would you choose between a faster solution and a cleaner long-term solution?',
                        'hint' => 'Balance speed, maintainability, and business impact in your answer.',
                        'difficulty' => 'high',
                        'adaptive_focus' => $focusSkills[0] ?? 'tradeoff analysis',
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

        $selectionSeed = $this->buildInterviewSelectionSeed($application);
        $technicalQuestions = $this->buildTechnicalQuestionSet(
            $jobTitle,
            $roleContext,
            $focusSkills,
            $resumeSummary,
            $selectionSeed
        );

        foreach ($technicalQuestions as $index => $questionText) {
            $sectionQuestion = [
                'question' => $questionText,
                'hint' => 'Tie your answer to a concrete example, result, or technical decision.',
                'difficulty' => $index === 0 ? 'medium' : ($index === 1 ? 'high' : 'high'),
                'adaptive_focus' => $focusSkills[$index] ?? ($roleContext['primary_skill'] ?? 'technical depth'),
            ];

            if ($index < 2 && !empty($focusSkills[$index])) {
                $sectionQuestion['hint'] = 'Reference how you used ' . $focusSkills[$index] . ' in work that maps to this role.';
            }

            $round2Sections[2]['questions'][] = $sectionQuestion;
        }

        $round1Questions = $this->buildRound1Questions($jobTitle, $focusSkills, $selectionSeed);
        $aiGenerated = $this->generateInterviewQuestionsWithOpenAi($application, $focusSkills, $selectionSeed, $interviewerPersona);
        $questionSource = 'fallback';
        if (!empty($aiGenerated['round1_questions']) && !empty($aiGenerated['round2_sections'])) {
            $round1Questions = $aiGenerated['round1_questions'];
            $round2Sections = $aiGenerated['round2_sections'];
            $questionSource = 'ai';
        }

        return [
            'title' => $jobTitle . ' AI Interview Flow',
            'intro' => 'A structured two-part interview: written screening followed by role-specific responses.',
            'resume_title' => $resumeTitle,
            'resume_summary' => $resumeSummary,
            'job_title' => $jobTitle,
            'company_name' => $companyName,
            'focus_skills' => $focusSkills,
            'persona' => $interviewerPersona,
            'generation_source' => $questionSource,
            'round1_questions' => $round1Questions,
            'round2_sections' => $round2Sections,
            'sections' => $round2Sections,
            'round1_timer_seconds' => 720,
            'round2_timer_seconds' => 1080,
            'total_timer_seconds' => 1800,
            'timer_seconds' => 60,
        ];
    }

    private function getAdaptiveInterviewState(array $session): array
    {
        $decoded = [];
        try {
            $raw = trim((string) ($session['conversation_history'] ?? ''));
            if ($raw !== '') {
                $parsed = json_decode($raw, true);
                if (is_array($parsed)) {
                    $decoded = $parsed;
                }
            }
        } catch (\Throwable $e) {
            $decoded = [];
        }

        $state = (array) ($decoded['adaptive_state'] ?? []);
        return [
            'pending_followup' => is_array($state['pending_followup'] ?? null) ? $state['pending_followup'] : null,
            'asked_followups' => (int) ($state['asked_followups'] ?? 0),
        ];
    }

    private function saveAdaptiveInterviewState(int $sessionId, array $state): void
    {
        $sessionModel = new InterviewSessionModel();
        $session = $sessionModel->find($sessionId);
        if (!$session) {
            return;
        }

        $history = [];
        $raw = trim((string) ($session['conversation_history'] ?? ''));
        if ($raw !== '') {
            $parsed = json_decode($raw, true);
            if (is_array($parsed)) {
                $history = $parsed;
            }
        }

        $history['events'] = (array) ($history['events'] ?? []);
        $history['adaptive_state'] = [
            'pending_followup' => $state['pending_followup'] ?? null,
            'asked_followups' => (int) ($state['asked_followups'] ?? 0),
        ];

        $sessionModel->update($sessionId, [
            'conversation_history' => json_encode($history, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    private function clearAdaptiveFollowupState(int $sessionId, array $session): void
    {
        $state = $this->getAdaptiveInterviewState($session);
        $state['pending_followup'] = null;
        $this->saveAdaptiveInterviewState($sessionId, $state);
    }

    private function loadInterviewHistory(array $session): array
    {
        $history = [];
        try {
            $raw = trim((string) ($session['conversation_history'] ?? ''));
            if ($raw !== '') {
                $parsed = json_decode($raw, true);
                if (is_array($parsed)) {
                    $history = $parsed;
                }
            }
        } catch (\Throwable $e) {
            $history = [];
        }

        $history['events'] = (array) ($history['events'] ?? []);
        $history['integrity_events'] = (array) ($history['integrity_events'] ?? []);
        $history['integrity_summary'] = (array) ($history['integrity_summary'] ?? []);

        return $history;
    }

    private function normalizePayloadArray(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '') {
                return [];
            }
            $decoded = json_decode($trimmed, true);
            if (is_array($decoded)) {
                return $decoded;
            }
            return [$trimmed];
        }

        if ($value === null) {
            return [];
        }

        return [(string) $value];
    }

    private function normalizeJsonPayloadField(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '') {
                return null;
            }

            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                return json_encode($decoded, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            }

            return $trimmed;
        }

        if (is_array($value) || is_object($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function createAdaptiveFollowupQuestion(
        array $application,
        array $session,
        string $sectionKey,
        int $questionIndex,
        string $questionText,
        ?string $transcript,
        float $score,
        string $feedback,
        ?int $durationSeconds
    ): array {
        $scoreBand = $score < 45 ? 'low' : ($score < 75 ? 'medium' : 'high');
        $followupType = $score < 45 ? 'clarify' : ($score < 75 ? 'tradeoff' : 'deepen');

        $fallbacks = [
            'low' => 'Can you walk me through that more clearly, step by step, and connect it back to the role?',
            'medium' => 'What tradeoff did you consider there, and why did you choose that approach?',
            'high' => 'If this scaled up or changed under pressure, what would you do differently?',
        ];

        $fallback = [
            'question_text' => $fallbacks[$scoreBand],
            'followup_type' => $followupType,
            'adaptive_level' => $scoreBand,
            'reason' => 'Heuristic follow-up based on answer quality.',
        ];

        $apiKey = trim((string) (getenv('OPENAI_API_KEY') ?: ''));
        if ($apiKey === '') {
            return $fallback;
        }

        $persona = $this->buildInterviewerPersona(
            (string) ($application['job_title'] ?? 'the role'),
            (string) ($application['company'] ?? ''),
            $this->tokenizeCsv((string) ($application['required_skills'] ?? '')),
            $this->buildRoleQuestionContext((string) ($application['job_title'] ?? 'the role'), $this->tokenizeCsv((string) ($application['required_skills'] ?? '')))
        );

        $payload = [
            'job_title' => (string) ($application['job_title'] ?? ''),
            'company' => (string) ($application['company'] ?? ''),
            'section_key' => $sectionKey,
            'question_index' => $questionIndex,
            'question_text' => $questionText,
            'transcript' => $transcript,
            'score' => $score,
            'feedback' => $feedback,
            'duration_seconds' => $durationSeconds,
            'persona' => $persona,
        ];

        $prompt = "Write one adaptive follow-up interview question for the candidate.\n\n"
            . "Return strict JSON only with this schema:\n"
            . "{\n"
            . "  \"question_text\": \"string\",\n"
            . "  \"followup_type\": \"clarify|tradeoff|deepen|edge_case|example\",\n"
            . "  \"adaptive_level\": \"low|medium|high\",\n"
            . "  \"reason\": \"string\"\n"
            . "}\n\n"
            . "Rules:\n"
            . "- Use the candidate's prior answer quality to decide the follow-up depth.\n"
            . "- If the answer was weak, ask for clarification or a simpler step-by-step explanation.\n"
            . "- If the answer was decent, probe tradeoffs, outcomes, or implementation details.\n"
            . "- If the answer was strong, ask a deeper scenario, edge case, or scaling question.\n"
            . "- Keep the same interviewer voice and role context.\n"
            . "- Ask only one follow-up question.\n\n"
            . "Context:\n" . json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $requestBody = [
            'model' => 'gpt-4o-mini',
            'messages' => [[
                'role' => 'system',
                'content' => 'You are an expert technical interviewer. Return valid JSON only.',
            ], [
                'role' => 'user',
                'content' => $prompt,
            ]],
            'temperature' => 0.3,
            'max_tokens' => 220,
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($requestBody),
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $curlError !== '' || $httpCode !== 200) {
            return $fallback;
        }

        $decoded = json_decode((string) $response, true);
        if (is_array($decoded)) {
            (new UsageAnalyticsService())->logOpenAiUsage($decoded, '/v1/chat/completions', 'gpt-4o-mini');
        }
        $content = (string) ($decoded['choices'][0]['message']['content'] ?? '');
        $json = $this->extractJsonObject($content);
        $result = json_decode($json, true);

        if (!is_array($result)) {
            return $fallback;
        }

        $questionTextOut = trim((string) ($result['question_text'] ?? ''));
        if ($questionTextOut === '') {
            return $fallback;
        }

        return [
            'question_text' => $questionTextOut,
            'followup_type' => in_array((string) ($result['followup_type'] ?? ''), ['clarify', 'tradeoff', 'deepen', 'edge_case', 'example'], true)
                ? (string) $result['followup_type']
                : $followupType,
            'adaptive_level' => in_array((string) ($result['adaptive_level'] ?? ''), ['low', 'medium', 'high'], true)
                ? (string) $result['adaptive_level']
                : $scoreBand,
            'reason' => trim((string) ($result['reason'] ?? 'Adaptive follow-up based on the previous answer.')),
        ];
    }

    private function generateInterviewQuestionsWithOpenAi(array $application, array $focusSkills, string $selectionSeed, array $persona = []): array
    {
        $apiKey = trim((string) (getenv('OPENAI_API_KEY') ?: ''));
        if ($apiKey === '') {
            return [];
        }

        $cacheKey = 'ai_interview_qset_' . sha1(json_encode([
            'candidate_id' => (int) ($application['candidate_id'] ?? 0),
            'application_id' => (int) ($application['id'] ?? 0),
            'job_id' => (int) ($application['job_id'] ?? 0),
            'job_title' => (string) ($application['job_title'] ?? ''),
            'company' => (string) ($application['company'] ?? ''),
            'required_skills' => (string) ($application['required_skills'] ?? ''),
            'experience_level' => (string) ($application['experience_level'] ?? ''),
            'resume_summary' => (string) ($application['resume_version_summary'] ?? ''),
            'focus_skills' => array_values($focusSkills),
            'persona' => [
                'name' => (string) ($persona['name'] ?? ''),
                'title' => (string) ($persona['title'] ?? ''),
                'tone' => (string) ($persona['tone'] ?? ''),
                'style' => (string) ($persona['style'] ?? ''),
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $cached = cache()->get($cacheKey);
        if (is_array($cached) && !empty($cached['round1_questions']) && !empty($cached['round2_sections'])) {
            return $cached;
        }

        $payload = [
            'role' => (string) ($application['job_title'] ?? ''),
            'company' => (string) ($application['company'] ?? ''),
            'required_skills' => array_values($this->tokenizeCsv((string) ($application['required_skills'] ?? ''))),
            'experience_level' => (string) ($application['experience_level'] ?? ''),
            'resume_summary' => (string) ($application['resume_version_summary'] ?? ''),
            'focus_skills' => array_values($focusSkills),
            'selection_seed' => $selectionSeed,
            'persona' => $persona,
        ];

        $prompt = "Generate interview questions tailored to this role context. Return strict JSON only.\n\n"
            . "Schema:\n"
            . "{\n"
            . "  \"round1_questions\": [\n"
            . "    {\n"
            . "      \"section_key\": \"reasoning|logical|fill_blank\",\n"
            . "      \"question_type\": \"mcq|fill_blank\",\n"
            . "      \"question_text\": \"string\",\n"
            . "      \"options\": [\"A\", \"B\", \"C\", \"D\"],\n"
            . "      \"correct_answer\": \"string\"\n"
            . "    }\n"
            . "  ],\n"
            . "  \"round2_sections\": [\n"
            . "    {\n"
            . "      \"key\": \"reasoning|logical|technical\",\n"
            . "      \"title\": \"Reasoning|Logical|Technical\",\n"
            . "      \"subtitle\": \"string\",\n"
            . "      \"time_limit\": 60,\n"
            . "      \"questions\": [\n"
            . "        {\"question\": \"string\", \"hint\": \"string\"}\n"
            . "      ]\n"
            . "    }\n"
            . "  ]\n"
            . "}\n\n"
            . "Rules:\n"
            . "- Keep the same interviewer persona and voice throughout the full interview set.\n"
            . "- Questions should sound like one experienced interviewer is speaking, not a generic question bank.\n"
            . "- Use a professional, calm, and specific tone.\n"
            . "- Round 1: exactly 6 questions, 4 MCQ + 2 fill_blank.\n"
            . "- Each MCQ must have exactly 4 options and one correct answer from options.\n"
            . "- Fill_blank must contain ____ and provide exact correct_answer.\n"
            . "- Questions must be role-specific and skill-specific, not generic templates.\n"
            . "- Round 2 must contain exactly 3 sections: reasoning, logical, technical.\n"
            . "- Each round2 section must have exactly 2 questions.\n"
            . "- Technical section questions must be concrete and tied to skills.\n"
            . "- Keep language concise and interview-ready.\n\n"
            . "Context:\n" . json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $requestBody = [
            'model' => 'gpt-4o-mini',
            'messages' => [[
                'role' => 'system',
                'content' => 'You are an expert technical interviewer. Return valid JSON only.',
            ], [
                'role' => 'user',
                'content' => $prompt,
            ]],
            'temperature' => 0.35,
            'max_tokens' => 2200,
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($requestBody),
            CURLOPT_TIMEOUT => 60,
        ]);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $curlError !== '' || $httpCode !== 200) {
            $errorDetails = [];
            if ($curlError !== '') {
                $errorDetails[] = 'cURL: ' . $curlError;
            }

            $decodedError = is_string($response) ? json_decode($response, true) : null;
            if (is_array($decodedError) && isset($decodedError['error']) && is_array($decodedError['error'])) {
                $openAiError = $decodedError['error'];
                $messageParts = array_filter([
                    trim((string) ($openAiError['message'] ?? '')),
                    trim((string) ($openAiError['type'] ?? '')),
                    trim((string) ($openAiError['code'] ?? '')),
                ]);
                if (!empty($messageParts)) {
                    $errorDetails[] = 'OpenAI: ' . implode(' | ', $messageParts);
                }
            } elseif (is_string($response) && trim($response) !== '') {
                $errorDetails[] = 'OpenAI response: ' . trim($response);
            }

            log_message(
                'warning',
                'AI interview question generation failed: HTTP ' . $httpCode
                . (empty($errorDetails) ? '' : ' | ' . implode(' ; ', $errorDetails))
            );
            return [];
        }

        $decoded = json_decode((string) $response, true);
        if (is_array($decoded)) {
            (new UsageAnalyticsService())->logOpenAiUsage($decoded, '/v1/chat/completions', 'gpt-4o-mini');
        }
        $content = (string) ($decoded['choices'][0]['message']['content'] ?? '');
        $json = $this->extractJsonObject($content);
        $generated = json_decode($json, true);
        if (!is_array($generated)) {
            return [];
        }

        $normalizedRound1 = $this->normalizeRound1Questions((array) ($generated['round1_questions'] ?? []));
        $normalizedRound2 = $this->normalizeRound2Sections((array) ($generated['round2_sections'] ?? []));
        if (count($normalizedRound1) !== 6 || count($normalizedRound2) !== 3) {
            return [];
        }

        $result = [
            'round1_questions' => $normalizedRound1,
            'round2_sections' => $normalizedRound2,
        ];
        cache()->save($cacheKey, $result, 7200);

        return $result;
    }

    private function extractJsonObject(string $content): string
    {
        $content = preg_replace('/```(?:json)?\s*/i', '', $content) ?? $content;
        $content = preg_replace('/```\s*$/', '', $content) ?? $content;
        $content = trim($content);

        $first = strpos($content, '{');
        $last = strrpos($content, '}');
        if ($first === false || $last === false || $last <= $first) {
            return '{}';
        }

        return substr($content, $first, $last - $first + 1);
    }

    private function normalizeRound2Sections(array $sections): array
    {
        $expected = [
            'reasoning' => ['title' => 'Reasoning', 'subtitle' => 'Show how you think through unfamiliar problems.'],
            'logical' => ['title' => 'Logical', 'subtitle' => 'Break down decisions and tradeoffs with structure.'],
            'technical' => ['title' => 'Technical', 'subtitle' => 'Connect your resume and the role with practical examples.'],
        ];

        $indexed = [];
        foreach ($sections as $section) {
            $key = strtolower(trim((string) ($section['key'] ?? '')));
            if (!isset($expected[$key])) {
                continue;
            }

            $questions = [];
            foreach ((array) ($section['questions'] ?? []) as $q) {
                $question = trim((string) ($q['question'] ?? ''));
                if ($question === '') {
                    continue;
                }
                $questions[] = [
                    'question' => $question,
                    'hint' => trim((string) ($q['hint'] ?? 'Tie your answer to a concrete example, result, or technical decision.')),
                ];
            }

            if (count($questions) < 2) {
                continue;
            }

            $indexed[$key] = [
                'key' => $key,
                'title' => $expected[$key]['title'],
                'subtitle' => (string) ($section['subtitle'] ?? $expected[$key]['subtitle']),
                'time_limit' => 60,
                'questions' => array_slice($questions, 0, 2),
            ];
        }

        $normalized = [];
        foreach (['reasoning', 'logical', 'technical'] as $key) {
            if (!isset($indexed[$key])) {
                return [];
            }
            $normalized[] = $indexed[$key];
        }

        return $normalized;
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

    private function storeInterviewMedia(
        \CodeIgniter\HTTP\Files\UploadedFile $file,
        int $candidateId,
        int $sessionId,
        string $sectionKey,
        int $questionIndex
    ): string {
        $basePath = WRITEPATH . 'uploads/interview-recordings/';
        $relativeBase = 'uploads/interview-recordings/';
        $folder = 'candidate_' . $candidateId . '/session_' . $sessionId . '/';
        $targetDir = $basePath . $folder;

        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $safeExt = strtolower((string) $file->getExtension());
        if ($safeExt === '') {
            $safeExt = 'webm';
        }

        $filename = sprintf(
            '%s_q%s_%s.%s',
            $sectionKey,
            $questionIndex + 1,
            date('YmdHis'),
            $safeExt
        );

        $file->move($targetDir, $filename, true);

        return $relativeBase . $folder . $filename;
    }

    private function scoreRound1Answer(string $questionType, string $selected, string $correct): float
    {
        $selectedNorm = strtolower(trim($selected));
        $correctNorm  = strtolower(trim($correct));

        // Exact match — full credit
        if ($selectedNorm === $correctNorm) {
            return 10.0;
        }

        if ($questionType === 'mcq') {
            // Partial credit: selected answer contains the correct answer or vice versa
            if (str_contains($selectedNorm, $correctNorm) || str_contains($correctNorm, $selectedNorm)) {
                return 5.0;
            }
            return 0.0;
        }

        // fill_blank: use similarity for partial credit
        similar_text($selectedNorm, $correctNorm, $percent);

        if ($percent >= 80) {
            return 8.0; // Very close (e.g. "indexes" vs "index")
        }
        if ($percent >= 60) {
            return 5.0; // Partially correct (e.g. "communicate" vs "communication")
        }
        if ($percent >= 40) {
            return 3.0; // Related but not quite right
        }

        return 0.0;
    }

    private function evaluateAnswer(
        string $sectionKey,
        string $questionText,
        ?string $transcript,
        ?int $durationSeconds
    ): array {
        $transcript = trim((string) $transcript);

        if ($this->isNoResponseTranscript($transcript)) {
            return [
                'available' => true,
                'score' => 0.0,
                'feedback' => 'No meaningful response was captured. The answer is scored as 0 because it did not address the question.',
            ];
        }

        if (
            $transcript !== '' &&
            !$this->isTranscriptUnavailableMarker($transcript)
        ) {
            $aiResult = $this->evaluateAnswerWithOpenAi($sectionKey, $questionText, $transcript);
            if ($aiResult !== null) {
                return [
                    'available' => true,
                    'score' => $aiResult['score'],
                    'feedback' => $aiResult['feedback'],
                ];
            }
        }

        return [
            'available' => false,
            'score' => null,
            'feedback' => 'AI score unavailable. This answer could not be evaluated by AI at the moment.',
        ];
    }

    private function evaluateAnswerWithOpenAi(
        string $sectionKey,
        string $questionText,
        string $transcript
    ): ?array {
        $apiKey = trim((string) (getenv('OPENAI_API_KEY') ?: ''));
        if ($apiKey === '') {
            return null;
        }

        $sectionLabels = [
            'reasoning'  => 'Reasoning — how the candidate thinks through unfamiliar problems',
            'logical'    => 'Logical — how the candidate breaks down decisions and tradeoffs',
            'technical'  => 'Technical — how the candidate connects skills and experience to the role',
        ];
        $sectionContext = $sectionLabels[$sectionKey] ?? 'General interview response';

        $prompt = <<<PROMPT
You are an expert technical interviewer evaluating a candidate's spoken answer.

Section: {$sectionContext}
Question: {$questionText}
Candidate Answer (transcript): {$transcript}

Evaluate the answer on these 4 criteria (each scored 0–25):
1. Relevance — does the answer directly address the question?
2. Clarity — is the answer structured and easy to follow?
3. Depth — does the answer show real understanding, not just surface-level?
4. Specificity — does the answer include concrete examples, results, or decisions?

Return strict JSON only:
{
  "score": <integer 0–100, sum of 4 criteria>,
  "feedback": "<2–3 sentence actionable feedback for the candidate>"
}

Rules:
- Score must reflect actual answer quality, not just length.
- Feedback must be specific to this answer, not generic.
- If the answer is off-topic or empty, score below 40.
- Do not return anything outside the JSON object.
PROMPT;

        $requestBody = [
            'model'    => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an expert technical interviewer. Return valid JSON only.'],
                ['role' => 'user',   'content' => $prompt],
            ],
            'temperature' => 0.2,
            'max_tokens'  => 300,
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($requestBody),
            CURLOPT_TIMEOUT    => 20,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($response === false || $curlError !== '' || $httpCode !== 200) {
            log_message('warning', 'AI answer evaluation failed: ' . ($curlError ?: 'HTTP ' . $httpCode));
            return null;
        }

        $decoded = json_decode((string) $response, true);
        if (is_array($decoded)) {
            (new UsageAnalyticsService())->logOpenAiUsage($decoded, '/v1/chat/completions', 'gpt-4o-mini');
        }

        $content   = (string) ($decoded['choices'][0]['message']['content'] ?? '');
        $json      = $this->extractJsonObject($content);
        $result    = json_decode($json, true);

        if (!is_array($result)) {
            return null;
        }

        $score    = (int) ($result['score'] ?? -1);
        $feedback = trim((string) ($result['feedback'] ?? ''));

        if ($score < 0 || $score > 100 || $feedback === '') {
            return null;
        }

        return [
            'score'    => (float) $score,
            'feedback' => $feedback,
        ];
    }

    private function cleanTranscript(string $transcript): string
    {
        $clean = preg_replace('/\s+/', ' ', trim($transcript)) ?? trim($transcript);
        if ($clean === '') {
            return '';
        }

        $tokens = preg_split('/\s+/', $clean) ?: [];
        $result = [];
        $previousNorm = '';
        $runCount = 0;
        $fillerWords = [
            'a', 'an', 'the', 'if', 'uh', 'um', 'like', 'so', 'and', 'but', 'to', 'of', 'in', 'on', 'for',
        ];

        foreach ($tokens as $token) {
            $norm = strtolower(preg_replace('/[^a-z0-9]/i', '', $token) ?? '');
            if ($norm === '') {
                continue;
            }

            if ($norm === $previousNorm) {
                $runCount++;
            } else {
                $runCount = 1;
                $previousNorm = $norm;
            }

            $maxRun = in_array($norm, $fillerWords, true) ? 1 : 2;
            if ($runCount <= $maxRun) {
                $result[] = $token;
            }
        }

        // Compress repeated short phrases such as:
        // "I would set up I would set up I would set up ..."
        $result = $this->compressRepeatedPhrases($result);

        $joined = trim(implode(' ', $result));
        $joined = preg_replace('/\s+([,.;:!?])/', '$1', $joined) ?? $joined;
        $joined = preg_replace('/([,.;:!?]){2,}/', '$1', $joined) ?? $joined;

        if ($joined !== '' && !preg_match('/[.!?]$/', $joined)) {
            $joined .= '.';
        }

        return ucfirst($joined);
    }

    private function compressRepeatedPhrases(array $tokens): array
    {
        $normalized = array_map(static function ($token): string {
            return strtolower((string) (preg_replace('/[^a-z0-9]/i', '', (string) $token) ?? ''));
        }, $tokens);

        $count = count($tokens);
        if ($count < 6) {
            return $tokens;
        }

        $output = [];
        $i = 0;
        while ($i < $count) {
            $bestLen = 0;
            $bestRepeats = 1;
            $maxWindow = min(8, intdiv($count - $i, 2));

            for ($len = 2; $len <= $maxWindow; $len++) {
                $repeats = 1;
                while (($i + ($repeats + 1) * $len) <= $count) {
                    $same = true;
                    for ($k = 0; $k < $len; $k++) {
                        $left = $normalized[$i + $k] ?? '';
                        $right = $normalized[$i + $repeats * $len + $k] ?? '';
                        if ($left === '' || $right === '' || $left !== $right) {
                            $same = false;
                            break;
                        }
                    }
                    if (!$same) {
                        break;
                    }
                    $repeats++;
                }

                if ($repeats >= 2 && $len > $bestLen) {
                    $bestLen = $len;
                    $bestRepeats = $repeats;
                }
            }

            if ($bestLen > 0 && $bestRepeats >= 2) {
                for ($k = 0; $k < $bestLen; $k++) {
                    $output[] = $tokens[$i + $k];
                }
                $i += ($bestLen * $bestRepeats);
                continue;
            }

            $output[] = $tokens[$i];
            $i++;
        }

        return $output;
    }

    private function transcribeUploadedAnswer(string $absolutePath): ?string
    {
        try {
            $transcriber = new AiInterviewTranscriber();
            $text = $transcriber->transcribeFile($absolutePath);
            if ($text === null) {
                return null;
            }

            $clean = $this->cleanTranscript($text);
            return $clean !== '' ? $clean : null;
        } catch (\Throwable $e) {
            log_message('warning', 'Interview server transcription error: ' . $e->getMessage());
            return null;
        }
    }

    private function selectBestTranscript(string $browserTranscriptRaw, ?string $serverTranscript): ?string
    {
        $browserTranscriptRaw = trim($browserTranscriptRaw);
        $browserTranscript    = ($browserTranscriptRaw !== '' && !$this->isTranscriptUnavailableMarker($browserTranscriptRaw))
            ? $this->cleanTranscript($browserTranscriptRaw)
            : '';

        $serverTranscript = trim((string) ($serverTranscript ?? ''));

        // Server transcript (Whisper) is always more accurate than browser Web Speech API.
        // Prefer it unconditionally when available and non-trivial.
        if ($serverTranscript !== '' && strlen($serverTranscript) >= 10) {
            return $serverTranscript;
        }

        // Fall back to browser transcript only if server produced nothing.
        if ($browserTranscript !== '') {
            return $browserTranscript;
        }

        return '[Transcript unavailable: no reliable transcript generated.]';
    }

    private function isTranscriptUnavailableMarker(string $value): bool
    {
        return stripos($value, '[Transcript unavailable:') === 0;
    }

    private function isNoResponseTranscript(string $value): bool
    {
        $value = trim($value);
        if ($value === '') {
            return true;
        }

        $normalized = strtolower($value);
        return str_contains($normalized, 'no speech detected')
            || str_contains($normalized, 'no reliable transcript generated')
            || str_contains($normalized, 'thank you for watching')
            || str_contains($normalized, 'answer does not address the question');
    }

    private function buildRound1Questions(string $jobTitle, array $focusSkills, string $selectionSeed): array
    {
        $roleContext = $this->buildRoleQuestionContext($jobTitle, $focusSkills);
        $family = (string) ($roleContext['role_family'] ?? 'general');
        $primarySkill = $roleContext['primary_skill'];
        $secondarySkill = $roleContext['secondary_skill'];
        $domainLabel = $roleContext['domain_label'];
        $domainAction = $roleContext['domain_action'];

        $basePool = [
            [
                'section_key' => 'reasoning',
                'question_type' => 'mcq',
                'question_text' => 'In a ' . $domainLabel . ' issue, what should be your first step?',
                'options' => [
                    'Change multiple components without diagnosis',
                    'Identify impact, gather evidence, then act',
                    'Wait for more complaints before acting',
                    'Ignore and continue planned work',
                ],
                'correct_answer' => 'Identify impact, gather evidence, then act',
            ],
            [
                'section_key' => 'logical',
                'question_type' => 'mcq',
                'question_text' => 'For the ' . $jobTitle . ' role, which approach is most reliable under deadline pressure?',
                'options' => [
                    'Skip verification and ship quickly',
                    'Deliver the smallest safe increment with validation',
                    'Delay until perfect architecture appears',
                    'Rewrite all modules immediately',
                ],
                'correct_answer' => 'Deliver the smallest safe increment with validation',
            ],
            [
                'section_key' => 'fill_blank',
                'question_type' => 'fill_blank',
                'question_text' => 'Fill in the blank: In code reviews, clear ____ helps teams maintain quality.',
                'correct_answer' => 'communication',
            ],
            [
                'section_key' => 'fill_blank',
                'question_type' => 'fill_blank',
                'question_text' => 'Fill in the blank: Strong ____ improves ' . $domainAction . ' quality.',
                'correct_answer' => $primarySkill,
            ],
            [
                'section_key' => 'reasoning',
                'question_type' => 'mcq',
                'question_text' => 'Which is the best signal of a maintainable solution in ' . $secondarySkill . '?',
                'options' => [
                    'No documentation and no tests',
                    'Clear modules, readable code, and test coverage',
                    'One very large function for everything',
                    'Hardcoded values across all files',
                ],
                'correct_answer' => 'Clear modules, readable code, and test coverage',
            ],
            [
                'section_key' => 'logical',
                'question_type' => 'mcq',
                'question_text' => 'Which decision best protects delivery quality when a deadline is at risk?',
                'options' => [
                    'Skip validation to save time',
                    'Narrow scope and preserve critical quality checks',
                    'Freeze work and wait for next release cycle',
                    'Rebuild the full module from scratch',
                ],
                'correct_answer' => 'Narrow scope and preserve critical quality checks',
            ],
            [
                'section_key' => 'fill_blank',
                'question_type' => 'fill_blank',
                'question_text' => 'Fill in the blank: Good ____ helps teams detect issues early.',
                'correct_answer' => 'monitoring',
            ],
        ];

        $rolePools = [
            'backend' => [
                [
                    'section_key' => 'reasoning',
                    'question_type' => 'mcq',
                    'question_text' => 'For backend APIs, what is the safest default when handling unexpected input?',
                    'options' => [
                        'Accept all input silently',
                        'Validate input and return clear errors',
                        'Retry endlessly without logging',
                        'Disable authentication temporarily',
                    ],
                    'correct_answer' => 'Validate input and return clear errors',
                ],
                [
                    'section_key' => 'fill_blank',
                    'question_type' => 'fill_blank',
                    'question_text' => 'Fill in the blank: Proper ____ design improves query performance.',
                    'correct_answer' => 'index',
                ],
            ],
            'frontend' => [
                [
                    'section_key' => 'logical',
                    'question_type' => 'mcq',
                    'question_text' => 'When UI feels slow, what should you measure first?',
                    'options' => [
                        'Only color palette',
                        'Render time and interaction latency',
                        'Team attendance',
                        'Logo size',
                    ],
                    'correct_answer' => 'Render time and interaction latency',
                ],
                [
                    'section_key' => 'fill_blank',
                    'question_type' => 'fill_blank',
                    'question_text' => 'Fill in the blank: Consistent ____ keeps UI behavior predictable.',
                    'correct_answer' => 'components',
                ],
            ],
            'devops' => [
                [
                    'section_key' => 'reasoning',
                    'question_type' => 'mcq',
                    'question_text' => 'Before production deployment, what is most important?',
                    'options' => [
                        'Skip rollback plan',
                        'Ensure rollout strategy and rollback readiness',
                        'Deploy directly from local machine',
                        'Disable alerts to avoid noise',
                    ],
                    'correct_answer' => 'Ensure rollout strategy and rollback readiness',
                ],
                [
                    'section_key' => 'fill_blank',
                    'question_type' => 'fill_blank',
                    'question_text' => 'Fill in the blank: Reliable ____ reduces mean time to recovery.',
                    'correct_answer' => 'alerting',
                ],
            ],
            'qa' => [
                [
                    'section_key' => 'logical',
                    'question_type' => 'mcq',
                    'question_text' => 'Which test should run first for high-risk release areas?',
                    'options' => [
                        'Random low-priority tests',
                        'Risk-based critical path tests',
                        'No tests if code compiles',
                        'Only UI color checks',
                    ],
                    'correct_answer' => 'Risk-based critical path tests',
                ],
                [
                    'section_key' => 'fill_blank',
                    'question_type' => 'fill_blank',
                    'question_text' => 'Fill in the blank: Clear bug ____ speeds up triage and fixes.',
                    'correct_answer' => 'reports',
                ],
            ],
            'data' => [
                [
                    'section_key' => 'reasoning',
                    'question_type' => 'mcq',
                    'question_text' => 'When two dashboards conflict, what should you do first?',
                    'options' => [
                        'Pick the larger number',
                        'Trace data source, filters, and definitions',
                        'Delete one dashboard',
                        'Ignore mismatch',
                    ],
                    'correct_answer' => 'Trace data source, filters, and definitions',
                ],
                [
                    'section_key' => 'fill_blank',
                    'question_type' => 'fill_blank',
                    'question_text' => 'Fill in the blank: Strong data ____ improves reporting trust.',
                    'correct_answer' => 'validation',
                ],
            ],
            'design' => [
                [
                    'section_key' => 'logical',
                    'question_type' => 'mcq',
                    'question_text' => 'What is the best way to validate a new UX flow quickly?',
                    'options' => [
                        'Launch globally without testing',
                        'Prototype and test with representative users',
                        'Decide based only on visual preference',
                        'Skip user feedback completely',
                    ],
                    'correct_answer' => 'Prototype and test with representative users',
                ],
                [
                    'section_key' => 'fill_blank',
                    'question_type' => 'fill_blank',
                    'question_text' => 'Fill in the blank: Design ____ improves developer handoff quality.',
                    'correct_answer' => 'specs',
                ],
            ],
            'general' => [
                [
                    'section_key' => 'reasoning',
                    'question_type' => 'mcq',
                    'question_text' => 'When priorities conflict, what is the best next action?',
                    'options' => [
                        'Work on everything at once',
                        'Clarify impact and align on a ranked priority list',
                        'Ignore business goals',
                        'Delay all tasks',
                    ],
                    'correct_answer' => 'Clarify impact and align on a ranked priority list',
                ],
                [
                    'section_key' => 'fill_blank',
                    'question_type' => 'fill_blank',
                    'question_text' => 'Fill in the blank: Clear team ____ prevents delivery confusion.',
                    'correct_answer' => 'communication',
                ],
            ],
        ];

        $defaults = array_merge($basePool, $rolePools[$family] ?? $rolePools['general']);

        return $this->normalizeRound1Questions(
            $this->seededShuffleAndSlice($defaults, $selectionSeed . '|round1_defaults', 6)
        );
    }

    private function calculateRound1Score(int $sessionId): float
    {
        if (!\Config\Database::connect()->tableExists('ai_interview_round1_attempts')) {
            return 0.0;
        }

        $attemptModel = new AiInterviewRound1AttemptModel();
        $rows = $attemptModel->findBySession($sessionId);
        if (empty($rows)) {
            return 0.0;
        }

        $obtained = array_sum(array_map(static fn ($row) => (float) ($row['score'] ?? 0), $rows));
        $possible = array_sum(array_map(static fn ($row) => (float) ($row['max_score'] ?? 0), $rows));
        if ($possible <= 0) {
            return 0.0;
        }

        return round(($obtained / $possible) * 100, 2);
    }

    private function buildInterviewResumeSnapshot(array $application, array $flow, array $session): array
    {
        $round1Questions = (array) ($flow['round1_questions'] ?? []);
        $round2Sections = (array) ($flow['round2_sections'] ?? $flow['sections'] ?? []);
        $round1Total = count($round1Questions);
        $round2Total = array_sum(array_map(static fn ($section) => count((array) ($section['questions'] ?? [])), $round2Sections));
        $adaptiveState = $this->getAdaptiveInterviewState($session);
        $pendingFollowup = is_array($adaptiveState['pending_followup'] ?? null) ? $adaptiveState['pending_followup'] : null;

        $round1Attempts = [];
        if (\Config\Database::connect()->tableExists('ai_interview_round1_attempts')) {
            $round1Attempts = (new AiInterviewRound1AttemptModel())
                ->where('interview_session_id', (int) ($session['id'] ?? 0))
                ->findAll();
        }

        $answers = [];
        if (\Config\Database::connect()->tableExists('interview_session_answers')) {
            $answers = (new InterviewSessionAnswerModel())
                ->where('interview_session_id', (int) ($session['id'] ?? 0))
                ->orderBy('section_key', 'ASC')
                ->orderBy('question_index', 'ASC')
                ->findAll();
        }

        $round1Answered = count($round1Attempts);
        $round2Answered = count(array_filter($answers, static fn ($row) => strtolower((string) ($row['answer_variant'] ?? 'base')) === 'base'));
        $status = (string) ($session['status'] ?? 'active');
        $finished = in_array($status, ['submitted', 'under_review', 'finalized', 'candidate_notified', 'completed', 'pending_evaluation', 'evaluated', 'expired'], true);
        $phase = $round1Answered < $round1Total ? 'round1' : 'round2';
        if ($pendingFollowup) {
            $phase = 'round2';
        }

        $sectionIndex = 0;
        $questionIndex = 0;
        if ($pendingFollowup) {
            $sectionIndex = $this->findRound2SectionIndexByKey($round2Sections, (string) ($pendingFollowup['section_key'] ?? ''));
            $questionIndex = max(0, (int) ($pendingFollowup['question_index'] ?? 0));
        } elseif ($phase === 'round2') {
            [$sectionIndex, $questionIndex] = $this->resolveRound2ResumePosition($round2Sections, $answers);
        } else {
            $questionIndex = min(max(0, $round1Answered), max(0, $round1Total - 1));
        }

        $createdAt = !empty($session['created_at']) ? strtotime((string) $session['created_at']) : false;
        $totalSeconds = (int) ($session['interview_total_seconds'] ?? 1800);
        $remainingSeconds = null;
        $sessionEndsAt = null;
        if ($createdAt) {
            $sessionEndsAt = $createdAt + $totalSeconds;
            $remainingSeconds = max(0, $sessionEndsAt - time());
        }

        return [
            'application_id' => (int) ($application['id'] ?? 0),
            'interview_session_id' => (int) ($session['id'] ?? 0),
            'session_uid' => (string) ($session['session_id'] ?? ''),
            'status' => $status,
            'started' => true,
            'finished' => $finished,
            'phase' => $phase,
            'round1_index' => max(0, min($round1Answered, max(0, $round1Total - 1))),
            'round1_saved_count' => $round1Answered,
            'section_index' => max(0, $sectionIndex),
            'question_index' => max(0, $questionIndex),
            'round2_saved_count' => $round2Answered,
            'round1_total_questions' => $round1Total,
            'round2_total_questions' => $round2Total,
            'remaining_seconds' => $remainingSeconds,
            'session_ends_at' => $sessionEndsAt,
            'created_at' => $session['created_at'] ?? null,
            'followup_pending' => (bool) $pendingFollowup,
            'followup_question' => $pendingFollowup['question_text'] ?? null,
            'followup_type' => $pendingFollowup['followup_type'] ?? null,
            'followup_reason' => $pendingFollowup['reason'] ?? null,
            'followup_section_key' => $pendingFollowup['section_key'] ?? null,
            'followup_question_index' => $pendingFollowup['question_index'] ?? null,
        ];
    }

    private function isInterviewSessionExpired(array $session): bool
    {
        $status = strtolower((string) ($session['status'] ?? ''));
        if ($status !== 'active') {
            return false;
        }

        $createdAt = !empty($session['created_at']) ? strtotime((string) $session['created_at']) : false;
        if (!$createdAt) {
            return false;
        }

        return (time() - $createdAt) >= self::INTERVIEW_SESSION_EXPIRY_SECONDS;
    }

    private function expireInterviewSession(InterviewSessionModel $sessionModel, array $session): array
    {
        $sessionId = (int) ($session['id'] ?? 0);
        if ($sessionId <= 0) {
            return $session;
        }

        $now = date('Y-m-d H:i:s');
        $sessionModel->update($sessionId, [
            'status' => 'expired',
            'completed_at' => $session['completed_at'] ?? $now,
            'updated_at' => $now,
        ]);
        log_message('info', 'Interview session expired after ' . self::INTERVIEW_SESSION_EXPIRY_SECONDS . ' seconds: session_id=' . $sessionId . ', application_id=' . (int) ($session['application_id'] ?? 0) . ', user_id=' . (int) ($session['user_id'] ?? 0));

        $session['status'] = 'expired';
        $session['completed_at'] = $session['completed_at'] ?? $now;
        $session['updated_at'] = $now;

        return $session;
    }

    private function resolveRound2ResumePosition(array $round2Sections, array $answers): array
    {
        $saved = [];
        foreach ($answers as $answer) {
            if (strtolower((string) ($answer['answer_variant'] ?? 'base')) !== 'base') {
                continue;
            }
            $sectionKey = strtolower(trim((string) ($answer['section_key'] ?? '')));
            $questionIndex = (int) ($answer['question_index'] ?? -1);
            if ($sectionKey === '' || $questionIndex < 0) {
                continue;
            }
            $saved[$sectionKey . '|' . $questionIndex] = true;
        }

        foreach ($round2Sections as $sectionIndex => $section) {
            $sectionKey = strtolower(trim((string) ($section['key'] ?? '')));
            $questions = (array) ($section['questions'] ?? []);
            foreach ($questions as $questionIndex => $question) {
                if (!isset($saved[$sectionKey . '|' . $questionIndex])) {
                    return [$sectionIndex, $questionIndex];
                }
            }
        }

        $lastSectionIndex = max(0, count($round2Sections) - 1);
        $lastQuestionIndex = 0;
        if (!empty($round2Sections)) {
            $lastQuestions = (array) ($round2Sections[$lastSectionIndex]['questions'] ?? []);
            $lastQuestionIndex = max(0, count($lastQuestions) - 1);
        }

        return [$lastSectionIndex, $lastQuestionIndex];
    }

    private function findRound2SectionIndexByKey(array $round2Sections, string $sectionKey): int
    {
        foreach ($round2Sections as $sectionIndex => $section) {
            if (strtolower(trim((string) ($section['key'] ?? ''))) === strtolower(trim($sectionKey))) {
                return (int) $sectionIndex;
            }
        }

        return 0;
    }

    private function calculateRound2Score(int $sessionId): ?float
    {
        if (!\Config\Database::connect()->tableExists('interview_session_answers')) {
            return null;
        }

        $answerModel = new InterviewSessionAnswerModel();
        $rows = $answerModel->where('interview_session_id', $sessionId)->findAll();
        if (empty($rows)) {
            return null;
        }

        foreach ($rows as $row) {
            if (($row['ai_score'] ?? null) === null) {
                return null;
            }
        }

        $scores = array_map(static fn ($row) => (float) ($row['ai_score'] ?? 0), $rows);
        return round(array_sum($scores) / count($scores), 2);
    }

    private function hasPendingAiEvaluation(int $sessionId): bool
    {
        if (!\Config\Database::connect()->tableExists('interview_session_answers')) {
            return true;
        }

        $answerModel = new InterviewSessionAnswerModel();
        $rows = $answerModel->where('interview_session_id', $sessionId)->findAll();
        if (empty($rows)) {
            return true;
        }

        foreach ($rows as $row) {
            if (($row['ai_score'] ?? null) === null) {
                return true;
            }
        }

        return false;
    }

    private function calculateRound2SectionScores(int $sessionId): array
    {
        if (!\Config\Database::connect()->tableExists('interview_session_answers')) {
            return [];
        }

        $answerModel = new InterviewSessionAnswerModel();
        $rows = $answerModel->where('interview_session_id', $sessionId)->findAll();
        if (empty($rows)) {
            return [];
        }

        $bucketed = [];
        foreach ($rows as $row) {
            $sectionKey = strtolower(trim((string) ($row['section_key'] ?? '')));
            $score = $row['ai_score'] ?? null;
            if ($sectionKey === '' || $score === null) {
                continue;
            }

            if (!isset($bucketed[$sectionKey])) {
                $bucketed[$sectionKey] = [];
            }
            $bucketed[$sectionKey][] = (float) $score;
        }

        $sectionScores = [];
        foreach ($bucketed as $sectionKey => $scores) {
            if (!empty($scores)) {
                $sectionScores[$sectionKey] = round(array_sum($scores) / count($scores), 2);
            }
        }

        ksort($sectionScores);
        return $sectionScores;
    }

    private function resolveAiDecision(float $overallRating): string
    {
        if ($overallRating >= 7.5) {
            return 'qualified';
        }
        if ($overallRating >= 6.0) {
            return 'needs_review';
        }
        return 'rejected';
    }

    private function buildRecommendationSummary(
        float $overallRating,
        float $technicalScore,
        float $communicationScore,
        float $problemSolvingScore
    ): string {
        return sprintf(
            'Overall %.2f/10. Technical %.1f, Communication %.1f, Problem Solving %.1f. Focus interview follow-ups on the lowest scoring area for deeper validation.',
            $overallRating,
            $technicalScore,
            $communicationScore,
            $problemSolvingScore
        );
    }

    private function buildStrengthsAndConcerns(
        float $technicalScore,
        float $communicationScore,
        float $problemSolvingScore
    ): array {
        $areas = [
            'Technical depth' => $technicalScore,
            'Communication clarity' => $communicationScore,
            'Problem solving approach' => $problemSolvingScore,
        ];

        arsort($areas);
        $labels = array_keys($areas);
        $scores = array_values($areas);

        $strengths = [];
        if (($scores[0] ?? 0) > 0) {
            $strengths[] = $labels[0] . ' appears strongest in this interview round.';
        }
        if (($scores[1] ?? 0) >= 70) {
            $strengths[] = $labels[1] . ' is consistently above baseline expectations.';
        }

        $concerns = [];
        if (($scores[count($scores) - 1] ?? 0) > 0) {
            $concerns[] = $labels[count($labels) - 1] . ' needs deeper probing in next discussion round.';
        }
        if (($scores[count($scores) - 1] ?? 0) < 60) {
            $concerns[] = 'Lowest scoring area is below preferred benchmark and may require support or validation.';
        }

        return [$strengths, $concerns];
    }

    private function buildInterviewSelectionSeed(array $application): string
    {
        $candidateId = (int) ($application['candidate_id'] ?? 0);
        $applicationId = (int) ($application['id'] ?? 0);
        $jobId = (int) ($application['job_id'] ?? 0);
        $jobTitle = (string) ($application['job_title'] ?? '');

        return sha1($candidateId . '|' . $applicationId . '|' . $jobId . '|' . strtolower(trim($jobTitle)));
    }

    private function seededShuffleAndSlice(array $items, string $seed, int $limit): array
    {
        $indexed = [];
        foreach (array_values($items) as $index => $item) {
            $signature = is_array($item)
                ? json_encode($item, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                : (string) $item;
            $sortKey = sha1($seed . '|' . $index . '|' . $signature);
            $indexed[] = ['key' => $sortKey, 'item' => $item];
        }

        usort($indexed, static fn ($a, $b) => strcmp((string) $a['key'], (string) $b['key']));
        $shuffled = array_map(static fn ($row) => $row['item'], $indexed);

        if ($limit <= 0) {
            return $shuffled;
        }

        return array_values(array_slice($shuffled, 0, $limit));
    }

    private function normalizeRound1Questions(array $questions): array
    {
        $normalized = [];
        foreach ($questions as &$question) {
            $type = strtolower((string) ($question['question_type'] ?? 'mcq'));
            $sectionKey = strtolower((string) ($question['section_key'] ?? ($type === 'fill_blank' ? 'fill_blank' : 'reasoning')));
            if (!in_array($sectionKey, ['reasoning', 'logical', 'fill_blank'], true)) {
                $sectionKey = $type === 'fill_blank' ? 'fill_blank' : 'reasoning';
            }
            if (!in_array($type, ['mcq', 'fill_blank'], true)) {
                $type = 'mcq';
            }

            $text = trim((string) ($question['question_text'] ?? ''));
            $answer = trim((string) ($question['correct_answer'] ?? ''));
            if ($text === '' || $answer === '') {
                continue;
            }

            $options = [];
            if ($type === 'mcq') {
                $options = array_values(array_filter(array_map(static fn ($item): string => trim((string) $item), (array) ($question['options'] ?? []))));
                if (count($options) < 4) {
                    continue;
                }
                $options = array_slice($options, 0, 4);
            }

            if ($type !== 'fill_blank') {
                $normalized[] = [
                    'section_key' => $sectionKey,
                    'question_type' => $type,
                    'question_text' => $text,
                    'options' => $options,
                    'correct_answer' => $answer,
                ];
                continue;
            }

            if (strpos($text, '____') === false) {
                if ($answer !== '' && stripos($text, $answer) !== false) {
                    $text = preg_replace('/' . preg_quote($answer, '/') . '/i', '____', $text, 1) ?? $text;
                } elseif (stripos($text, 'Fill in the blank:') === 0) {
                    $text = rtrim($text, '. ') . ' ____.';
                } else {
                    $text = 'Fill in the blank: ' . rtrim($text, '. ') . ' ____.';
                }
            }

            $normalized[] = [
                'section_key' => 'fill_blank',
                'question_type' => 'fill_blank',
                'question_text' => $text,
                'correct_answer' => $answer,
            ];
        }
        unset($question);

        return $normalized;
    }

    private function buildInterviewerPersona(string $jobTitle, string $companyName, array $focusSkills, array $roleContext): array
    {
        $roleFamily = strtolower((string) ($roleContext['role_family'] ?? 'general'));
        $personaMap = [
            'design' => [
                'name' => 'Meera Shah',
                'title' => 'Product Design Interviewer',
                'tone' => 'Warm, structured, and evidence-driven',
                'style' => 'I ask for practical examples, tradeoffs, and impact on users.',
            ],
            'data' => [
                'name' => 'Rohan Iyer',
                'title' => 'Data Interviewer',
                'tone' => 'Analytical, concise, and probing',
                'style' => 'I look for reasoning, metrics, and clarity in technical decisions.',
            ],
            'qa' => [
                'name' => 'Ananya Rao',
                'title' => 'Quality Interviewer',
                'tone' => 'Calm, precise, and quality-focused',
                'style' => 'I focus on test strategy, risk, and how you prevent regressions.',
            ],
            'devops' => [
                'name' => 'Karan Mehta',
                'title' => 'Platform Interviewer',
                'tone' => 'Direct, practical, and systems-minded',
                'style' => 'I care about reliability, operational clarity, and incident response.',
            ],
            'general' => [
                'name' => 'Priya Nair',
                'title' => 'Technical Interviewer',
                'tone' => 'Professional, steady, and friendly',
                'style' => 'I keep the interview focused, fair, and grounded in real work examples.',
            ],
        ];

        $persona = $personaMap[$roleFamily] ?? $personaMap['general'];
        $primarySkill = (string) ($focusSkills[0] ?? ($roleContext['primary_skill'] ?? 'the core requirements'));
        $secondarySkill = (string) ($focusSkills[1] ?? ($roleContext['secondary_skill'] ?? 'clear communication'));
        $companyContext = $companyName !== '' ? ' for ' . $companyName : '';

        $persona['opening_title'] = 'Interview overview';
        $persona['opening_message'] = 'Hi {candidate_name}, I am ' . $persona['name'] . ', your ' . $persona['title'] . '. '
            . 'I will keep this interview structured, professional, and consistent from start to finish. '
            . 'We will begin with a short written screening, then move into role-specific responses' . $companyContext . '. '
            . 'I will look for evidence around ' . $primarySkill . ' and how clearly you explain your thinking about ' . $secondarySkill . '.';
        $persona['prestart_message'] = 'Your interviewer will greet you first, then the written screening will begin.';
        $persona['transition_message'] = 'Answer each question directly, then expand with one concrete example if you have one.';
        $persona['closing_message'] = 'That completes the interview flow. I will now summarize your performance for review.';
        $persona['questioning_style'] = [
            'Ask one question at a time and stay consistent in tone.',
            'Keep follow-ups focused on evidence, tradeoffs, and outcomes.',
            'Use clear transitions between rounds so the candidate always knows what comes next.',
        ];

        return $persona;
    }

    private function buildRoleQuestionContext(string $jobTitle, array $focusSkills): array
    {
        $title = strtolower($jobTitle);
        $context = [
            'role_family' => 'general',
            'domain_label' => 'product engineering',
            'domain_action' => 'delivery',
            'primary_skill' => $focusSkills[0] ?? 'technical fundamentals',
            'secondary_skill' => $focusSkills[1] ?? 'problem solving',
        ];

        if (preg_match('/design|ui|ux/', $title)) {
            return [
                'role_family' => 'design',
                'domain_label' => 'design system',
                'domain_action' => 'user experience',
                'primary_skill' => $focusSkills[0] ?? 'user research',
                'secondary_skill' => $focusSkills[1] ?? 'interaction design',
            ];
        }

        if (preg_match('/data|analyst|bi|ml|ai/', $title)) {
            return [
                'role_family' => 'data',
                'domain_label' => 'data pipeline',
                'domain_action' => 'analysis',
                'primary_skill' => $focusSkills[0] ?? 'data modeling',
                'secondary_skill' => $focusSkills[1] ?? 'query optimization',
            ];
        }

        if (preg_match('/qa|test|quality/', $title)) {
            return [
                'role_family' => 'qa',
                'domain_label' => 'quality assurance',
                'domain_action' => 'release confidence',
                'primary_skill' => $focusSkills[0] ?? 'test strategy',
                'secondary_skill' => $focusSkills[1] ?? 'defect analysis',
            ];
        }

        if (preg_match('/devops|cloud|sre|infra/', $title)) {
            return [
                'role_family' => 'devops',
                'domain_label' => 'deployment pipeline',
                'domain_action' => 'system reliability',
                'primary_skill' => $focusSkills[0] ?? 'infrastructure automation',
                'secondary_skill' => $focusSkills[1] ?? 'monitoring',
            ];
        }

        if (preg_match('/frontend|react|angular|vue/', $title)) {
            return [
                'role_family' => 'frontend',
                'domain_label' => 'frontend application',
                'domain_action' => 'interface performance',
                'primary_skill' => $focusSkills[0] ?? 'component architecture',
                'secondary_skill' => $focusSkills[1] ?? 'state management',
            ];
        }

        if (preg_match('/backend|php|java|python|node/', $title)) {
            return [
                'role_family' => 'backend',
                'domain_label' => 'backend service',
                'domain_action' => 'API reliability',
                'primary_skill' => $focusSkills[0] ?? 'service design',
                'secondary_skill' => $focusSkills[1] ?? 'database design',
            ];
        }

        return $context;
    }

    private function buildTechnicalQuestionSet(
        string $jobTitle,
        array $roleContext,
        array $focusSkills,
        string $resumeSummary,
        string $selectionSeed
    ): array {
        $primary = $roleContext['primary_skill'] ?? ($focusSkills[0] ?? 'core skill');
        $secondary = $roleContext['secondary_skill'] ?? ($focusSkills[1] ?? 'secondary skill');
        $family = $roleContext['role_family'] ?? 'general';
        $domain = $roleContext['domain_label'] ?? 'product system';
        $skill3 = $focusSkills[2] ?? 'cross-team collaboration';
        $roleLabel = trim($jobTitle) !== '' ? trim($jobTitle) : 'this role';

        $templates = [
            'general' => [
                "Walk through a recent task where {$primary} changed the final outcome.",
                "When requirements are ambiguous in {$domain}, how do you de-risk before implementation?",
                "Describe a production/debugging issue you handled and how you validated the fix.",
                "If performance drops after release, what measurements and actions do you take first?",
                "Tell me about a time you had to simplify a complex idea for non-technical stakeholders.",
                "What signals tell you a solution is production-ready versus only demo-ready?",
            ],
            'backend' => [
                "Design an API endpoint for {$domain}. What contract, validation, and error strategy would you use?",
                "Explain a database optimization you did using {$secondary}, and the before/after impact.",
                "How would you make a {$roleLabel} service resilient during traffic spikes?",
                "Describe your approach to logging, tracing, and root-cause analysis in backend incidents.",
                "How do you approach idempotency, retries, and failure handling for critical workflows?",
                "Describe a breaking-change risk you found in an API design and how you handled it.",
            ],
            'frontend' => [
                "How do you structure components using {$primary} for long-term maintainability?",
                "Describe a rendering/performance issue you solved and how you measured improvement.",
                "When product asks for rapid UI changes, how do you keep consistency in the design system?",
                "Explain one accessibility decision you made and why it mattered for users.",
                "How do you decide what state belongs locally versus globally in a complex UI?",
                "Describe a bug caused by async UI behavior and how you prevented regressions.",
            ],
            'devops' => [
                "How would you design CI/CD for {$roleLabel} work to keep releases safe and fast?",
                "Describe an incident where monitoring/alerting helped you prevent extended downtime.",
                "What rollback strategy would you define for a risky infrastructure deployment?",
                "How do you balance cost optimization with reliability in cloud environments?",
                "How do you pick SLO/SLI targets that are meaningful for users, not just systems?",
                "Describe one deployment guardrail you introduced that prevented outages.",
            ],
            'qa' => [
                "How do you decide what to automate first in a fast-moving release cycle?",
                "Describe a high-impact defect you found and how you prevented recurrence.",
                "How would you design test coverage for {$domain} with limited time?",
                "What quality gates should block release, and which should only warn?",
                "How do you build confidence when requirements are changing mid-sprint?",
                "Describe how you balance flaky-test cleanup versus new test creation.",
            ],
            'data' => [
                "How do you validate data quality before publishing insights to stakeholders?",
                "Describe a query/model optimization using {$secondary} and its business impact.",
                "How would you explain a conflicting metric result to non-technical teams?",
                "What checks would you add to prevent data drift in recurring reports/pipelines?",
                "Describe a case where metric definition changes altered business decisions.",
                "How do you decide between batch and near-real-time data approaches?",
            ],
            'design' => [
                "How did you use {$primary} to influence a product decision in a real project?",
                "Describe how you balanced UX quality with engineering constraints.",
                "How do you evaluate whether a new flow improves user outcomes?",
                "Walk through a handoff where design specs avoided rework for engineering.",
                "How do you resolve conflicts between usability findings and business deadlines?",
                "Describe a design tradeoff you made and what evidence supported it.",
            ],
        ];

        $selected = $templates[$family] ?? $templates['general'];
        $skillAnchored = [];
        foreach (array_slice($focusSkills, 0, 4) as $skill) {
            $skill = trim((string) $skill);
            if ($skill === '') {
                continue;
            }

            $skillAnchored[] = "Pick one project where you used {$skill}. What constraints shaped your implementation choices?";
            $skillAnchored[] = "What failure mode do teams commonly miss with {$skill}, and how do you catch it early?";
        }

        $crossFunctional = [
            "How do you prioritize technical debt for {$roleLabel} work when delivery pressure is high?",
            "Describe a disagreement with a teammate on implementation direction and how you resolved it with evidence.",
            "What do you measure after release to confirm your solution actually solved the user problem?",
            "If your first approach fails in {$domain}, how do you decide whether to iterate or pivot?",
        ];

        $selected = array_merge($skillAnchored, $selected, $crossFunctional);
        if ($resumeSummary !== '') {
            $selected[] = "From your resume summary, what proof points best demonstrate {$skill3} for {$roleLabel}?";
        }

        $unique = $this->uniqueQuestionPool($selected);
        return $this->seededShuffleAndSlice($unique, $selectionSeed . '|round2_technical', 4);
    }

    private function uniqueQuestionPool(array $questions): array
    {
        $seen = [];
        $result = [];
        foreach ($questions as $question) {
            $text = trim((string) $question);
            if ($text === '') {
                continue;
            }

            $key = strtolower(preg_replace('/\s+/', ' ', $text) ?? $text);
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $result[] = $text;
        }

        return $result;
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
