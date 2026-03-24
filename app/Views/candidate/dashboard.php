<?= view('Layouts/candidate_header', ['title' => 'Dashboard']) ?>

<?php
$applicationCount = count($applications ?? []);
$recentApps = array_slice($applications ?? [], 0, 5);
$topSuggestedJobs = $topSuggestedJobs ?? [];
$avgScore = (int) round((float) ($stats['average_ai_score'] ?? 0));
$profileStrength = (int) ($profileStrength ?? 0);
$activeMatches = count($topSuggestedJobs);
$activeSuggestions = session()->get('career_suggestions') ?? [];
$activeSuggestions = array_filter($activeSuggestions, static function ($suggestion): bool {
    return isset($suggestion['expires_at']) && time() < (int) $suggestion['expires_at'];
});
$activeSuggestionsCount = count($activeSuggestions);
$topRecommendedCount = count($topSuggestedJobs);
$dashboardStrategy = is_array($jobSearchStrategy ?? null) ? $jobSearchStrategy : [];
$dashboardStrategySource = (string) ($dashboardStrategy['source'] ?? 'fallback');
$dashboardStrategyHeading = $dashboardStrategySource === 'ai' ? 'AI-generated strategy' : 'Job Search Strategy Coach';
$dashboardStrategyBadge = $dashboardStrategySource === 'ai' ? 'AI-generated' : 'Strategy preview';
$dashboardStrategyRoles = array_values(array_filter(array_map('trim', (array) ($dashboardStrategy['target_roles'] ?? []))));
if (empty($dashboardStrategyRoles)) {
    $dashboardStrategyRoles = array_slice(array_values(array_filter(array_map(static function (array $job): string {
        return trim((string) ($job['title'] ?? ''));
    }, $topSuggestedJobs))), 0, 3);
}
if (empty($dashboardStrategyRoles)) {
    $dashboardStrategyRoles = ['Web Developer', 'Software Developer', 'Frontend Developer'];
}

$pickJobIcon = static function (string $title): string {
    $needle = strtolower($title);
    if (str_contains($needle, 'data')) {
        return 'fas fa-database';
    }
    if (str_contains($needle, 'design')) {
        return 'fas fa-pencil-ruler';
    }
    if (str_contains($needle, 'manager') || str_contains($needle, 'product')) {
        return 'fas fa-chart-line';
    }
    if (str_contains($needle, 'engineer') || str_contains($needle, 'developer') || str_contains($needle, 'backend')) {
        return 'fas fa-code';
    }

    return 'fas fa-briefcase';
};

$formatDate = static function ($value, string $fallback = 'Recently'): string {
    if (empty($value)) {
        return $fallback;
    }

    $timestamp = strtotime((string) $value);
    return $timestamp ? date('M d, Y', $timestamp) : $fallback;
};

$resolveAssetUrl = static function (string $path): string {
    $path = trim($path);
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path) || str_starts_with($path, '//')) {
        return $path;
    }
    return base_url(ltrim($path, '/'));
};
?>

