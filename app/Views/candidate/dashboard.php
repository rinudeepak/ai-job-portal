<?= view('Layouts/candidate_header', ['title' => 'Dashboard']) ?>

<!-- CSS Circle Progress (Required for visual ATS Score) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/css-percentage-circle/0.0.3/css/circle.min.css">

<?php
$applicationCount = count($applications ?? []);
$recentApps = array_slice($applications ?? [], 0, 5);
$topSuggestedJobs = $topSuggestedJobs ?? [];
$avgScore = (int) round((float) ($stats['average_ai_score'] ?? 0));
$profileStrength = (int) ($profileStrength ?? 0);
$activeMatches = count($topSuggestedJobs);
$candidateId = (int) (session()->get('user_id') ?? 0);
$candidateName = (string) (session()->get('user_name') ?? 'Candidate');
$candidateInitial = strtoupper(substr(trim($candidateName), 0, 1) ?: 'C');
$activeSuggestions = session()->get('career_suggestions') ?? [];
$activeSuggestions = array_filter($activeSuggestions, static function ($suggestion): bool {
    return isset($suggestion['expires_at']) && time() < (int) $suggestion['expires_at'];
});
$activeSuggestionsCount = count($activeSuggestions);
$topRecommendedCount = count($topSuggestedJobs);
$savedJobsCount = $candidateId > 0
    ? (int) model('SavedJobModel')->where('candidate_id', $candidateId)->countAllResults()
    : 0;
$jobAlertsCount = $candidateId > 0
    ? (int) model('JobAlertModel')->where('candidate_id', $candidateId)->where('is_active', 1)->countAllResults()
    : 0;
$unreadNotificationCount = $candidateId > 0
    ? (int) model('NotificationModel')->getUnreadCount($candidateId)
    : 0;
$profilePrompt = $profileStrength >= 80
    ? 'Recruiter-ready profile. Keep momentum with fresh applications.'
    : ($profileStrength >= 50
        ? 'Complete a few more profile details to get sharper matches.'
        : 'Complete your profile to unlock stronger matches and recruiter visibility.');
$profilePromptCta = $profileStrength >= 80 ? 'View profile' : 'Complete profile';
$nextActionTitle = $topRecommendedCount > 0 ? 'Recommended jobs waiting' : 'Build your shortlist';
$nextActionText = $topRecommendedCount > 0
    ? 'Fresh matches are available based on your profile and recent activity.'
    : 'Save a few relevant jobs first so your dashboard becomes more targeted.';
$nextActionUrl = $topRecommendedCount > 0 ? base_url('jobs?tab=suggested') : base_url('jobs');
$nextActionCta = $topRecommendedCount > 0 ? 'View matches' : 'Browse jobs';
$formatCompactCount = static function (int $count): string {
    return $count > 99 ? '99+' : (string) $count;
};
$dashboardStrategy = is_array($jobSearchStrategy ?? null) ? $jobSearchStrategy : [];
$dashboardStrategySource = (string) ($dashboardStrategy['source'] ?? 'fallback');
$dashboardStrategyHeading = $dashboardStrategySource === 'ai' ? 'AI-generated strategy' : 'Job Search Strategy Coach';
$dashboardStrategyBadge = $dashboardStrategySource === 'ai' ? 'AI-generated' : 'Strategy preview';
$dashboardStrategyRoles = array_values(array_filter(array_map('trim', (array) ($dashboardStrategy['target_roles'] ?? []))));
$dailyReminder = is_array($dailyReminder ?? null) ? $dailyReminder : [];
$engagementBanners = is_array($engagementBanners ?? null) ? $engagementBanners : [];
$bannerItems = array_values(array_filter((array) ($engagementBanners['items'] ?? []), 'is_array'));
$bannerActiveIndex = (int) ($engagementBanners['active_index'] ?? 0);

