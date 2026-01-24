<?= view('Layouts/candidate_header', ['title' => 'AI Interview']) ?>

<div class="slider-area">
    <div class="single-slider section-overly slider-height2 d-flex align-items-center"
        data-background="<?= base_url('assets/img/hero/about.jpg') ?>">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="hero-cap text-center">
                        <h2>AI Interview</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="contact-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="contact-title">Start Your Interview with AI</h2>
            </div>

            <div class="col-lg-8">
                <div class="alert alert-info">
                    <h5><i class="fas fa-robot"></i> Meet Sarah - Your AI Interviewer</h5>
                    <p>Sarah will conduct a natural, conversational interview with you. She will:</p>
                    <ul class="mb-0">
                        <li>Ask about your experience and projects</li>
                        <li>Dig deeper into your technical skills</li>
                        <li>Adapt questions based on your answers</li>
                        <li>Help you if you get stuck</li>
                        <li>Act like a real human interviewer</li>
                    </ul>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5>Your Profile Summary</h5>
                        <p><strong>Skills:</strong> 
                            <?php foreach ($skills as $skill): ?>
                                <span class="badge badge-primary"><?= esc($skill['skill_name']) ?></span>
                            <?php endforeach; ?>
                        </p>
                        <?php if (!empty($github_languages)): ?>
                            <p><strong>GitHub Languages:</strong>
                                <?php foreach ($github_languages as $lang): ?>
                                    <span class="badge badge-success"><?= esc($lang) ?></span>
                                <?php endforeach; ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <form class="form-contact contact_form" method="post" 
                      action="<?= base_url('interview/begin/'.$application['id']) ?>" novalidate="novalidate">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label>Position You're Applying For *</label>
                                <input class="form-control valid" type="text" 
                                       value="<?php echo $job_title ?>" disabled>
                                       <input type="hidden" name="position" value="<?= esc($job_title) ?>">

                                <small class="form-text text-muted">
                                    Sarah will tailor questions based on this role
                                </small>
                            </div>
                        </div>

                      
                    </div>

                    <div class="form-group mt-3">
                        <button type="submit" class="button button-contactForm boxed-btn">
                            <i class="fas fa-comments"></i> Start Interview with Sarah
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="card" style="background: #fff3cd; border: none;">
                    <div class="card-body">
                        <h5><i class="fas fa-lightbulb"></i> Interview Tips</h5>
                        <hr>
                        <ul style="font-size: 14px;">
                            <li>Be honest - Sarah can detect generic answers</li>
                            <li>Explain your thought process</li>
                            <li>Give specific examples from your work</li>
                            <li>It's okay to say "I don't know" - honesty is valued</li>
                            <li>Sarah will help if you're stuck</li>
                            <li>Take your time to think before answering</li>
                        </ul>
                    </div>
                </div>

                <div class="card mt-3" style="background: #d1ecf1; border: none;">
                    <div class="card-body">
                        <h6><i class="fas fa-info-circle"></i> What to Expect</h6>
                        <ul style="font-size: 13px; margin-bottom: 0;">
                            <li>Intro & background questions</li>
                            <li>Deep dive into your resume</li>
                            <li>Technical concept questions</li>
                            <li>Follow-up based on your answers</li>
                            <li>Natural conversation flow</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= view('layouts/candidate_footer') ?>