<div class="dashboard-jobboard">
    <section class="hero dashboard-hero">
        <div class="container">
            <div class="status-pill">
                <i class="fas fa-arrow-trend-up" style="color: var(--primary);"></i>
                <?= $applicationCount ?> Active Applications
            </div>

            <h1 class="hero-title">
                Welcome Back to Your
                <span class="gradient-text">Career Dashboard</span>
            </h1>

            <p class="hero-subtitle">
                Track your applications, interviews, and AI progress. Discover live opportunities that match your career goals.
            </p>

            <div class="dashboard-primary-action">
                <div class="dashboard-primary-action-copy">
                    <span class="dashboard-primary-action-kicker">Recommended next step</span>
                    <h2 class="dashboard-primary-action-title">Review your best job matches and apply first</h2>
                    <p class="dashboard-primary-action-text">Start with the strongest-fit roles, then use your strategy plan to refine the rest of your search.</p>
                </div>
                <div class="dashboard-primary-action-buttons">
                    <a href="<?= base_url('jobs?tab=suggested') ?>" class="btn btn-primary btn-lg dashboard-primary-btn">
                        Browse Suggested Jobs
                    </a>
                    <a href="<?= base_url('candidate/job-search-strategy') ?>" class="btn btn-outline-light btn-lg dashboard-secondary-btn">
                        Open Strategy Plan
                    </a>
                </div>
            </div>

        </div>
    </section>

    <section class="dashboard-section">
        <div class="container">
            <div class="dashboard-metric-grid">
                <a href="<?= base_url('candidate/profile') ?>" class="dashboard-metric-card dashboard-metric-link">
                    <div class="dashboard-metric-label">Profile strength</div>
                    <div class="dashboard-metric-value"><?= $profileStrength ?>%</div>
                    <div class="dashboard-metric-note">How complete your profile looks to recruiters</div>
                </a>
                <a href="<?= base_url('jobs?tab=suggested') ?>" class="dashboard-metric-card dashboard-metric-link">
                    <div class="dashboard-metric-label">Active matches</div>
                    <div class="dashboard-metric-value"><?= $activeMatches ?></div>
                    <div class="dashboard-metric-note">Recommended roles currently in view</div>
                </a>
                <a href="<?= base_url('candidate/my-bookings') ?>" class="dashboard-metric-card dashboard-metric-link">
                    <div class="dashboard-metric-label">Interviews booked</div>
                    <div class="dashboard-metric-value"><?= (int) ($stats['interviews_scheduled'] ?? 0) ?></div>
                    <div class="dashboard-metric-note">Confirmed interview slots on your calendar</div>
                </a>
                <a href="<?= base_url('candidate/applications') ?>" class="dashboard-metric-card dashboard-metric-link">
                    <div class="dashboard-metric-label">Applications in progress</div>
                    <div class="dashboard-metric-value"><?= (int) ($stats['active_applications'] ?? 0) ?></div>
                    <div class="dashboard-metric-note">Open applications still moving forward</div>
                </a>
            </div>
        </div>
    </section>

    <section class="dashboard-section pt-0">
        <div class="container">
            <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
                <div>
                    <div class="ai-badge">
                        <i class="fas fa-sparkles"></i>
                        Live Recommendations
                    </div>
                    <h2 class="section-title">Jobs Matching Your Profile</h2>
                    <p class="section-subtitle">Based on your skills, preferences, and application history</p>
                </div>
                <a href="<?= base_url('jobs?tab=suggested') ?>" class="btn btn-ghost text-primary">View all jobs <i class="fas fa-arrow-right ms-2"></i></a>
            </div>

            <div class="row g-4">
                <?php if (!empty($topSuggestedJobs)): ?>
                    <?php foreach (array_slice($topSuggestedJobs, 0, 6) as $job): ?>
                        <?php
                        $score = (int) round((float) ($job['match_score'] ?? 0));
                        $title = (string) ($job['title'] ?? 'Untitled Role');
                        $company = (string) ($job['company'] ?? 'Company');
                        $location = (string) ($job['location'] ?? 'N/A');
                        $postedAt = isset($job['posted_at']) ? $formatDate($job['posted_at']) : 'Recently';
                        $companyInitial = strtoupper(substr($company, 0, 1) ?: 'C');
                        $companyLogo = trim((string) ($job['company_logo'] ?? ''));
                        $matchPct = max(10, min(100, $score));
                        $matchLabel = $score > 0 ? $matchPct . '% match' : 'Open role';
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="job-card dashboard-card">
                                <div class="job-card-icon">
                                    <?php if ($companyLogo !== ''): ?>
                                        <img src="<?= esc($resolveAssetUrl($companyLogo)) ?>" alt="<?= esc($company) ?>">
                                    <?php else: ?>
                                        <span><?= esc($companyInitial) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="job-card-match-badge"><?= esc($matchLabel) ?></div>
                                <h3 class="job-card-title"><?= esc($title) ?></h3>
                                <p class="job-card-company"><?= esc($company) ?></p>
                                <div class="job-card-meta">
                                    <span><i class="fas fa-map-pin"></i> <?= esc($location) ?></span>
                                    <span><i class="fas fa-clock"></i> <?= esc($postedAt) ?></span>
                                </div>
                                <div class="job-card-tags">
                                    <span class="badge badge-primary">Full-time</span>
                                    <span class="badge badge-secondary"><?= esc(substr($title, 0, 15) ?: 'Role') ?></span>
                                </div>
                                <a href="<?= base_url('job/' . (int) $job['id']) ?>" class="view-details">View Details &rarr;</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="dashboard-panel">
                            <div class="panel-body text-center py-5">
                                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                <h4 class="mb-2">No recommended jobs yet</h4>
                                <p class="text-muted mb-0">Once your profile matches live openings, they will appear here automatically.</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section class="dashboard-section pt-0">
        <div class="container">
            <div class="dashboard-strategy-banner">
                <div class="dashboard-strategy-banner-inner">
                    <div class="dashboard-strategy-copy">
                        <div class="dashboard-strategy-kicker">
                            <i class="fas fa-compass"></i>
                            <?= esc($dashboardStrategyHeading) ?>
                        </div>
                        <h2 class="dashboard-strategy-title"><?= esc((string) ($dashboardStrategy['title'] ?? 'Job Search Strategy Coach')) ?></h2>
                        <p class="dashboard-strategy-text">
                            <?= esc((string) ($dashboardStrategy['summary'] ?? 'Use a focused plan to refine your resume, prioritize applications, and target roles that align with your strongest skills.')) ?>
                        </p>
                        <ul class="dashboard-strategy-list">
                            <?php foreach (array_slice((array) ($dashboardStrategy['priority_actions'] ?? []), 0, 3) as $item): ?>
                                <li><?= esc($item) ?></li>
                            <?php endforeach; ?>
                            <?php if (empty($dashboardStrategy['priority_actions'])): ?>
                                <li>Refine your resume around the skills that matter most.</li>
                                <li>Focus on applications with the highest match potential.</li>
                                <li>Set weekly priorities instead of applying broadly.</li>
                            <?php endif; ?>
                        </ul>
                        <a href="<?= base_url('candidate/job-search-strategy') ?>" class="btn btn-primary dashboard-strategy-btn">
                            Open Full Strategy <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                    <div class="dashboard-strategy-panel">
                        <span class="dashboard-strategy-badge"><?= esc($dashboardStrategyBadge) ?></span>
                        <div class="dashboard-strategy-panel-label">Target Roles</div>
                        <div class="dashboard-strategy-role-list">
                            <?php foreach ($dashboardStrategyRoles as $role): ?>
                                <span class="dashboard-strategy-role-pill"><?= esc($role) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-section pt-0">
        <div class="container">
            <div class="dashboard-cta-banner mb-4">
                <div class="dashboard-cta-banner-inner">
                    <div class="dashboard-cta-copy">
                        <div class="dashboard-cta-kicker">
                            <i class="fas fa-sparkles"></i>
                            Career Transition AI
                        </div>
                        <h2 class="dashboard-cta-title">Career Transition AI</h2>
                        <p class="dashboard-cta-text">
                            We analyze your current skill set and generate a focused roadmap for your target role. Start your career transition journey today!
                        </p>
                        <a href="<?= base_url('career-transition') ?>" class="btn btn-light dashboard-cta-btn">
                            Generate Roadmap <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                    <div class="dashboard-cta-art d-none d-lg-flex" aria-hidden="true">
                        <div class="dashboard-cta-orb">
                            <i class="fas fa-sparkles"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-section pt-0">
        <div class="container">
            <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="section-title">Recent Applications</h2>
                    <p class="section-subtitle">Track your application status and next steps</p>
                </div>
                <a href="<?= base_url('candidate/applications') ?>" class="btn btn-ghost text-primary">View all applications <i class="fas fa-arrow-right ms-2"></i></a>
            </div>

            <div class="dashboard-panel dashboard-table-wrap">
                <div class="panel-body">
                    <?php if (empty($recentApps)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h4 class="mb-2">No applications yet</h4>
                            <p class="text-muted mb-4">Start exploring opportunities and submit your first application.</p>
                            <a href="<?= base_url('jobs') ?>" class="btn btn-primary btn-lg">Browse Jobs</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Job Title</th>
                                        <th>Company</th>
                                        <th>Applied Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentApps as $application): ?>
                                        <tr>
                                            <td><strong><?= esc($application['job_title'] ?? '-') ?></strong></td>
                                            <td><?= esc($application['company_name'] ?? '-') ?></td>
                                            <td><?= !empty($application['applied_at']) ? $formatDate($application['applied_at']) : '-' ?></td>
                                            <td>
                                                <span class="badge badge-primary">
                                                    <?= esc(ucwords(str_replace('_', ' ', (string) ($application['status'] ?? 'applied')))) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('job/' . (int) ($application['job_id'] ?? 0)) ?>" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-eye me-1"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

</div>

<?= view('Layouts/candidate_footer') ?>
