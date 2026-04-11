<?= view('Layouts/candidate_header', ['title' => $title ?? 'Premium Services Plans - HireMatrix']) ?>

<?php
$selectedService = strtolower((string) ($selected_service ?? 'all'));
$serviceCards = [
    [
        'key' => 'career-transition',
        'title' => 'Career Transition AI',
        'icon' => 'fas fa-route',
        'accent' => 'primary',
        'summary' => 'Build a structured learning path from your current role to your target role.',
        'points' => [
            'Personalized roadmap',
            'Daily actionable tasks',
            'Skill gap analysis',
            'Course modules and exercises',
        ],
    ],
    [
        'key' => 'resume-studio',
        'title' => 'Resume Studio',
        'icon' => 'fas fa-file-alt',
        'accent' => 'success',
        'summary' => 'Create AI-assisted resume versions for roles, jobs, and career pivots.',
        'points' => [
            'ATS-friendly resumes',
            'Job-specific versions',
            'Career transition resumes',
            'Unlimited updates',
        ],
    ],
    [
        'key' => 'mentor',
        'title' => 'AI Career Mentor',
        'icon' => 'fas fa-robot',
        'accent' => 'info',
        'summary' => 'Chat with a career mentor for interview prep, strategy, and next-step guidance.',
        'points' => [
            'Unlimited mentor chats',
            'Interview preparation',
            'Resume review guidance',
            'Job search strategy',
        ],
    ],
];
?>

