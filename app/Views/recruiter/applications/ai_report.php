<?= view('Layouts/recruiter_header', ['title' => 'AI Interview Report']) ?>

<?php
$application = $application ?? [];
$session = $session ?? [];
$answers = $answers ?? [];
$round1Attempts = $round1Attempts ?? [];
$sectionScores = $sectionScores ?? [];
$strengths = $strengths ?? [];
$concerns = $concerns ?? [];
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
                                    <strong><?= esc((string) ($session['round2_score'] ?? 0)) ?></strong>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="border rounded p-3 h-100 bg-light">
                                    <small class="text-muted d-block text-uppercase">Overall Rating</small>
                                    <strong><?= esc((string) ($session['overall_rating'] ?? 0)) ?>/10</strong>
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
                                        <span class="badge badge-light border">AI Score: <?= esc((string) ($answer['ai_score'] ?? '0')) ?></span>
                                    </div>

                                    <p class="mb-2"><?= esc($answer['transcript'] ?? 'Transcript not available yet.') ?></p>

                                    <div class="small text-muted">
                                        Duration: <?= esc((string) ($answer['duration_seconds'] ?? 0)) ?>s
                                    </div>

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
                            <li class="mb-2">Overall Rating: <strong><?= esc((string) ($session['overall_rating'] ?? 0)) ?></strong></li>
                            <li class="mb-2">Technical: <strong><?= esc((string) ($session['technical_score'] ?? 0)) ?></strong></li>
                            <li class="mb-2">Communication: <strong><?= esc((string) ($session['communication_score'] ?? 0)) ?></strong></li>
                            <li class="mb-2">Problem Solving: <strong><?= esc((string) ($session['problem_solving_score'] ?? 0)) ?></strong></li>
                            <li class="mb-0">AI Decision: <strong><?= esc(ucwords(str_replace('_', ' ', (string) ($session['ai_decision'] ?? 'pending')))) ?></strong></li>
                        </ul>
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
                        <h5 class="mb-1">Recruiter Override</h5>
                        <p class="text-muted small mb-3">Override the AI score or flag this interview for review.</p>

                        <?php if (session()->getFlashdata('override_success')): ?>
                            <div class="alert alert-success py-2"><?= session()->getFlashdata('override_success') ?></div>
                        <?php endif; ?>

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

                            <button type="submit" class="btn btn-primary btn-block">Save Override</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('Layouts/recruiter_footer') ?>