$candidateProfile = model('CandidateProfileModel')->find($candidateId) ?? [];
$profileHeadline = trim((string) ($candidateProfile['headline'] ?? 'Candidate'));
$profileLocation = trim((string) ($candidateProfile['location'] ?? ''));
$candidatePhotoUrl = '';
$candidatePhoto = trim((string) ($candidateProfile['profile_photo'] ?? session()->get('profile_photo') ?? ''));
if ($candidatePhoto !== '') {
    $candidatePhotoUrl = preg_match('/^https?:\/\//i', $candidatePhoto) ? $candidatePhoto : base_url($candidatePhoto);
}

$request = service('request');
$currentPath = '/' . trim((string) parse_url(current_url(), PHP_URL_PATH), '/');
$activeTab = (string) ($request->getGet('tab') ?? '');
$sidebarActive = [
    'dashboard' => $currentPath === '/candidate' || $currentPath === '/candidate/dashboard',
    'applications' => $currentPath === '/candidate/applications',
    'suggested' => $currentPath === '/jobs' && $activeTab === 'suggested',
    'saved' => $currentPath === '/candidate/saved-jobs',
    'companies' => $currentPath === '/companies' || str_contains($currentPath, '/company/'),
    'profile' => $currentPath === '/candidate/profile',
    'career' => $currentPath === '/career-transition',
    'resume' => $currentPath === '/candidate/resume-studio',
];
$sidebarClass = static function (string $key) use ($sidebarActive): string {
    return 'dashboard-sidebar-link' . (isset($sidebarActive[$key]) && $sidebarActive[$key] ? ' active' : '');
};
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

    <div class="container dashboard-layout">
        <aside class="sidebar dashboard-sidebar">
            <div class="dashboard-sidebar-head">
                <div class="dashboard-sidebar-profile-pic-container">
                    <div class="dashboard-sidebar-profile-pic">
                        <?php if ($candidatePhotoUrl !== ''): ?>
                            <img src="<?= esc($candidatePhotoUrl) ?>" alt="<?= esc($candidateName) ?>">
                        <?php else: ?>
                            <span><?= esc($candidateInitial) ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="dashboard-sidebar-profile">
                    <div class="dashboard-sidebar-profile-meta">
                        <strong><?= esc($candidateName) ?></strong>
                        <p class="dashboard-sidebar-role"><?= esc($profileHeadline) ?></p>
                        <?php if ($profileLocation !== ''): ?>
                            <p class="dashboard-sidebar-location"><?= esc($profileLocation) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="<?= base_url('candidate/profile') ?>" class="btn btn-sm btn-primary dashboard-sidebar-view-profile">View profile</a>
            </div>
            <div class="dashboard-sidebar-progress">
                <div class="dashboard-sidebar-progress-header">
                    <span>Profile completion</span>
                    <span class="dashboard-sidebar-progress-value"><?= $profileStrength ?>%</span>
                </div>
                <div class="dashboard-sidebar-progress-bar">
                    <div style="width: <?= $profileStrength ?>%;"></div>
                </div>
                <p class="dashboard-sidebar-progress-note"><?= esc($profilePrompt) ?></p>
                <div class="dashboard-sidebar-stats">
                    <a href="<?= base_url('candidate/applications') ?>" class="dashboard-sidebar-stat">
                        <strong><?= esc($formatCompactCount($applicationCount)) ?></strong>
                        <span>Applied</span>
                    </a>
                    <a href="<?= base_url('candidate/saved-jobs') ?>" class="dashboard-sidebar-stat">
                        <strong><?= esc($formatCompactCount($savedJobsCount)) ?></strong>
                        <span>Saved</span>
                    </a>
                    <a href="<?= base_url('candidate/job-alerts') ?>" class="dashboard-sidebar-stat">
                        <strong><?= esc($formatCompactCount($jobAlertsCount)) ?></strong>
                        <span>Alerts</span>
                    </a>
                </div>
            </div>
            <nav class="dashboard-sidebar-menu">
                <a href="<?= base_url('candidate/dashboard') ?>" class="<?= $sidebarClass('dashboard') ?>"><i class="fas fa-home"></i> Dashboard</a>
                <a href="<?= base_url('candidate/applications') ?>" class="<?= $sidebarClass('applications') ?>"><i class="fas fa-briefcase"></i> Applications <span class="dashboard-sidebar-pill"><?= esc($formatCompactCount($applicationCount)) ?></span></a>
                <a href="<?= base_url('jobs?tab=suggested') ?>" class="<?= $sidebarClass('suggested') ?>"><i class="fas fa-fire"></i> Recommended Jobs <span class="dashboard-sidebar-pill dashboard-sidebar-pill-accent">For You</span></a>
                <a href="<?= base_url('candidate/saved-jobs') ?>" class="<?= $sidebarClass('saved') ?>"><i class="fas fa-bookmark"></i> Saved Jobs <span class="dashboard-sidebar-pill"><?= esc($formatCompactCount($savedJobsCount)) ?></span></a>
                <a href="<?= base_url('companies') ?>" class="<?= $sidebarClass('companies') ?>"><i class="fas fa-building"></i> Companies</a>
            </nav>
            <div class="dashboard-sidebar-cta">
                <div class="dashboard-sidebar-cta-kicker">Next Step</div>
                <h3><?= esc($nextActionTitle) ?></h3>
                <p><?= esc($nextActionText) ?></p>
                <div class="dashboard-sidebar-cta-actions">
                    <a href="<?= esc($nextActionUrl) ?>" class="dashboard-sidebar-cta-primary"><?= esc($nextActionCta) ?></a>
                    <a href="<?= base_url('candidate/profile') ?>" class="dashboard-sidebar-cta-secondary"><?= esc($profilePromptCta) ?></a>
                </div>
            </div>
        </aside>
        <div class="dashboard-main">
            <section class="dashboard-section">
                <div class="container">
                    <?php if (!empty($bannerItems)): ?>
            <div class="naukri-banner-strip naukri-banner-strip--inline mb-3">
                <div class="naukri-banner-slider" data-dashboard-banner-slider data-active-index="<?= $bannerActiveIndex ?>">
                    <?php foreach ($bannerItems as $index => $banner): ?>
                        <div class="naukri-banner-item<?= $index === $bannerActiveIndex ? ' is-active' : '' ?>" data-banner-slide>
                            <span class="naukri-banner-label"><?= esc((string) ($banner['label'] ?? '')) ?></span>
                            <span class="naukri-banner-title"><?= esc((string) ($banner['title'] ?? '')) ?></span>
                            <a href="<?= esc((string) ($banner['action_link'] ?? base_url('candidate/dashboard'))) ?>" class="naukri-banner-cta">
                                <?= esc((string) ($banner['action_text'] ?? 'View')) ?> <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                    <?php if (count($bannerItems) > 1): ?>
                        <div class="naukri-banner-dots">
                            <?php foreach ($bannerItems as $index => $banner): ?>
                                <button type="button" class="naukri-banner-dot<?= $index === $bannerActiveIndex ? ' is-active' : '' ?>" data-banner-dot="<?= $index ?>" aria-label="<?= esc((string) ($banner['label'] ?? '')) ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="dashboard-summary-shell">
                <div class="dashboard-summary-hero">
                    <div class="dashboard-summary-kicker">My Dashboard</div>
                    <h1 class="dashboard-summary-title">Focus on the jobs most likely to move forward.</h1>
                    <p class="dashboard-summary-text">Track your profile strength, recent applications, saved opportunities, and recruiter-facing activity from one compact workspace.</p>
                    <div class="dashboard-summary-inline-stats">
                        <span><strong><?= esc($formatCompactCount($unreadNotificationCount)) ?></strong> unread updates</span>
                        <span><strong><?= esc($formatCompactCount($jobAlertsCount)) ?></strong> active alerts</span>
                        <span><strong><?= esc($formatCompactCount($savedJobsCount)) ?></strong> saved jobs</span>
                    </div>
                    <div class="dashboard-summary-actions">
                        <a href="<?= esc($nextActionUrl) ?>" class="btn btn-primary dashboard-summary-primary"><?= esc($nextActionCta) ?> <i class="fas fa-arrow-right ms-2"></i></a>
                        <a href="<?= base_url('candidate/applications') ?>" class="btn btn-light dashboard-summary-secondary">Track applications</a>
                    </div>
                </div>
            </div>

            <div class="dashboard-metric-grid">
                <a href="<?= base_url('candidate/profile') ?>" class="dashboard-metric-card dashboard-metric-link">
                    <div class="dashboard-metric-label">Profile strength</div>
                    <div class="dashboard-metric-value"><?= $profileStrength ?>%</div>
                    <div class="dashboard-metric-note"><?= esc($profilePrompt) ?></div>
                </a>
                <a href="<?= base_url('jobs?tab=suggested') ?>" class="dashboard-metric-card dashboard-metric-link">
                    <div class="dashboard-metric-label">Active matches</div>
                    <div class="dashboard-metric-value"><?= $activeMatches ?></div>
                    <div class="dashboard-metric-note">Recommended roles currently aligned to your profile</div>
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
                    <p class="section-subtitle">Based on your skills, target roles, and work preferences</p>
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
                        $experience = trim((string) ($job['experience_level'] ?? ''));
                        $salary = trim((string) ($job['salary_range'] ?? ''));
                        $postedAt = isset($job['posted_at']) ? $formatDate($job['posted_at']) : 'Recently';
                        $companyInitial = strtoupper(substr($company, 0, 1) ?: 'C');
                        $companyLogo = trim((string) ($job['company_logo'] ?? ''));
                        $matchPct = max(10, min(100, $score));
                        $matchLabel = $score > 0 ? $matchPct . '% match' : 'Open role';
                        $isExternalJob = (int) ($job['is_external'] ?? 0) === 1;
                        $externalSource = trim((string) ($job['external_source'] ?? ''));
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
                                <h3 class="job-card-title"><?= esc($title) ?></h3>
                                <p class="job-card-company"><?= esc($company) ?></p>
                                <div class="job-card-meta">
                                    <span><i class="fas fa-map-pin"></i> <?= esc($location) ?></span>
                                    <?php if ($experience !== ''): ?>
                                        <span><i class="fas fa-briefcase"></i> <?= esc($experience) ?></span>
                                    <?php endif; ?>
                                    <?php if ($salary !== ''): ?>
                                        <span><i class="fas fa-rupee-sign"></i> <?= esc($salary) ?></span>
                                    <?php endif; ?>
                                    <span><i class="fas fa-clock"></i> <?= esc($postedAt) ?></span>
                                </div>
                                <div class="job-card-tags">
                                    <span class="badge badge-primary"><?= esc($job['employment_type'] ?: 'Full-time') ?></span>
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
            <?php if (empty($premiumSubscription ?? null)): ?>
            <div class="dashboard-cta-banner">
                <div class="dashboard-cta-banner-inner">
                    <div class="dashboard-cta-copy">
                        <div class="dashboard-cta-kicker">
                            <i class="fas fa-crown"></i>
                            HireMatrix Pro
                        </div>
                        <h2 class="dashboard-cta-title">One subscription unlocks all three AI services</h2>
                        <div class="pro-ad-services">
                            <div class="pro-ad-service">
                                <div class="pro-ad-service-title"><i class="fas fa-route"></i> Career Transition AI</div>
                                <ul class="pro-ad-features">
                                    <li>Personalized roadmap</li>
                                    <li>Daily actionable tasks</li>
                                    <li>Skill gap analysis</li>
                                    <li>Course modules and exercises</li>
                                </ul>
                            </div>
                            <div class="pro-ad-service">
                                <div class="pro-ad-service-title"><i class="fas fa-file-alt"></i> Resume Studio</div>
                                <ul class="pro-ad-features">
                                    <li>ATS-friendly resumes</li>
                                    <li>Job-specific versions</li>
                                    <li>Career transition resumes</li>
                                    <li>Unlimited updates</li>
                                </ul>
                            </div>
                            <div class="pro-ad-service">
                                <div class="pro-ad-service-title"><i class="fas fa-robot"></i> AI Career Mentor</div>
                                <ul class="pro-ad-features">
                                    <li>Unlimited mentor chats</li>
                                    <li>Interview preparation</li>
                                    <li>Resume review guidance</li>
                                    <li>Job search strategy</li>
                                </ul>
                            </div>
                        </div>
                        <a href="<?= base_url('premium/plans') ?>" class="dashboard-cta-btn btn btn-light mt-3">
                            View Plans <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                    <div class="dashboard-cta-art d-none d-lg-flex" aria-hidden="true">
                        <div class="dashboard-cta-orb">
                            <i class="fas fa-crown"></i>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="dashboard-section pt-0">
        <div class="container">
            <?php
            $topHiringCompanies = $topHiringCompanies ?? [];
            $resolveLogoUrl = static function (string $path): string {
                $path = trim($path);
                if ($path === '') return '';
                if (preg_match('#^https?://#i', $path)) return $path;
                return base_url(ltrim($path, '/'));
            };
            ?>
            <?php if (!empty($topHiringCompanies)): ?>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="section-title mb-0">Top Companies Hiring Now</h2>
                <a href="<?= base_url('companies') ?>" class="btn btn-ghost text-primary">View all <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
            <div class="top-companies-grid">
                <?php foreach ($topHiringCompanies as $co): ?>
                    <?php
                    $coName    = trim((string) ($co['name'] ?? 'Company'));
                    $coLogo    = $resolveLogoUrl((string) ($co['logo'] ?? ''));
                    $coIndustry = trim((string) ($co['industry'] ?? ''));
                    $coJobs    = (int) ($co['job_count'] ?? 0);
                    $coInitial = strtoupper(substr($coName, 0, 1) ?: 'C');
                    $coId      = (int) ($co['company_id'] ?? 0);
                    $coUrl     = $coId > 0 ? base_url('company/' . $coId) : base_url('jobs?company=' . urlencode($coName));
                    ?>
                    <a href="<?= esc($coUrl) ?>" class="top-company-card">
                        <div class="top-company-logo">
                            <?php if ($coLogo !== ''): ?>
                                <img src="<?= esc($coLogo) ?>" alt="<?= esc($coName) ?>">
                            <?php else: ?>
                                <span><?= esc($coInitial) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="top-company-info">
                            <div class="top-company-name"><?= esc($coName) ?></div>
                            <?php if ($coIndustry !== ''): ?>
                                <div class="top-company-industry"><?= esc($coIndustry) ?></div>
                            <?php endif; ?>
                            <div class="top-company-jobs"><?= $coJobs ?> <?= $coJobs === 1 ? 'opening' : 'openings' ?></div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if (!empty($blogPosts)): ?>
        <?= view('candidate/dashboard_blog_section', ['blogPosts' => $blogPosts]) ?>
    <?php endif; ?>

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
    </div>

