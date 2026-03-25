<?= view('Layouts/candidate_header', ['title' => 'AI Interview Flow']) ?>
<?php
$application = $application ?? [];
$flow = $interviewFlow ?? [];
$sections = $flow['sections'] ?? [];
$jobTitle = (string) ($flow['job_title'] ?? ($application['job_title'] ?? 'AI Interview'));
$companyName = trim((string) ($flow['company_name'] ?? ($application['company'] ?? '')));
$resumeTitle = trim((string) ($flow['resume_title'] ?? ($application['resume_version_title'] ?? '')));
$resumeSummary = trim((string) ($flow['resume_summary'] ?? ($application['resume_version_summary'] ?? '')));
$focusSkills = $flow['focus_skills'] ?? [];
$timerSeconds = (int) ($flow['timer_seconds'] ?? 60);
$applicationStatus = (string) ($application['status'] ?? '');
$policy = strtoupper((string) ($application['ai_interview_policy'] ?? 'REQUIRED_HARD'));
$flowJson = json_encode($flow, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$candidateName = (string) (session()->get('user_name') ?? 'Candidate');
?>

<div class="ai-interview-flow-jobboard" data-ai-interview-flow="1" data-timer-seconds="<?= $timerSeconds ?>" data-application-id="<?= (int) ($application['id'] ?? 0) ?>">
    <div class="container">
        <div class="page-board-header page-board-header-tight">
            <div class="page-board-copy">
                <span class="page-board-kicker"><i class="fas fa-video"></i> AI interview flow</span>
                <h1 class="page-board-title"><?= esc($flow['title'] ?? ($jobTitle . ' AI Interview Flow')) ?></h1>
                <p class="page-board-subtitle"><?= esc($flow['intro'] ?? 'Practice each section with a 60-second timer and record voice + video directly in the browser.') ?></p>
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

        <div class="ai-interview-flow-shell">
            <aside class="ai-interview-flow-rail">
                <div class="ai-interview-flow-rail-card">
                    <div class="ai-interview-flow-rail-title">Session Snapshot</div>
                    <div class="ai-interview-flow-rail-meta">
                        <div><strong>Status</strong><span><?= esc(ucwords(str_replace('_', ' ', $applicationStatus))) ?></span></div>
                        <div><strong>Policy</strong><span><?= esc($policy === 'OFF' ? 'Recruiter / HR' : 'AI screening') ?></span></div>
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
                        <div class="ai-interview-flow-status-strip">
                            <span class="badge badge-primary" id="questionCountBadge">Waiting to start</span>
                            <span class="badge badge-light ai-interview-flow-recording-state" id="recordingState">Ready to begin</span>
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
                        </div>

                        <div class="ai-interview-flow-controls">
                            <button type="button" class="btn btn-primary" id="startSessionBtn">
                                <i class="fas fa-video mr-1"></i> Start Session
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="previousQuestionBtn" disabled>
                                <i class="fas fa-arrow-left mr-1"></i> Previous
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="nextQuestionBtn" disabled>
                                Next <i class="fas fa-arrow-right ml-1"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="finishSessionBtn" disabled>
                                <i class="fas fa-flag-checkered mr-1"></i> Finish Session
                            </button>
                        </div>
                    </div>
                </section>

                <div class="ai-interview-flow-grid">
                    <section class="ai-interview-flow-media-card dashboard-panel">
                        <div class="panel-header">
                            <h3 class="section-title mb-1">Camera Preview</h3>
                            <p class="section-subtitle mb-0">Your voice and video are recorded locally in the browser while each question runs.</p>
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
                                    <strong id="capturedResponsesCount">0 / <?= array_sum(array_map(static fn($section) => count((array) ($section['questions'] ?? [])), $sections)) ?></strong>
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
    </div>
</div>

<script>
window.aiInterviewFlow = <?= $flowJson ?: 'null' ?>;
</script>
<script src="<?= base_url('jobboard/js/ai-interview-flow.js?v=' . @filemtime(FCPATH . 'jobboard/js/ai-interview-flow.js')) ?>"></script>

<?= view('Layouts/candidate_footer') ?>