<div class="career-transition-jobboard">
    <section class="career-transition-content">
        <div class="container">
            <div class="page-board-header page-board-header-tight">
                <div class="page-board-copy">
                    <span class="page-board-kicker"><i class="fas fa-crown"></i> Premium Services</span>
                    <h1 class="page-board-title">One subscription unlocks all three AI services</h1>
                    <p class="page-board-subtitle">
                        Unlock Career Transition AI, Resume Studio, and AI Career Mentor from one shared plans page.
                    </p>
                </div>
            </div>

            <?php if ($selectedService !== 'all'): ?>
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    You came here from <strong><?= esc(ucwords(str_replace('-', ' ', $selectedService))) ?></strong>.
                    This plan page covers all premium services in one place.
                </div>
            <?php endif; ?>

            <?php if (!empty($current_subscription)): ?>
                <div class="alert alert-success border-0 shadow-sm mb-4">
                    Your subscription is active. You can open the premium services directly from the Services menu.
                </div>
            <?php endif; ?>

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

            <div class="row mb-5">
                <?php foreach ($serviceCards as $card): ?>
                    <?php $isSelected = $selectedService === $card['key']; ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="dashboard-panel h-100 <?= $isSelected ? 'border border-primary' : '' ?>">
                            <?php if ($isSelected): ?>
                                <div class="text-center py-2 bg-primary text-white" style="border-radius: 4px 4px 0 0;">
                                    <small><i class="fas fa-star"></i> Selected service</small>
                                </div>
                            <?php endif; ?>
                            <div class="panel-body text-center p-4">
                                <div class="mb-3">
                                    <i class="<?= esc($card['icon']) ?> fa-3x text-<?= esc($card['accent']) ?>"></i>
                                </div>
                                <h4 class="mb-3"><?= esc($card['title']) ?></h4>
                                <p class="text-muted small mb-3"><?= esc($card['summary']) ?></p>

                                <ul class="list-unstyled text-left mb-0">
                                    <?php foreach ($card['points'] as $point): ?>
                                        <li class="mb-2 small">
                                            <i class="fas fa-check text-success mr-2"></i><?= esc($point) ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="row mb-5">
                <div class="col-12 text-center mb-4">
                    <h3>Choose a plan to unlock every premium service</h3>
                    <p class="text-muted mb-0">Your subscription works across Career Transition AI, Resume Studio, and AI Career Mentor.</p>
                </div>

                <?php foreach ($plans as $plan): ?>
                    <?php
                    $planFeatures = json_decode((string) ($plan['features'] ?? '[]'), true);
                    if (!is_array($planFeatures)) {
                        $planFeatures = [];
                    }
                    $isPopular = strcasecmp((string) ($plan['name'] ?? ''), 'Pro Monthly') === 0;
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="dashboard-panel h-100 <?= $isPopular ? 'border border-primary' : '' ?>">
                            <?php if ($isPopular): ?>
                                <div class="text-center py-2 bg-primary text-white" style="border-radius: 4px 4px 0 0;">
                                    <small><i class="fas fa-star"></i> Most Popular</small>
                                </div>
                            <?php endif; ?>
                            <div class="panel-body text-center p-4">
                                <h4 class="mb-3"><?= esc($plan['name']) ?></h4>
                                <div class="mb-3">
                                    <?php if ((float) $plan['price'] <= 0): ?>
                                        <span class="h2 text-success font-weight-bold">Free</span>
                                    <?php else: ?>
                                        <span class="h2 text-primary font-weight-bold">₹<?= number_format((float) $plan['price']) ?></span>
                                        <small class="text-muted d-block">
                                            /<?= (int) $plan['duration_days'] === 30 ? 'month' : ((int) $plan['duration_days'] === 90 ? 'quarter' : 'year') ?>
                                        </small>
                                    <?php endif; ?>
                                </div>

                                <p class="text-muted small mb-3"><?= esc((string) ($plan['description'] ?? '')) ?></p>

                                <ul class="list-unstyled text-left mb-4">
                                    <?php foreach ($planFeatures as $feature): ?>
                                        <li class="mb-2 small">
                                            <i class="fas fa-check text-success mr-2"></i><?= esc((string) $feature) ?>
                                        </li>
                                    <?php endforeach; ?>
                                    <li class="mb-2 small">
                                        <?php if (!empty($plan['chat_limit'])): ?>
                                            <i class="fas fa-comment text-info mr-2"></i><?= esc((string) $plan['chat_limit']) ?> chats/day
                                        <?php else: ?>
                                            <i class="fas fa-infinity text-success mr-2"></i>Unlimited chats
                                        <?php endif; ?>
                                    </li>
                                    <?php if ((int) ($plan['mentor_sessions_included'] ?? 0) > 0): ?>
                                        <li class="mb-2 small">
                                            <i class="fas fa-user-tie text-warning mr-2"></i><?= (int) $plan['mentor_sessions_included'] ?> mentor session(s)
                                        </li>
                                    <?php endif; ?>
                                </ul>

                                <?php if ((float) $plan['price'] <= 0): ?>
                                    <a href="<?= base_url('candidate/dashboard') ?>" class="btn btn-outline-primary btn-block">Get Started Free</a>
                                <?php else: ?>
                                    <form method="post" action="<?= base_url('premium-mentor/subscribe') ?>">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="plan_id" value="<?= (int) $plan['id'] ?>">
                                        <button type="submit" class="btn btn-primary btn-block">Subscribe Now</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="row mb-5">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-lg table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Feature</th>
                                    <?php foreach ($plans as $plan): ?>
                                        <th><?= esc($plan['name']) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Career Transition AI</strong></td>
                                    <?php foreach ($plans as $plan): ?>
                                        <td><?= str_contains(strtolower((string) ($plan['features'] ?? '')), 'career_transition') || str_contains(strtolower((string) ($plan['features'] ?? '')), 'career transition') ? 'Yes' : 'No' ?></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <td><strong>Resume Studio</strong></td>
                                    <?php foreach ($plans as $plan): ?>
                                        <td><?= str_contains(strtolower((string) ($plan['features'] ?? '')), 'resume_ai') || str_contains(strtolower((string) ($plan['features'] ?? '')), 'resume') ? 'Yes' : 'No' ?></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <td><strong>AI Career Mentor</strong></td>
                                    <?php foreach ($plans as $plan): ?>
                                        <td><?= str_contains(strtolower((string) ($plan['features'] ?? '')), 'mentor') || str_contains(strtolower((string) ($plan['features'] ?? '')), 'career chat') ? 'Yes' : 'No' ?></td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr>
                                    <td>Price</td>
                                    <?php foreach ($plans as $plan): ?>
                                        <td><strong>₹<?= number_format((float) $plan['price']) ?>/<?= (int) $plan['duration_days'] === 30 ? 'month' : ((int) $plan['duration_days'] === 90 ? 'quarter' : 'year') ?></strong></td>
                                    <?php endforeach; ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="text-center mt-5">
                <a href="<?= base_url('premium-mentor') ?>" class="btn btn-lg btn-primary">
                    Open AI Career Mentor
                </a>
                <p class="mt-3 text-muted">
                    One subscription unlocks all three services. Cancel anytime.
                </p>
            </div>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
