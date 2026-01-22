<?= view('Layouts/candidate_header', ['title' => 'Interview Results']) ?>

<div class="slider-area">
    <div class="single-slider section-overly slider-height2 d-flex align-items-center"
        data-background="<?= base_url('assets/img/hero/about.jpg') ?>">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="hero-cap text-center">
                        <h2>Interview Results</h2>
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
                <h2 class="contact-title">AI Evaluation Report</h2>
            </div>

            <!-- Decision Alert -->
            <div class="col-lg-12">
                <?php if ($evaluation['ai_decision'] === 'qualified'): ?>
                    <div class="alert alert-success text-center">
                        <h3><i class="fas fa-check-circle"></i> Congratulations!</h3>
                        <p class="mb-0">
                            <strong>Recommendation: <?= esc($evaluation['recommendation']) ?></strong><br>
                            <?= esc($evaluation['decision_reasoning']) ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <h3><i class="fas fa-info-circle"></i> Interview Complete</h3>
                        <p class="mb-0">
                            <strong>Recommendation: <?= esc($evaluation['recommendation']) ?></strong><br>
                            <?= esc($evaluation['decision_reasoning']) ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Score Cards -->
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="card-body text-center">
                        <h1 class="display-4 font-weight-bold"><?= number_format($evaluation['technical_score'], 1) ?>%</h1>
                        <h5>Technical Knowledge</h5>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <div class="card-body text-center">
                        <h1 class="display-4 font-weight-bold"><?= number_format($evaluation['communication_score'], 1) ?>%</h1>
                        <h5>Communication</h5>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <div class="card-body text-center">
                        <h1 class="display-4 font-weight-bold"><?= number_format($evaluation['overall_rating'], 1) ?>%</h1>
                        <h5>Overall Rating</h5>
                    </div>
                </div>
            </div>

            <!-- Detailed Breakdown -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header" style="background: #667eea; color: white;">
                        <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Performance Breakdown</h5>
                    </div>
                    <div class="card-body">
                        <?php 
                        $scores = [
                            'Technical Knowledge' => $evaluation['technical_score'],
                            'Problem Solving' => $evaluation['problem_solving_score'],
                            'Communication' => $evaluation['communication_score'],
                            'Adaptability' => $evaluation['adaptability_score'],
                            'Enthusiasm' => $evaluation['enthusiasm_score']
                        ];
                        foreach ($scores as $label => $score): 
                        ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small><?= $label ?></small>
                                    <small class="font-weight-bold"><?= number_format($score, 1) ?>%</small>
                                </div>
                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar" style="width: <?= $score ?>%; 
                                         background: <?= $score >= 80 ? '#28a745' : ($score >= 60 ? '#ffc107' : '#dc3545') ?>;">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Key Highlights & Concerns -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-header" style="background: #28a745; color: white;">
                        <h5 class="mb-0"><i class="fas fa-star"></i> Key Highlights</h5>
                    </div>
                    <div class="card-body">
                        <ul>
                            <?php foreach ($evaluation['key_highlights'] as $highlight): ?>
                                <li><?= esc($highlight) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <?php if (!empty($evaluation['concerns'])): ?>
                    <div class="card mb-4">
                        <div class="card-header" style="background: #ffc107; color: #333;">
                            <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Areas of Concern</h5>
                        </div>
                        <div class="card-body">
                            <ul>
                                <?php foreach ($evaluation['concerns'] as $concern): ?>
                                    <li><?= esc($concern) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Feedback Sections -->
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-comments"></i> Detailed Feedback</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="text-primary">Technical Assessment</h6>
                        <p><?= esc($evaluation['technical_feedback']) ?></p>
                        
                        <hr>
                        
                        <h6 class="text-primary">Communication Assessment</h6>
                        <p><?= esc($evaluation['communication_feedback']) ?></p>
                    </div>
                </div>
            </div>

            <!-- Growth Areas -->
            <?php if (!empty($evaluation['recommendations'])): ?>
                <div class="col-lg-12">
                    <div class="card" style="background: #fff3cd; border: 1px solid #ffc107;">
                        <div class="card-body">
                            <h5 style="color: #856404;">
                                <i class="fas fa-lightbulb"></i> Recommended Growth Areas
                            </h5>
                            <ul style="color: #856404;">
                                <?php foreach ($evaluation['recommendations'] as $rec): ?>
                                    <li><?= esc($rec) ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if (!empty($evaluation['next_steps'])): ?>
                                <p class="mb-0"><strong>Next Steps:</strong> <?= esc($evaluation['next_steps']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Notable Moments -->
            <?php if (!empty($evaluation['notable_moments'])): ?>
                <div class="col-lg-12 mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-bookmark"></i> Notable Moments</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($evaluation['notable_moments'] as $moment): ?>
                                <div class="alert alert-light mb-2">
                                    <strong>Turn <?= $moment['turn'] ?>:</strong> 
                                    <?= esc($moment['what_happened']) ?>
                                    <br>
                                    <small class="text-muted">Impact: <?= esc($moment['impact']) ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="col-lg-12 text-center mt-4">
                <a href="<?= base_url('candidate/dashboard') ?>" class="button button-contactForm boxed-btn mr-2">
                    <i class="fas fa-home"></i> Back to Dashboard
                </a>
                <a href="<?= base_url('interview/transcript/' . $interview['id']) ?>" 
                   class="button button-contactForm boxed-btn" style="background: #6c757d;">
                    <i class="fas fa-file-alt"></i> View Full Transcript
                </a>
            </div>
        </div>
    </div>
</section>

<?= view('layouts/candidate_footer') ?>