</div>


<?php if (count($bannerItems) > 1): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-dashboard-banner-slider]').forEach(function (slider) {
        var slides = Array.prototype.slice.call(slider.querySelectorAll('[data-banner-slide]'));
        var dots = Array.prototype.slice.call(slider.querySelectorAll('[data-banner-dot]'));
        if (slides.length < 2) return;
        var activeIndex = parseInt(slider.getAttribute('data-active-index') || '0', 10);
        if (isNaN(activeIndex) || activeIndex < 0 || activeIndex >= slides.length) activeIndex = 0;
        var timerId = null;
        var render = function () {
            slides.forEach(function (s, i) { s.classList.toggle('is-active', i === activeIndex); });
            dots.forEach(function (d, i) { d.classList.toggle('is-active', i === activeIndex); });
        };
        var startAutoPlay = function () {
            clearInterval(timerId);
            timerId = setInterval(function () { activeIndex = (activeIndex + 1) % slides.length; render(); }, 4800);
        };
        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                var n = parseInt(dot.getAttribute('data-banner-dot') || '0', 10);
                if (!isNaN(n)) { activeIndex = n; render(); startAutoPlay(); }
            });
        });
        render(); startAutoPlay();
    });
});
</script>
<?php endif; ?>

<?= view('Layouts/candidate_footer') ?>
