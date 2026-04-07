<?= view('Layouts/recruiter_header', ['title' => 'AI Interview Report']) ?>

<?php
$application = $application ?? [];
$session = $session ?? [];
$answers = $answers ?? [];
$round1Attempts = $round1Attempts ?? [];
$sectionScores = $sectionScores ?? [];
$strengths = $strengths ?? [];
$concerns = $concerns ?? [];
$integrityEvents = $integrityEvents ?? [];
$integrityFlags = $integrityFlags ?? [];
$integritySummary = $integritySummary ?? [];
$sessionStatus = (string) ($session['status'] ?? '');
$hasIntegrityFlags = !empty($integrityFlags) || ((int) ($integritySummary['warning_count'] ?? 0) > 0) || ((int) ($integritySummary['tab_switch_count'] ?? 0) > 0) || ((int) ($integritySummary['hidden_duration_seconds'] ?? 0) > 0);
?>

<div class="recruiter-ai-report-jobboard">
    <div class="container-fluid py-5">
        <div class="page-board-header page-board-header-tight recruiter-page-board-header">
            <div class="page-board-copy">
                <span class="page-board-kicker"><i class="fas fa-robot"></i> AI interview report</span>
                <h1 class="page-board-title"><?= esc($application['candidate_name'] ?? 'Candidate') ?></h1>
                <p class="page-board-subtitle">
                    <?= esc($application['job_title'] ?? 'Role') ?> at <?= esc($application['company'] ?? 'Company') ?>
                </p>
            </div>
            <div class="page-board-actions">
                <a href="<?= base_url('recruiter/jobs/' . (int) ($application['job_id'] ?? 0) . '/applications') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Applications
                </a>
            </div>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger recruiter-alert"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <div class="alert alert-info recruiter-alert">
            <strong>AI review status:</strong>
            <?php if ($sessionStatus === 'submitted'): ?>
                Submitted and waiting for recruiter review.
            <?php elseif ($sessionStatus === 'under_review'): ?>
                Under review by the recruiter.
            <?php elseif ($sessionStatus === 'finalized'): ?>
                Finalized internally and ready for candidate notification.
            <?php elseif ($sessionStatus === 'candidate_notified'): ?>
                Candidate has been notified of the final update.
            <?php else: ?>
                <?= esc(ucwords(str_replace('_', ' ', $sessionStatus ?: 'active'))) ?>
            <?php endif; ?>
        </div>

        <div class="card shadow-sm mb-4 <?= $hasIntegrityFlags ? 'border-warning' : 'border-light' ?>">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3 mb-3">
                    <div>
                        <h5 class="mb-1">Session Integrity</h5>
                        <p class="text-muted mb-0">Quick check for tab switching, reconnects, and recording risk indicators.</p>
                    </div>
                    <span class="badge badge-<?= $hasIntegrityFlags ? 'warning' : 'success' ?> px-3 py-2">
                        <?= $hasIntegrityFlags ? 'Review recommended' : 'No integrity flags' ?>
                    </span>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3 h-100 bg-light">
                            <small class="text-muted d-block text-uppercase">Warnings</small>
                            <strong><?= (int) ($integritySummary['warning_count'] ?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3 h-100 bg-light">
                            <small class="text-muted d-block text-uppercase">Tab Switches</small>
                            <strong><?= (int) ($integritySummary['tab_switch_count'] ?? 0) ?></strong>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3 h-100 bg-light">
                            <small class="text-muted d-block text-uppercase">Hidden Time</small>
                            <strong><?= (int) ($integritySummary['hidden_duration_seconds'] ?? 0) ?>s</strong>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="border rounded p-3 h-100 bg-light">
                            <small class="text-muted d-block text-uppercase">Reconnects</small>
                            <strong><?= (int) ($integritySummary['reconnect_count'] ?? 0) ?></strong>
                        </div>
                    </div>
                </div>

                <?php if (!empty($integrityFlags)): ?>
                    <div class="mt-2">
                        <small class="text-muted d-block text-uppercase mb-2">Flags</small>
                        <div class="d-flex flex-wrap" style="gap:6px;">
                            <?php foreach ($integrityFlags as $flag): ?>
                                <span class="badge badge-warning border"><?= esc(ucwords(str_replace(['_', '-'], ' ', (string) $flag))) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($integritySummary['last_event_type'])): ?>
                    <div class="mt-3 small text-muted">
                        Last event: <strong><?= esc(ucwords(str_replace(['_', '-'], ' ', (string) $integritySummary['last_event_type']))) ?></strong>
                        <?php if (!empty($integritySummary['last_event_at'])): ?>
                            on <?= esc((string) $integritySummary['last_event_at']) ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Round Scores</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 h-100 bg-light">
                                    <small class="text-muted d-block text-uppercase">Round 1 (Written)</small>
                                    <strong><?= esc((string) ($session['round1_score'] ?? 0)) ?>%</strong>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 h-100 bg-light">
                                    <small class="text-muted d-block text-uppercase">Round 2 (Verbal)</small>
                                    <?php if (($session['round2_score'] ?? null) !== null): ?>
                                        <strong><?= esc((string) $session['round2_score']) ?></strong>
                                    <?php else: ?>
                                        <strong class="text-muted">AI score unavailable</strong>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 h-100 bg-light">
                                    <small class="text-muted d-block text-uppercase">Overall Rating</small>
                                    <?php if (($session['overall_rating'] ?? null) !== null): ?>
                                        <strong><?= esc((string) $session['overall_rating']) ?>/10</strong>
                                    <?php else: ?>
                                        <strong class="text-muted">AI score unavailable</strong>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <h5 class="mb-3">Round 1 Answer Review</h5>
                        <?php if (!empty($round1Attempts)): ?>
                            <?php foreach ($round1Attempts as $attempt): ?>
                                <div class="border rounded p-3 mb-3">
                                    <div class="small text-muted text-uppercase mb-1">
                                        <?= esc((string) ($attempt['section_key'] ?? 'section')) ?> · <?= esc((string) ($attempt['question_type'] ?? 'question')) ?>
                                    </div>
                                    <h6 class="mb-2"><?= esc((string) ($attempt['question_text'] ?? 'Question')) ?></h6>
                                    <div class="mb-1"><strong>Candidate Answer:</strong> <?= esc((string) ($attempt['selected_answer'] ?? '-')) ?></div>
                                    <div class="mb-1"><strong>Expected:</strong> <?= esc((string) ($attempt['correct_answer'] ?? '-')) ?></div>
                                    <?php
                                        $round1Flags = [];
                                        if (!empty($attempt['integrity_flags'])) {
                                            $decodedFlags = json_decode((string) $attempt['integrity_flags'], true);
                                            if (is_array($decodedFlags)) {
                                                $round1Flags = array_values(array_unique(array_filter(array_map('strval', $decodedFlags))));
                                            }
                                        }
                                        $pasteDetected = !empty($attempt['copy_paste_detected']) || in_array('text_paste_detected', $round1Flags, true);
                                        $largeInsertDetected = !empty($attempt['large_insert_detected']) || in_array('suspicious_text_insert', $round1Flags, true);
                                    ?>
                                    <div class="mb-1">
                                        <strong>Integrity:</strong>
                                        <?php if ($pasteDetected): ?>
                                            <span class="badge badge-warning ml-1">Paste detected</span>
                                        <?php else: ?>
                                            <span class="badge badge-success ml-1">No paste detected</span>
                                        <?php endif; ?>
                                        <?php if ($largeInsertDetected): ?>
                                            <span class="badge badge-warning ml-1">Large insert suspected</span>
                                        <?php endif; ?>
                                        <?php if (!empty($attempt['paste_event_count']) || !empty($attempt['pasted_character_count'])): ?>
                                            <span class="badge badge-light border ml-1"><?= esc((string) ($attempt['paste_event_count'] ?? 0)) ?> pastes</span>
                                            <span class="badge badge-light border ml-1"><?= esc((string) ($attempt['pasted_character_count'] ?? 0)) ?> chars</span>
                                        <?php endif; ?>
                                        <?php if (!empty($attempt['large_insert_count']) || !empty($attempt['large_insert_character_count'])): ?>
                                            <span class="badge badge-light border ml-1"><?= esc((string) ($attempt['large_insert_count'] ?? 0)) ?> inserts</span>
                                            <span class="badge badge-light border ml-1"><?= esc((string) ($attempt['large_insert_character_count'] ?? 0)) ?> chars</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="small text-muted">Score: <?= esc((string) ($attempt['score'] ?? 0)) ?> / <?= esc((string) ($attempt['max_score'] ?? 10)) ?></div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Round 1 written answers not available yet.</p>
                        <?php endif; ?>

                        <hr class="my-4">

                        <h5 class="mb-3">AI Summary</h5>
                        <p class="mb-0">
                            <?= esc($session['recommendation_summary'] ?? 'Evaluation summary will appear here once AI scoring is connected.') ?>
                        </p>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Round 2 Section Scores</h5>
                        <?php if (!empty($sectionScores)): ?>
                            <div class="row">
                                <?php foreach ($sectionScores as $label => $value): ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="border rounded p-3 h-100 bg-light">
                                            <small class="text-muted d-block text-uppercase"><?= esc(ucwords(str_replace('_', ' ', (string) $label))) ?></small>
                                            <strong><?= esc((string) $value) ?></strong>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted mb-0">Section-level AI scores are not available yet.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-3">Answer Breakdown</h5>
                        <?php if (!empty($answers)): ?>
                            <?php foreach ($answers as $answer): ?>
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                        <div>
                                            <div class="small text-muted text-uppercase">
                                                <?= esc((string) ($answer['section_key'] ?? 'section')) ?> · Question <?= (int) (($answer['question_index'] ?? 0) + 1) ?>
                                            </div>
                                            <h6 class="mb-2"><?= esc($answer['question_text'] ?? 'Question') ?></h6>
                                        </div>
                                        <span class="badge badge-light border">
                                            <?php if (($answer['ai_score'] ?? null) !== null): ?>
                                                AI Score: <?= esc((string) $answer['ai_score']) ?>
                                            <?php else: ?>
                                                AI score unavailable
                                            <?php endif; ?>
                                        </span>
                                    </div>

                                    <p class="mb-2"><?= esc($answer['transcript'] ?? 'Transcript not available yet.') ?></p>

                                    <div class="small text-muted">
                                        Duration: <?= esc((string) ($answer['duration_seconds'] ?? 0)) ?>s
                                    </div>

                                    <?php
                                    $answerFlags = json_decode((string) ($answer['integrity_flags'] ?? '[]'), true) ?: [];
                                    $answerHealth = trim((string) ($answer['recording_health'] ?? ''));
                                    $recordingMetrics = json_decode((string) ($answer['recording_metrics'] ?? '[]'), true);
                                    $recordingMetrics = is_array($recordingMetrics) ? $recordingMetrics : [];
                                    $noiseMetrics = isset($recordingMetrics['noise_monitoring']) && is_array($recordingMetrics['noise_monitoring'])
                                        ? $recordingMetrics['noise_monitoring']
                                        : [];
                                    $faceMetrics = isset($recordingMetrics['face_monitoring']) && is_array($recordingMetrics['face_monitoring'])
                                        ? $recordingMetrics['face_monitoring']
                                        : [];
                                    $noiseDetected = in_array('background_noise_detected', $answerFlags, true)
                                        || (($noiseMetrics['total_loud_seconds'] ?? 0) > 0)
                                        || (($noiseMetrics['longest_loud_streak_seconds'] ?? 0) > 0);
                                    $faceIssueDetected = in_array('face_not_detected', $answerFlags, true)
                                        || in_array('multiple_faces_detected', $answerFlags, true)
                                        || (($faceMetrics['total_no_face_seconds'] ?? 0) > 0)
                                        || (($faceMetrics['multi_face_samples'] ?? 0) > 0);
                                    ?>
                                    <?php if ($answerHealth !== '' || !empty($answerFlags) || !empty($answer['client_context'])): ?>
                                        <div class="mt-2">
                                            <small class="text-muted d-block text-uppercase mb-1">Recording Integrity</small>
                                            <div class="d-flex flex-wrap align-items-center" style="gap:6px;">
                                                <?php if ($answerHealth !== ''): ?>
                                                    <span class="badge badge-<?= $answerHealth === 'ok' ? 'success' : 'warning' ?>">
                                                        <?= esc(ucwords(str_replace('_', ' ', $answerHealth))) ?>
                                                    </span>
                                                <?php endif; ?>
                                                <?php foreach ($answerFlags as $flag): ?>
                                                    <span class="badge badge-warning border"><?= esc(ucwords(str_replace(['_', '-'], ' ', (string) $flag))) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($noiseDetected): ?>
                                        <div class="mt-3">
                                            <small class="text-muted d-block text-uppercase mb-2">Noise Monitoring</small>
                                            <div class="d-flex flex-wrap align-items-center mb-2" style="gap:6px;">
                                                <span class="badge badge-warning border">Background noise detected</span>
                                                <?php if (array_key_exists('threshold_db', $noiseMetrics) && $noiseMetrics['threshold_db'] !== null): ?>
                                                    <span class="badge badge-light border">Threshold <?= esc((string) $noiseMetrics['threshold_db']) ?> dB</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3 col-6 mb-2">
                                                    <div class="border rounded p-2 h-100 bg-light">
                                                        <small class="text-muted d-block text-uppercase">Avg dB</small>
                                                        <strong><?= esc((string) ($noiseMetrics['average_db'] ?? 'N/A')) ?></strong>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6 mb-2">
                                                    <div class="border rounded p-2 h-100 bg-light">
                                                        <small class="text-muted d-block text-uppercase">Peak dB</small>
                                                        <strong><?= esc((string) ($noiseMetrics['peak_db'] ?? 'N/A')) ?></strong>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6 mb-2">
                                                    <div class="border rounded p-2 h-100 bg-light">
                                                        <small class="text-muted d-block text-uppercase">Loud Time</small>
                                                        <strong><?= esc((string) ($noiseMetrics['total_loud_seconds'] ?? 0)) ?>s</strong>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6 mb-2">
                                                    <div class="border rounded p-2 h-100 bg-light">
                                                        <small class="text-muted d-block text-uppercase">Longest Streak</small>
                                                        <strong><?= esc((string) ($noiseMetrics['longest_loud_streak_seconds'] ?? 0)) ?>s</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($faceIssueDetected || !empty($faceMetrics)): ?>
                                        <div class="mt-3">
                                            <small class="text-muted d-block text-uppercase mb-2">Face Presence</small>
                                            <div class="d-flex flex-wrap align-items-center mb-2" style="gap:6px;">
                                                <?php if (($faceMetrics['supported'] ?? false)): ?>
                                                    <span class="badge badge-light border">Browser face detection supported</span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary border">Face detection not supported</span>
                                                <?php endif; ?>
                                                <?php if (in_array('face_not_detected', $answerFlags, true)): ?>
                                                    <span class="badge badge-warning border">Face not detected</span>
                                                <?php endif; ?>
                                                <?php if (in_array('multiple_faces_detected', $answerFlags, true)): ?>
                                                    <span class="badge badge-warning border">Multiple faces detected</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-3 col-6 mb-2">
                                                    <div class="border rounded p-2 h-100 bg-light">
                                                        <small class="text-muted d-block text-uppercase">No Face</small>
                                                        <strong><?= esc((string) ($faceMetrics['total_no_face_seconds'] ?? 0)) ?>s</strong>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6 mb-2">
                                                    <div class="border rounded p-2 h-100 bg-light">
                                                        <small class="text-muted d-block text-uppercase">Longest Gap</small>
                                                        <strong><?= esc((string) ($faceMetrics['longest_no_face_streak_seconds'] ?? 0)) ?>s</strong>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6 mb-2">
                                                    <div class="border rounded p-2 h-100 bg-light">
                                                        <small class="text-muted d-block text-uppercase">Max Faces</small>
                                                        <strong><?= esc((string) ($faceMetrics['max_faces_detected'] ?? 0)) ?></strong>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 col-6 mb-2">
                                                    <div class="border rounded p-2 h-100 bg-light">
                                                        <small class="text-muted d-block text-uppercase">Samples</small>
                                                        <strong><?= esc((string) ($faceMetrics['sample_count'] ?? 0)) ?></strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-4 col-6 mb-2">
                                                    <div class="border rounded p-2 h-100 bg-light">
                                                        <small class="text-muted d-block text-uppercase">Face Present</small>
                                                        <strong><?= esc((string) ($faceMetrics['face_present_samples'] ?? 0)) ?></strong>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-6 mb-2">
                                                    <div class="border rounded p-2 h-100 bg-light">
                                                        <small class="text-muted d-block text-uppercase">No-Face Samples</small>
                                                        <strong><?= esc((string) ($faceMetrics['no_face_samples'] ?? 0)) ?></strong>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-6 mb-2">
                                                    <div class="border rounded p-2 h-100 bg-light">
                                                        <small class="text-muted d-block text-uppercase">Multi-Face Samples</small>
                                                        <strong><?= esc((string) ($faceMetrics['multi_face_samples'] ?? 0)) ?></strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <?php if (!empty($answer['ai_feedback'])): ?>
                                        <div class="mt-2 text-muted"><?= esc($answer['ai_feedback']) ?></div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0">No per-question answers have been saved yet.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Overall Evaluation</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">Overall Rating: <strong><?= ($session['overall_rating'] ?? null) !== null ? esc((string) $session['overall_rating']) : 'AI score unavailable' ?></strong></li>
                            <li class="mb-2">Technical: <strong><?= ($session['technical_score'] ?? null) !== null ? esc((string) $session['technical_score']) : 'AI score unavailable' ?></strong></li>
                            <li class="mb-2">Communication: <strong><?= ($session['communication_score'] ?? null) !== null ? esc((string) $session['communication_score']) : 'AI score unavailable' ?></strong></li>
                            <li class="mb-2">Problem Solving: <strong><?= ($session['problem_solving_score'] ?? null) !== null ? esc((string) $session['problem_solving_score']) : 'AI score unavailable' ?></strong></li>
                            <li class="mb-0">AI Decision: <strong><?= esc(ucwords(str_replace('_', ' ', (string) ($session['ai_decision'] ?? 'pending')))) ?></strong></li>
                        </ul>
                        <?php if ($hasIntegrityFlags): ?>
                            <div class="alert alert-warning mt-3 mb-0 py-2">
                                This session has integrity signals. Review the flags above before finalizing.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Strengths</h5>
                        <?php if (!empty($strengths)): ?>
                            <?php foreach ($strengths as $item): ?>
                                <div class="mb-2"><?= esc((string) $item) ?></div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Strengths will appear after evaluation.</p>
                        <?php endif; ?>

                        <h5 class="mt-4 mb-3">Concerns</h5>
                        <?php if (!empty($concerns)): ?>
                            <?php foreach ($concerns as $item): ?>
                                <div class="mb-2"><?= esc((string) $item) ?></div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0">Concerns will appear after evaluation.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="mb-3">Next Action</h5>
                        <form method="post" action="<?= base_url('recruiter/applications/shortlist/' . (int) ($application['id'] ?? 0)) ?>" class="mb-2">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-primary btn-block">Shortlist</button>
                        </form>
                        <form method="post" action="<?= base_url('recruiter/applications/reject/' . (int) ($application['id'] ?? 0)) ?>">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-danger btn-block">Reject</button>
                        </form>
                    </div>
                </div>

                <!-- Score Override & Flag -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="mb-1">Finalize Review</h5>
                        <p class="text-muted small mb-3">Save the recruiter override, finalize the review, and notify the candidate.</p>

                        <?php if (session()->getFlashdata('override_success')): ?>
                            <div class="alert alert-success py-2"><?= session()->getFlashdata('override_success') ?></div>
                        <?php endif; ?>

                        <?php if ($sessionStatus === 'candidate_notified'): ?>
                            <div class="alert alert-success mb-0">
                                The candidate has already been notified. This review is locked.
                            </div>
                        <?php else: ?>
                        <form method="post" action="<?= base_url('recruiter/applications/' . (int) ($application['id'] ?? 0) . '/ai-report/override') ?>">
                            <?= csrf_field() ?>

                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">Override Overall Score <span class="text-muted">(0–10)</span></label>
                                <input
                                    type="number"
                                    name="override_score"
                                    class="form-control"
                                    min="0" max="10" step="0.1"
                                    placeholder="Leave blank to keep AI score"
                                    value="<?= esc((string) ($session['recruiter_override_score'] ?? '')) ?>"
                                >
                            </div>

                            <div class="form-group mb-3">
                                <label class="small font-weight-bold d-block mb-2">Flag for Review</label>
                                <?php
                                $flagOptions = [
                                    ''             => ['label' => 'No Flag',      'color' => '#6c757d'],
                                    'strong_yes'   => ['label' => 'Strong Yes',   'color' => '#198754'],
                                    'yes'          => ['label' => 'Yes',          'color' => '#20c997'],
                                    'maybe'        => ['label' => 'Maybe',        'color' => '#ffc107'],
                                    'no'           => ['label' => 'No',           'color' => '#dc3545'],
                                    'needs_review' => ['label' => 'Needs Review', 'color' => '#6c757d'],
                                ];
                                $currentFlag = (string) ($session['recruiter_flag'] ?? '');
                                ?>
                                <div style="display:flex;flex-wrap:wrap;gap:6px;">
                                    <?php foreach ($flagOptions as $val => $opt): ?>
                                        <label style="margin:0;cursor:pointer;">
                                            <input type="radio" name="flag" value="<?= esc($val) ?>" <?= $currentFlag === $val ? 'checked' : '' ?> style="display:none;" class="flag-radio">
                                            <span class="flag-pill" style="display:inline-block;padding:4px 12px;border-radius:999px;font-size:0.78rem;font-weight:600;border:2px solid <?= $opt['color'] ?>;color:<?= $currentFlag === $val ? '#fff' : $opt['color'] ?>;background:<?= $currentFlag === $val ? $opt['color'] : 'transparent' ?>;transition:all .15s;">
                                                <?= esc($opt['label']) ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <script>
                                document.querySelectorAll('.flag-radio').forEach(function(radio) {
                                    radio.addEventListener('change', function() {
                                        document.querySelectorAll('.flag-radio').forEach(function(r) {
                                            var pill = r.nextElementSibling;
                                            var color = pill.style.borderColor;
                                            pill.style.background = r.checked ? color : 'transparent';
                                            pill.style.color = r.checked ? '#fff' : color;
                                        });
                                    });
                                });
                                </script>
                            </div>

                            <div class="form-group mb-3">
                                <label class="small font-weight-bold">Recruiter Note</label>
                                <textarea name="recruiter_note" class="form-control" rows="3" placeholder="Optional note about this interview..."><?= esc((string) ($session['recruiter_note'] ?? '')) ?></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Finalize and Notify Candidate</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('Layouts/recruiter_footer') ?>
