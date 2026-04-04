<?= view('Layouts/candidate_header', ['title' => 'AI Interview Flow']) ?>
<?php
$application        = $application ?? [];
$flow               = $interviewFlow ?? [];
$interviewCompleted = $interviewCompleted ?? false;
$completedSession   = $completedSession ?? [];
$round1Questions    = $flow['round1_questions'] ?? [];
$round2Sections     = $flow['round2_sections'] ?? ($flow['sections'] ?? []);
$sections           = $round2Sections;
$jobTitle           = (string) ($flow['job_title'] ?? ($application['job_title'] ?? 'AI Interview'));
$companyName        = trim((string) ($flow['company_name'] ?? ($application['company'] ?? '')));
$resumeTitle        = trim((string) ($flow['resume_title'] ?? ($application['resume_version_title'] ?? '')));
$resumeSummary      = trim((string) ($flow['resume_summary'] ?? ($application['resume_version_summary'] ?? '')));
$focusSkills        = $flow['focus_skills'] ?? [];
$timerSeconds       = (int) ($flow['timer_seconds'] ?? 60);
$totalTimerSeconds  = (int) ($flow['total_timer_seconds'] ?? 1800);
$applicationStatus  = (string) ($application['status'] ?? '');
$sessionStatus      = (string) ($completedSession['status'] ?? '');
$policy             = strtoupper((string) ($application['ai_interview_policy'] ?? 'REQUIRED_HARD'));
$flowJson           = json_encode($flow, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$candidateName      = (string) (session()->get('user_name') ?? 'Candidate');
$persona            = $flow['persona'] ?? [];
$generationSource   = strtolower((string) ($flow['generation_source'] ?? 'fallback'));
$introText          = str_replace('{candidate_name}', $candidateName, (string) ($flow['intro'] ?? 'Practice each section with a 60-second timer and record voice + video directly in the browser.'));
?>

<div class="ai-interview-flow-jobboard" data-ai-interview-flow="1" data-timer-seconds="<?= $timerSeconds ?>" data-total-timer-seconds="<?= $totalTimerSeconds ?>" data-application-id="<?= (int) ($application['id'] ?? 0) ?>" data-job-id="<?= (int) ($application['job_id'] ?? 0) ?>" data-resume-version-id="<?= (int) ($application['resume_version_id'] ?? 0) ?>" data-candidate-name="<?= esc($candidateName) ?>" data-interview-base-url="<?= esc(base_url('interview')) ?>">
    <div class="container">
        <div class="page-board-header page-board-header-tight">
            <div class="page-board-copy">
                <span class="page-board-kicker"><i class="fas fa-video"></i> AI interview flow</span>
                <h1 class="page-board-title"><?= esc($flow['title'] ?? ($jobTitle . ' AI Interview Flow')) ?></h1>
                <p class="page-board-subtitle"><?= esc($introText) ?></p>
            </div>
            <div class="page-board-actions">
                <a href="<?= base_url('candidate/applications') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Applications
                </a>
            </div>
        </div>

        <div class="ai-interview-flow-context">
            <div class="ai-interview-flow-context-item">
                <span>Candidate</span>
                <strong><?= esc($candidateName) ?></strong>
            </div>
            <div class="ai-interview-flow-context-item">
                <span>Role</span>
                <strong><?= esc($jobTitle) ?></strong>
            </div>
            <?php if ($companyName !== ''): ?>
                <div class="ai-interview-flow-context-item">
                    <span>Company</span>
                    <strong><?= esc($companyName) ?></strong>
                </div>
            <?php endif; ?>
            <?php if ($resumeTitle !== ''): ?>
                <div class="ai-interview-flow-context-item">
                    <span>Resume</span>
                    <strong><?= esc($resumeTitle) ?></strong>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($interviewCompleted): ?>
        <!-- Completed State: no interactive UI, no start button -->
        <div class="dashboard-panel" style="margin-top: 2rem; overflow: hidden; border-left: 6px solid #28a745;">
            <div class="panel-header" style="background: linear-gradient(135deg, rgba(40,167,69,0.10), rgba(40,167,69,0.03));">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="width: 56px; height: 56px; border-radius: 16px; display:flex; align-items:center; justify-content:center; background: rgba(40,167,69,0.14); color:#1f8f3a; font-size: 2rem;">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <?php if (in_array($sessionStatus, ['finalized', 'candidate_notified'], true)): ?>
                            <h2 class="section-title mb-1">Interview Review Finalized</h2>
                            <p class="section-subtitle mb-0">Your interview for <strong><?= esc($jobTitle) ?></strong> has been reviewed and the final update is now available.</p>
                        <?php elseif ($sessionStatus === 'under_review'): ?>
                            <h2 class="section-title mb-1">Interview Under Review</h2>
                            <p class="section-subtitle mb-0">Your interview for <strong><?= esc($jobTitle) ?></strong> is now being reviewed by the recruiter.</p>
                        <?php else: ?>
                            <h2 class="section-title mb-1">Interview Submitted for Review</h2>
                            <p class="section-subtitle mb-0">Your interview for <strong><?= esc($jobTitle) ?></strong> has been successfully submitted to the recruiter.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="panel-body">
                <div class="row g-3">
                    <?php if (!empty($completedSession['completed_at'])): ?>
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 h-100 bg-white">
                            <div class="text-muted small text-uppercase">Submitted On</div>
                            <strong><?= date('M d, Y H:i', strtotime($completedSession['completed_at'])) ?></strong>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 h-100 bg-white">
                            <div class="text-muted small text-uppercase">Status</div>
                            <strong>
                                <?php if (in_array($sessionStatus, ['finalized', 'candidate_notified'], true)): ?>
                                    Final review complete
                                <?php elseif ($sessionStatus === 'under_review'): ?>
                                    Waiting for recruiter review
                                <?php else: ?>
                                    Submitted for recruiter review
                                <?php endif; ?>
                            </strong>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="border rounded p-3 h-100 bg-white">
                            <div class="text-muted small text-uppercase">Next Step</div>
                            <strong>
                                <?php if (in_array($sessionStatus, ['finalized', 'candidate_notified'], true)): ?>
                                    Open your applications page for the latest update
                                <?php else: ?>
                                    Check your applications page later
                                <?php endif; ?>
                            </strong>
                        </div>
                    </div>
                </div>

                <?php if (in_array($sessionStatus, ['finalized', 'candidate_notified'], true)): ?>
                    <div class="border rounded p-3 bg-light mt-2">
                        <div class="font-weight-bold mb-2">Final Review</div>
                        <p class="mb-0">Your interview review has been finalized. Please check your applications page for the latest recruiter update.</p>
                    </div>
                <?php else: ?>
                    <div class="border rounded p-3 bg-light mt-2">
                        <div class="font-weight-bold mb-2">What happens next</div>
                        <ul class="mb-0 pl-3">
                            <li>Your interview is locked and ready for recruiter review.</li>
                            <li>The recruiter can review and finalize the interview outcome internally.</li>
                            <li>You’ll see the final update after the review is finalized.</li>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="<?= base_url('candidate/applications') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Applications
                    </a>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- Active Interview UI -->
        <div class="ai-interview-flow-shell">
            <aside class="ai-interview-flow-rail">
                <?php if (!empty($persona)): ?>
                    <div class="ai-interview-flow-rail-card">
                        <div class="ai-interview-flow-rail-title">Your Interviewer</div>
                        <div class="ai-interview-flow-persona-name"><?= esc($persona['name'] ?? 'Interviewer') ?></div>
                        <div class="ai-interview-flow-persona-title"><?= esc($persona['title'] ?? 'Technical Interviewer') ?></div>
                        <p class="ai-interview-flow-summary">
                            <?= esc(str_replace('{candidate_name}', $candidateName, (string) ($persona['opening_message'] ?? 'I will keep this interview structured and professional.'))) ?>
                        </p>
                        <?php if (!empty($persona['tone']) || !empty($persona['style'])): ?>
                            <div class="ai-interview-flow-skill-chips">
                                <?php if (!empty($persona['tone'])): ?>
                                    <span class="ai-interview-flow-skill-chip"><?= esc($persona['tone']) ?></span>
                                <?php endif; ?>
                                <?php if (!empty($persona['style'])): ?>
                                    <span class="ai-interview-flow-skill-chip"><?= esc($persona['style']) ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="ai-interview-flow-rail-card">
                    <div class="ai-interview-flow-rail-title">Session Snapshot</div>
                    <div class="ai-interview-flow-rail-meta">
                        <div><strong>Status</strong><span><?= esc(ucwords(str_replace('_', ' ', $applicationStatus))) ?></span></div>
                        <div><strong>Policy</strong><span><?= esc($policy === 'OFF' ? 'Recruiter / HR' : 'AI screening') ?></span></div>
                        <div><strong>Questions</strong><span><?= esc($generationSource === 'ai' ? 'AI generated' : 'Fallback set') ?></span></div>
                        <div><strong>Timer</strong><span><?= (int) $timerSeconds ?> sec/question</span></div>
                    </div>
                </div>

                <div class="ai-interview-flow-rail-card">
                    <div class="ai-interview-flow-rail-title">Interview Sections</div>
                    <div class="ai-interview-flow-section-list" id="sectionList">
                        <?php foreach ($sections as $index => $section): ?>
                            <button type="button" class="ai-interview-flow-section-card<?= $index === 0 ? ' is-active' : '' ?>" data-section-index="<?= (int) $index ?>">
                                <div class="ai-interview-flow-section-heading">
                                    <span class="ai-interview-flow-section-step">Section <?= (int) ($index + 1) ?></span>
                                    <strong><?= esc($section['title'] ?? 'Section') ?></strong>
                                </div>
                                <span class="ai-interview-flow-section-subtitle"><?= esc($section['subtitle'] ?? '') ?></span>
                                <span class="ai-interview-flow-section-count"><?= count((array) ($section['questions'] ?? [])) ?> question<?= count((array) ($section['questions'] ?? [])) === 1 ? '' : 's' ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if (!empty($focusSkills)): ?>
                    <div class="ai-interview-flow-rail-card">
                        <div class="ai-interview-flow-rail-title">Focus Skills</div>
                        <div class="ai-interview-flow-skill-chips">
                            <?php foreach ($focusSkills as $skill): ?>
                                <span class="ai-interview-flow-skill-chip"><?= esc($skill) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($resumeSummary !== ''): ?>
                    <div class="ai-interview-flow-rail-card">
                        <div class="ai-interview-flow-rail-title">Resume Snapshot</div>
                        <p class="ai-interview-flow-summary"><?= esc($resumeSummary) ?></p>
                    </div>
                <?php endif; ?>
            </aside>

            <main class="ai-interview-flow-main">
                <section class="ai-interview-flow-stage-card dashboard-panel">
                        <div class="panel-header ai-interview-flow-stage-header">
                            <div>
                                <span class="ai-interview-flow-stage-label" id="sectionLabel">Interview ready</span>
                                <h2 class="section-title mb-1" id="sectionTitle">Start when you are ready</h2>
                                <p class="section-subtitle mb-0" id="sectionSubtitle">We will show one question at a time after the session begins.</p>
                            </div>
                        <div class="ai-interview-flow-timer" id="timerBadge">
                            <span class="ai-interview-flow-timer-label">Timer</span>
                            <strong id="timerValue"><?= $timerSeconds ?></strong>
                            <small>seconds</small>
                        </div>
                    </div>

                    <div class="panel-body">
                        <div id="sessionRestoreBanner" class="alert alert-info ai-interview-flow-session-banner" style="display:none;"></div>

                        <div class="ai-interview-flow-status-strip">
                            <span class="badge badge-light border" id="roundBadge">Round 1 · Written</span>
                            <span class="badge badge-primary" id="questionCountBadge">Waiting to start</span>
                            <span class="badge badge-light ai-interview-flow-recording-state" id="recordingState">Ready to begin</span>
                        </div>

                        <div class="ai-interview-flow-stage-progress">
                            <div class="d-flex align-items-center justify-content-between small mb-1">
                                <span>Overall progress</span>
                                <strong id="overallProgressText">0%</strong>
                            </div>
                            <div class="ai-interview-flow-progress-bar">
                                <span id="overallProgressFill" style="width: 0%;"></span>
                            </div>
                        </div>

                        <div class="ai-interview-flow-question-card">
                            <div class="ai-interview-flow-instructions" id="preStartInstructions">
                                <div class="ai-interview-flow-instructions-title">Interview Instructions</div>
                                <ul class="ai-interview-flow-instructions-list">
                                    <li>Answer one question at a time in a clear and natural way.</li>
                                    <li>Keep your camera and microphone on throughout the session.</li>
                                    <li>You have <?= $timerSeconds ?> seconds for each response, so focus on the key point first.</li>
                                </ul>
                            </div>
                            <div class="ai-interview-flow-question" id="questionText" style="display:none;">
                                Press Start Session to begin your AI interview.
                            </div>
                            <p class="ai-interview-flow-question-hint" id="questionHint" style="display:none;">
                                Make sure your camera and microphone are ready. The first question will appear only after the session starts.
                            </p>
                            <div id="round1AnswerPanel" style="display:none;" class="mt-3">
                                <div id="round1OptionList" class="mb-2"></div>
                                <input type="text" id="round1TextAnswer" class="form-control mb-2" placeholder="Type your answer">
                                <button type="button" id="saveRound1AnswerBtn" class="btn btn-primary btn-sm">Save & Continue</button>
                            </div>
                        </div>

                        <div class="ai-interview-flow-controls">
                            <button type="button" class="btn btn-primary" id="startSessionBtn">
                                <i class="fas fa-video mr-1"></i> Start Interview
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="nextQuestionBtn" disabled>
                                Save &amp; Next <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="finishSessionBtn" style="display:none;" disabled>
                                <i class="fas fa-flag-checkered mr-1"></i> End Interview
                            </button>
                        </div>
                        <div id="syncingIndicator" class="text-muted small mt-2" style="display:none;">
                            Final response is syncing...
                        </div>
                    </div>
                </section>

                <div class="ai-interview-flow-grid">
                    <section class="ai-interview-flow-media-card dashboard-panel">
                        <div class="panel-header">
                            <h3 class="section-title mb-1">Camera Preview</h3>
                            <p class="section-subtitle mb-0">Camera and microphone start only after you confirm the interview.</p>
                        </div>
                        <div class="panel-body">
                            <div class="ai-interview-flow-video-wrap">
                                <video id="cameraPreview" autoplay muted playsinline></video>
                                <div class="ai-interview-flow-video-placeholder" id="previewPlaceholder">
                                    <i class="fas fa-video"></i>
                                    <span>Camera preview will appear here</span>
                                </div>
                            </div>
                            <div class="ai-interview-flow-note">
                                <strong>Tip:</strong> speak naturally, keep your answers structured, and focus on the reason behind each decision.
                            </div>
                        </div>
                    </section>

                    <section class="ai-interview-flow-recording-card dashboard-panel">
                        <div class="panel-header">
                            <h3 class="section-title mb-1">Interview Progress</h3>
                            <p class="section-subtitle mb-0">Track captured responses and section completion without exposing answer playback during the live interview.</p>
                        </div>
                        <div class="panel-body">
                            <div class="ai-interview-flow-progress-summary">
                                <div class="ai-interview-flow-progress-stat">
                                    <span>Responses captured</span>
                                    <strong id="capturedResponsesCount">0 / <?= (int) (count($round1Questions) + array_sum(array_map(static fn($section) => count((array) ($section['questions'] ?? [])), $sections))) ?></strong>
                                </div>
                                <div class="ai-interview-flow-progress-stat">
                                    <span>Current step</span>
                                    <strong id="currentStepStatus">Waiting to start</strong>
                                </div>
                            </div>

                            <div class="ai-interview-flow-progress-list" id="recordingList">
                                <div class="ai-interview-flow-empty-progress" id="emptyRecordings">
                                    <i class="fas fa-chart-line"></i>
                                    <span>Start the session to capture responses and update your interview progress.</span>
                                </div>

                                <?php foreach ($sections as $index => $section): ?>
                                    <div class="ai-interview-flow-progress-item" data-progress-section="<?= (int) $index ?>">
                                        <div class="ai-interview-flow-progress-copy">
                                            <strong><?= esc($section['title'] ?? 'Section') ?></strong>
                                            <span id="sectionProgressText<?= (int) $index ?>">0 / <?= count((array) ($section['questions'] ?? [])) ?> completed</span>
                                        </div>
                                        <div class="ai-interview-flow-progress-meta">
                                            <div class="ai-interview-flow-progress-bar">
                                                <span id="sectionProgressFill<?= (int) $index ?>" style="width: 0%;"></span>
                                            </div>
                                            <small id="sectionProgressBadge<?= (int) $index ?>">Not started</small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </section>
                </div>
            </main>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
window.aiInterviewFlow = <?= $flowJson ?: 'null' ?>;
</script>
<script src="<?= base_url('jobboard/js/ai-interview-flow.js?v=' . @filemtime(FCPATH . 'jobboard/js/ai-interview-flow.js')) ?>"></script>

<?= view('Layouts/candidate_footer') ?>




