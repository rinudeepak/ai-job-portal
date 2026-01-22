<?= view('layouts/candidate_header', ['title' => 'Interview Result']) ?>
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


        <div class="card">
            <div class="card-body text-center">
                <h3>AI Interview Result</h3>

                <p><strong>Technical Score:</strong> <?= $technical_score ?>%</p>
                <p><strong>Communication Score:</strong> <?= $communication_score ?>%</p>
                <p><strong>Overall Rating:</strong> <?= $overall_rating ?>%</p>

                <h4 class="mt-3">
                    <?= strtoupper($ai_decision) ?>
                </h4>

                <p><?= $ai_feedback['decision_reasoning'] ?></p>
            </div>
        </div>



    </div>
</section>
<?= view('layouts/candidate_footer') ?>