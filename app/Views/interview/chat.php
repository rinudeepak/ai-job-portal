<?= view('Layouts/candidate_header', ['title' => 'AI Interview - Chat']) ?>

<div class="slider-area">
    <div class="single-slider section-overly slider-height2 d-flex align-items-center"
        data-background="<?= base_url('assets/img/hero/about.jpg') ?>">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="hero-cap text-center">
                        <h2>Interview with Sarah</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="contact-section" style="padding: 40px 0;">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2">
                <!-- Progress -->
                <div class="card mb-3">
                    <div class="card-body p-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <small><strong>Turn <?= $session_data['turn'] ?> of <?= $session_data['max_turns'] ?></strong></small>
                            <div class="progress" style="width: 60%; height: 20px;">
                                <div class="progress-bar bg-success" role="progressbar" 
                                     style="width: <?= ($session_data['turn'] / $session_data['max_turns']) * 100 ?>%;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat Container -->
                <!-- Chat Container -->
<div class="card" style="min-height: 500px; max-height: 600px; overflow-y: auto;" id="chatContainer">
    <div class="card-body" id="chatMessages">
        <?php 
        $history = $session_data['conversation_history'] ?? [];
        
        if (empty($history)):
        ?>
            <div class="alert alert-danger">
                <strong>Error:</strong> No conversation history found. 
                <a href="<?= base_url('interview/start') ?>">Start a new interview</a>
            </div>
        <?php else: ?>
            <?php foreach ($history as $msg):
                if ($msg['role'] === 'system') continue;
                $isAi = $msg['role'] === 'assistant';
            ?>
                <div class="mb-3 <?= $isAi ? 'text-left' : 'text-right' ?>">
                    <div class="d-inline-block" style="max-width: 80%;">
                        <div class="mb-1">
                            <?php if ($isAi): ?>
                                <span class="badge badge-primary">
                                    <i class="fas fa-robot"></i> Sarah
                                </span>
                            <?php else: ?>
                                <span class="badge badge-secondary">
                                    <i class="fas fa-user"></i> You
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="p-3 rounded" style="background: <?= $isAi ? '#e3f2fd' : '#f5f5f5' ?>; 
                             border-left: 3px solid <?= $isAi ? '#2196F3' : '#9e9e9e' ?>;">
                            <?= nl2br(esc($msg['content'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

                <!-- Answer Form -->
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger mt-3">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= base_url('interview/submit') ?>" 
                      class="mt-3" id="answerForm">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <textarea class="form-control" name="answer" id="answer" 
                                  rows="4" placeholder="Type your answer here..." 
                                  required autofocus></textarea>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle"></i> Take your time. Be specific and honest.
                        </small>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <button type="submit" class="button button-contactForm boxed-btn">
                            <i class="fas fa-paper-plane"></i> Send Answer
                        </button>
                        <span id="charCount" class="text-muted">0 characters</span>
                    </div>
                </form>

                <!-- Tips -->
                <div class="alert alert-info mt-3">
                    <small>
                        <i class="fas fa-lightbulb"></i> 
                        <strong>Tip:</strong> Explain your thinking process. Give real examples from your experience.
                    </small>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Auto-scroll to bottom
document.addEventListener('DOMContentLoaded', function() {
    const chatContainer = document.getElementById('chatContainer');
    chatContainer.scrollTop = chatContainer.scrollHeight;
    
    // Character counter
    const textarea = document.getElementById('answer');
    const charCount = document.getElementById('charCount');
    
    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length + ' characters';
    });
    
    // Prevent accidental page leave
    window.addEventListener('beforeunload', function (e) {
        e.preventDefault();
        e.returnValue = '';
    });
});
</script>

<?= view('layouts/candidate_footer') ?>
