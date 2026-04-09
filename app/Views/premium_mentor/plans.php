<?= view('Layouts/candidate_header', ['title' => 'AI Career Mentor - Plans']) ?>

<div class="career-transition-jobboard">
    <section class="career-transition-content">
        <div class="container">
            <div class="page-board-header page-board-header-tight">
                <div class="page-board-copy">
                    <span class="page-board-kicker"><i class="fas fa-robot"></i> Premium Service</span>
                    <h1 class="page-board-title">AI Career Mentor</h1>
                    <p class="page-board-subtitle">Your personal AI career coach — available 24/7 to help you achieve your dream job.</p>
                </div>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <!-- Plans -->
            <div class="row mb-5">
                <?php foreach ($plans as $plan): ?>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="dashboard-panel h-100 <?= $plan['name'] === 'Pro Monthly' ? 'border border-primary' : '' ?>">
                            <?php if ($plan['name'] === 'Pro Monthly'): ?>
                                <div class="text-center py-2 bg-primary text-white" style="border-radius: 4px 4px 0 0;">
                                    <small><i class="fas fa-star"></i> Most Popular</small>
                                </div>
                            <?php endif; ?>
                            <div class="panel-body text-center">
                                <h4 class="mb-3"><?= esc($plan['name']) ?></h4>

                                <div class="mb-3">
                                    <?php if ($plan['price'] == 0): ?>
                                        <span class="h2 text-success font-weight-bold">Free</span>
                                    <?php else: ?>
                                        <span class="h2 text-primary font-weight-bold">₹<?= number_format($plan['price']) ?></span>
                                        <small class="text-muted d-block">
                                            /<?= $plan['duration_days'] == 30 ? 'month' : ($plan['duration_days'] == 90 ? 'quarter' : 'year') ?>
                                        </small>
                                    <?php endif; ?>
                                </div>

                                <p class="text-muted small mb-3"><?= esc($plan['description']) ?></p>

                                <ul class="list-unstyled text-left mb-4">
                                    <?php foreach (json_decode($plan['features'], true) as $feature): ?>
                                        <li class="mb-2 small">
                                            <i class="fas fa-check text-success mr-2"></i><?= esc($feature) ?>
                                        </li>
                                    <?php endforeach; ?>
                                    <li class="mb-2 small">
                                        <?php if ($plan['chat_limit']): ?>
                                            <i class="fas fa-comment text-info mr-2"></i><?= $plan['chat_limit'] ?> chats/day
                                        <?php else: ?>
                                            <i class="fas fa-infinity text-success mr-2"></i>Unlimited chats
                                        <?php endif; ?>
                                    </li>
                                    <?php if ($plan['mentor_sessions_included'] > 0): ?>
                                        <li class="mb-2 small">
                                            <i class="fas fa-user-tie text-warning mr-2"></i><?= $plan['mentor_sessions_included'] ?> mentor session(s)
                                        </li>
                                    <?php endif; ?>
                                </ul>

                                <?php if ($plan['price'] == 0): ?>
                                    <a href="<?= base_url('premium-mentor') ?>" class="btn btn-outline-primary btn-block">Get Started Free</a>
                                <?php else: ?>
                                    <form method="post" action="<?= base_url('premium-mentor/subscribe') ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                                        <button type="submit" class="btn btn-primary btn-block">Subscribe Now</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Features Section -->
            <div class="row mb-5">
                <div class="col-12 text-center mb-4">
                    <h3>What You Get with Premium AI Career Mentor</h3>
                </div>
                <div class="col-md-4 mb-4 text-center">
                    <i class="fas fa-brain fa-3x text-primary mb-3"></i>
                    <h5>Advanced AI Analysis</h5>
                    <p class="text-muted small">Deep career analysis with personalized guidance tailored to your target role and background.</p>
                </div>
                <div class="col-md-4 mb-4 text-center">
                    <i class="fas fa-route fa-3x text-success mb-3"></i>
                    <h5>Personalized Roadmap</h5>
                    <p class="text-muted small">Step-by-step career roadmap with specific milestones, timelines, and learning resources.</p>
                </div>
                <div class="col-md-4 mb-4 text-center">
                    <i class="fas fa-chart-line fa-3x text-warning mb-3"></i>
                    <h5>Progress Tracking</h5>
                    <p class="text-muted small">Monitor your career progress with detailed analytics and continuous improvement recommendations.</p>
                </div>
            </div>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
