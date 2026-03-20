<?= view('Layouts/candidate_header', ['title' => 'Dashboard']) ?>

<?php
$applicationCount = count($applications ?? []);
$recentApps = array_slice($applications ?? [], 0, 5);
$topSuggestedJobs = $topSuggestedJobs ?? [];
$avgScore = (int) round((float) ($stats['average_ai_score'] ?? 0));
$activeSuggestions = session()->get('career_suggestions') ?? [];
$activeSuggestions = array_filter($activeSuggestions, static function ($suggestion): bool {
    return isset($suggestion['expires_at']) && time() < (int) $suggestion['expires_at'];
});
$activeSuggestionsCount = count($activeSuggestions);
$topRecommendedCount = count($topSuggestedJobs);

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

            <div class="card mb-4" style="max-width: 800px;">
                <div class="card-body p-3 p-md-4">
                    <form action="<?= base_url('jobs') ?>" method="get">
                        <div class="row g-3 g-md-2">
                            <div class="col-12 col-md-6 col-lg-5">
                                <div class="search-input-group">
                                    <i class="fas fa-search" style="color: var(--muted-foreground);"></i>
                                    <input type="text" name="search" placeholder="Job title, skills, or company" class="form-control border-0">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="search-input-group">
                                    <i class="fas fa-map-pin" style="color: var(--muted-foreground);"></i>
                                    <input type="text" name="location" placeholder="City or location" class="form-control border-0">
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <button type="submit" class="btn btn-primary w-100">Search Jobs</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mb-4">
                <span class="text-muted me-3" style="font-size: 0.875rem; font-weight: 500;">Popular:</span>
                <div class="btn-group flex-wrap" role="group">
                    <a href="<?= base_url('jobs?search=developer') ?>" class="btn btn-outline-primary btn-sm" style="border-width: 2px;">Developer</a>
                    <a href="<?= base_url('jobs?search=designer') ?>" class="btn btn-sm" style="background: rgba(255, 123, 42, 0.2); color: var(--secondary); border: none;">Designer</a>
                    <a href="<?= base_url('jobs?search=marketing') ?>" class="btn btn-sm" style="background: rgba(0, 191, 165, 0.2); color: var(--accent); border: none;">Marketing</a>
                    <a href="<?= base_url('jobs?search=remote') ?>" class="btn btn-sm" style="background: rgba(59, 130, 246, 0.2); color: var(--primary); border: none;">Remote</a>
                    <a href="<?= base_url('jobs?employment_type=full-time') ?>" class="btn btn-sm" style="background: rgba(255, 123, 42, 0.2); color: var(--secondary); border: none;">Full-time</a>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-section">
        <div class="container">
            <div class="dashboard-metric-grid">
                <div class="dashboard-metric-card">
                    <div class="dashboard-metric-label">Applications</div>
                    <div class="dashboard-metric-value"><?= $applicationCount ?></div>
                    <div class="dashboard-metric-note">Across all active roles</div>
                </div>
                <div class="dashboard-metric-card">
                    <div class="dashboard-metric-label">AI Score</div>
                    <div class="dashboard-metric-value"><?= $avgScore ?>%</div>
                    <div class="dashboard-metric-note">Average recommendation fit</div>
                </div>
                <div class="dashboard-metric-card">
                    <div class="dashboard-metric-label">Live Matches</div>
                    <div class="dashboard-metric-value"><?= $topRecommendedCount ?></div>
                    <div class="dashboard-metric-note">Dynamic jobs for your profile</div>
                </div>
                <div class="dashboard-metric-card">
                    <div class="dashboard-metric-label">Active AI Paths</div>
                    <div class="dashboard-metric-value"><?= $activeSuggestionsCount ?></div>
                    <div class="dashboard-metric-note">Current career transition plans</div>
                </div>
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
                        ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="job-card dashboard-card">
                                <div class="job-card-icon"><i class="<?= esc($pickJobIcon($title)) ?>"></i></div>
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
                                <div class="progress-container">
                                    <div class="progress-bar-custom" style="width: <?= max(10, min(100, $score)) ?>%;"></div>
                                    <span class="progress-label"><?= max(10, min(100, $score)) ?>%</span>
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

    <section class="dashboard-section pt-0">
        <div class="container">
            <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
                <div>
                    <h2 class="section-title">Quick Actions</h2>
                    <p class="section-subtitle">Everything you need, one click away</p>
                </div>
            </div>

            <div class="dashboard-actions-grid">
                <a href="<?= base_url('jobs') ?>" class="quick-action-card">
                    <div class="quick-action-icon"><i class="fas fa-search"></i></div>
                    <div class="quick-action-title">Browse Jobs</div>
                    <div class="quick-action-text">Find matching roles.</div>
                </a>

                <a href="<?= base_url('candidate/my-bookings') ?>" class="quick-action-card">
                    <div class="quick-action-icon"><i class="fas fa-calendar"></i></div>
                    <div class="quick-action-title">My Interviews</div>
                    <div class="quick-action-text">Check your schedule.</div>
                </a>

                <a href="<?= base_url('candidate/profile') ?>" class="quick-action-card">
                    <div class="quick-action-icon"><i class="fas fa-user"></i></div>
                    <div class="quick-action-title">My Profile</div>
                    <div class="quick-action-text">Update profile data.</div>
                </a>

                <a href="<?= base_url('career-transition') ?>" class="quick-action-card">
                    <div class="quick-action-icon"><i class="fas fa-chart-line"></i></div>
                    <div class="quick-action-title">Career Transition AI</div>
                    <div class="quick-action-text">Build your path.</div>
                </a>
            </div>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
