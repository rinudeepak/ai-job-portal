<?= view('layouts/candidate_header', ['title' => 'AI Interview']) ?>

<div class="slider-area">
    <div class="single-slider section-overly slider-height2 d-flex align-items-center"
         data-background="<?= base_url('assets/img/hero/about.jpg') ?>">
        <div class="container">
            <div class="row">
                <div class="col-xl-12 text-center">
                    <h2 class="text-white">AI Technical Interview</h2>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="contact-section">
    <div class="container">

        <!-- Status -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="alert alert-success">
                    <i class="fas fa-file-alt"></i>
                    Resume analyzed successfully
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-info">
                    <i class="fab fa-github"></i>
                    GitHub profile analyzed successfully
                </div>
            </div>
        </div>

        <div class="row">

            <!-- Detected Skills -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-brain"></i> Skills from Resume</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($resumeSkills as $skill): ?>
                            <span class="badge badge-primary m-1"><?= esc($skill) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- GitHub Languages -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fab fa-github"></i> GitHub Languages</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($githubLanguages as $lang): ?>
                            <span class="badge badge-dark m-1"><?= esc($lang) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div>

        <!-- Interview Rules -->
        <div class="row mt-4">
            <div class="col-lg-8 mx-auto">
                <div class="card border-warning">
                    <div class="card-body">
                        <h5><i class="fas fa-exclamation-triangle"></i> Interview Guidelines</h5>
                        <ul>
                            <li>Questions are generated automatically by AI</li>
                            <li>Technical and communication skills are evaluated</li>
                            <li>Each question has a time limit</li>
                            <li>Auto-submit occurs if time expires</li>
                            <li>Do not refresh or navigate away</li>
                        </ul>

                        <form method="post" action="<?= base_url('ai-interview/start') ?>">
                            <?= csrf_field() ?>
                            <button type="submit"
                                    class="button button-contactForm boxed-btn w-100 mt-3">
                                🚀 Start AI Interview
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?= view('layouts/candidate_footer') ?>
