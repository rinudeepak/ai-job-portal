<?= view('Layouts/candidate_header', ['title' => 'AI Interview - Question']) ?>

<!-- Hero Area Start-->
<div class="slider-area ">
    <div class="single-slider section-overly slider-height2 d-flex align-items-center"
        data-background="<?= base_url('assets/img/hero/about.jpg') ?>">
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
                    Question <?= $current_question ?> of <?= $total_questions ?>
                </h2>
            </div>

            <div class="col-lg-12">
                <!-- Progress Bar -->
                <div class="progress mb-4" style="height: 25px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: <?= ($current_question / $total_questions) * 100 ?>%;" 
                         aria-valuenow="<?= ($current_question / $total_questions) * 100 ?>" 
                         aria-valuemin="0" aria-valuemax="100">
                        <?= round(($current_question / $total_questions) * 100) ?>% Complete
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <!-- Question Card -->
                <div class="card mb-4" style="border-left: 4px solid #fb246a;">
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="badge badge-primary">
    <?= esc($question['topic'] ?? 'General') ?>
</span>

<h4><?= esc($question['question']) ?></h4>

                            <span class="badge badge-warning" id="timer">
                                <i class="fas fa-clock"></i> 15:00
                            </span>
                        </div>
                        <h4><?= esc($question['question']) ?></h4>
                    </div>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form class="form-contact contact_form" method="post" 
                      action="<?= base_url('interview/submit_answer') ?>" 
                      id="answerForm" novalidate="novalidate">
                    <?= csrf_field() ?>
                    <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
                    <input type="hidden" name="session_id" value="<?= $session_id ?>">
                    <input type="hidden" name="time_taken" id="time_taken" value="0">

                    <?php if ($question['type'] === 'mcq'): ?>
                        <!-- MCQ Options -->
                        <div class="row">
                            <?php foreach ($question['options'] as $index => $option): ?>
                                <div class="col-12 mb-3">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="option<?= $index ?>" name="answer" 
                                               class="custom-control-input" value="<?= $index ?>" required>
                                        <label class="custom-control-label" for="option<?= $index ?>" 
                                               style="padding: 15px; border: 2px solid #e0e0e0; border-radius: 5px; 
                                                      display: block; cursor: pointer; transition: all 0.3s;">
                                            <?= esc($option) ?>
                                        </label>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <!-- Text Answer -->
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Your Answer *</label>
                                    <textarea class="form-control w-100" name="answer" id="answer" 
                                              cols="30" rows="9" placeholder="Type your answer here..." 
                                              required></textarea>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-robot"></i> AI will evaluate: Grammar, Relevance, Clarity, and Confidence
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="form-group mt-3">
                        <button type="submit" class="button button-contactForm boxed-btn mr-2">
                            <?= $current_question < $total_questions ? 'Next Question →' : 'Submit Interview' ?>
                        </button>
                        <button type="button" class="button button-contactForm boxed-btn" 
                                style="background: #6c757d;" onclick="saveDraft()">
                            <i class="fas fa-save"></i> Save Draft
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="card" style="background: #fff3cd; border: none; border-radius: 8px;">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-lightbulb"></i> Tips</h5>
                        <hr>
                        <ul style="font-size: 14px;">
                            <li>Read questions carefully</li>
                            <li>Be clear and concise</li>
                            <li>Use proper grammar</li>
                            <li>Stay relevant to the question</li>
                            <li>Show confidence in your answers</li>
                        </ul>

                        <div class="alert alert-warning mt-3">
                            <strong>Auto-submit warning:</strong><br>
                            Question will auto-submit when timer reaches 0:00
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .custom-control-label:hover {
        background: #f0f0f0 !important;
        border-color: #fb246a !important;
    }
    input[type="radio"]:checked + label {
        background: #fff3f3 !important;
        border-color: #fb246a !important;
        font-weight: 600;
    }
</style>

<script>
// Timer functionality
let timeLeft = 900; // 15 minutes in seconds
const timerElement = document.getElementById('timer');
const timeTakenInput = document.getElementById('time_taken');

const timerInterval = setInterval(() => {
    timeLeft--;
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    timerElement.innerHTML = `<i class="fas fa-clock"></i> ${minutes}:${seconds.toString().padStart(2, '0')}`;
    
    // Update time taken
    timeTakenInput.value = 900 - timeLeft;
    
    if (timeLeft <= 0) {
        clearInterval(timerInterval);
        document.getElementById('answerForm').submit();
    }
}, 1000);

// Save draft function
function saveDraft() {
    const formData = new FormData(document.getElementById('answerForm'));
    fetch('<?= base_url('interview/save_draft') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Draft saved successfully!');
        } else {
            alert('Error saving draft. Please try again.');
        }
    })
    .catch(error => {
        alert('Error saving draft. Please try again.');
    });
}

// Warn before leaving
window.addEventListener('beforeunload', function (e) {
    e.preventDefault();
    e.returnValue = '';
});
</script>

<?= view('layouts/candidate_footer') ?>
