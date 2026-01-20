<?= view('layouts/candidate_header', ['title' => 'AI Interview - Question']) ?>

<!-- Hero Area Start -->
<div class="slider-area">
    <div class="single-slider section-overly slider-height2 d-flex align-items-center"
         style="background-image:url('<?= base_url('assets/img/hero/about.jpg') ?>')">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="hero-cap text-center">
                        <h2>AI Technical Interview</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Hero Area End -->

<section class="contact-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="contact-title">
                    Question <?= esc($current_question) ?> of <?= esc($total_questions) ?>
                </h2>
            </div>

            <!-- Progress Bar -->
            <div class="col-lg-12">
                <?php
                    $progress = $total_questions > 0
                        ? round(($current_question / $total_questions) * 100)
                        : 0;
                ?>
                <div class="progress mb-4" style="height: 25px;">
                    <div class="progress-bar bg-success"
                         role="progressbar"
                         style="width: <?= $progress ?>%;"
                         aria-valuenow="<?= $progress ?>"
                         aria-valuemin="0"
                         aria-valuemax="100">
                        <?= $progress ?>% Complete
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <!-- Question Card -->
                <div class="card mb-4" style="border-left: 4px solid #fb246a;">
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge badge-primary"><?= esc($question['skill']) ?></span>
                            <span class="badge badge-info"><?= esc($question['difficulty']) ?></span>
                            <span class="badge badge-warning" id="timer">
                                <i class="fas fa-clock"></i> 15:00
                            </span>
                        </div>
                        <h4><?= esc($question['text']) ?></h4>
                    </div>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= esc(session()->getFlashdata('error')) ?>
                    </div>
                <?php endif; ?>

                <form method="post"
                      action="<?= site_url('interview/submit_answer') ?>"
                      id="answerForm">
                    <?= csrf_field() ?>

                    <input type="hidden" name="question_id" value="<?= esc($question['id']) ?>">
                    <input type="hidden" name="session_id" value="<?= esc($session_id) ?>">
                    <input type="hidden" name="time_taken" id="time_taken" value="0">

                    <?php if ($question['type'] === 'mcq'): ?>
                        <?php foreach ($question['options'] as $index => $option): ?>
                            <div class="custom-control custom-radio mb-3">
                                <input type="radio"
                                       id="option<?= $index ?>"
                                       name="answer"
                                       class="custom-control-input"
                                       value="<?= $index ?>"
                                       required>
                                <label class="custom-control-label" for="option<?= $index ?>">
                                    <?= esc($option) ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="form-group">
                            <label>Your Answer *</label>
                            <textarea class="form-control"
                                      name="answer"
                                      rows="8"
                                      required></textarea>
                            <small class="form-text text-muted">
                                AI evaluates grammar, relevance, clarity & confidence
                            </small>
                        </div>
                    <?php endif; ?>

                    <div class="form-group mt-3">
                        <button type="submit" class="button boxed-btn">
                            <?= $current_question < $total_questions ? 'Next Question →' : 'Submit Interview' ?>
                        </button>

                        <button type="button"
                                class="button boxed-btn"
                                style="background:#6c757d"
                                onclick="saveDraft()">
                            Save Draft
                        </button>
                    </div>
                </form>
            </div>

            <!-- Tips -->
            <div class="col-lg-4">
                <div class="card" style="background:#fff3cd">
                    <div class="card-body">
                        <h5><i class="fas fa-lightbulb"></i> Tips</h5>
                        <ul>
                            <li>Read carefully</li>
                            <li>Be concise</li>
                            <li>Use proper grammar</li>
                            <li>Stay relevant</li>
                        </ul>
                        <div class="alert alert-warning">
                            Auto-submit when timer reaches 0
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    window.INTERVIEW_TIME = 900;
    window.SAVE_DRAFT_URL = "<?= site_url('interview/save_draft') ?>";
</script>

<script src="<?= base_url('assets/js/interview.js') ?>"></script>


<?= view('layouts/candidate_footer') ?>
