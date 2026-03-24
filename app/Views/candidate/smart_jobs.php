<?= view('Layouts/candidate_header', ['title' => 'Find Jobs']) ?>
<?php
$activeTab                  = $activeTab ?? 'recommended';
$recommendationType         = $recommendationType ?? 'skills';
$filters                    = $filters ?? [];
$suggestedJobs              = $suggestedJobs ?? [];
$suggestedJobsByApplies     = $suggestedJobsByApplies ?? [];
$suggestedJobsBySkills      = $suggestedJobsBySkills ?? [];
$suggestedJobsByPreferences = $suggestedJobsByPreferences ?? [];
$suggestedJobsByAi          = $suggestedJobsByAi ?? [];
$candidateSkills            = $candidateSkills ?? [];
$candidateInterests         = $candidateInterests ?? [];
$behavior                   = $behavior ?? [];
$showFilters                = $showFilters ?? false;
$totalJobs                  = $totalJobs ?? 0;
$jobs                       = $jobs ?? [];
$locations                  = $locations ?? [];
$categories                 = $categories ?? [];
$experienceLevels           = $experienceLevels ?? [];
$employmentTypes            = $employmentTypes ?? [];
$savedJobIds                = $savedJobIds ?? [];
$salaryRanges = [
    '' => 'Any Salary',
    'under_3' => 'Under 3 LPA',
    '3_5' => '3 - 5 LPA',
    '5_8' => '5 - 8 LPA',
    '8_12' => '8 - 12 LPA',
    '12_plus' => '12+ LPA',
];
$workModes = [
    '' => 'Any mode',
    'remote' => 'Remote',
    'hybrid' => 'Hybrid',
    'onsite' => 'On-site',
];

$recommendationSets = [
    'applies' => $suggestedJobsByApplies,
    'skills' => $suggestedJobsBySkills,
    'preferences' => $suggestedJobsByPreferences,
    'ai' => $suggestedJobsByAi,
];
if (!array_key_exists($recommendationType, $recommendationSets)) {
    $recommendationType = 'skills';
}
$activeRecommendedJobs = $recommendationSets[$recommendationType];
$jobsHeroTitle = $showFilters ? 'Browse Jobs' : 'Jobs Matching Your Profile';
$jobsHeroSubtitle = $showFilters
    ? 'Use live filters to narrow roles by company, location, experience, job type, salary, and work mode.'
    : 'Based on your skills, preferences, and application history';

$jobIconSet = [
    'developer' => 'fas fa-code',
    'engineer' => 'fas fa-cogs',
    'designer' => 'fas fa-palette',
    'manager' => 'fas fa-chart-line',
    'data' => 'fas fa-database',
    'marketing' => 'fas fa-bullhorn',
    'product' => 'fas fa-briefcase',
    'frontend' => 'fas fa-code',
    'backend' => 'fas fa-server',
    'full stack' => 'fas fa-layer-group',
];

$pickJobIcon = static function (string $title) use ($jobIconSet): string {
    $needle = strtolower($title);
    foreach ($jobIconSet as $key => $icon) {
        if (str_contains($needle, $key)) {
            return $icon;
        }
    }

    return 'fas fa-briefcase';
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

$formatPostedMeta = static function (?string $createdAt): ?string {
    $raw = trim((string) $createdAt);
    if ($raw === '') {
        return null;
    }

    try {
        $postedAt = new \DateTime($raw);
        $postedDay = (clone $postedAt)->setTime(0, 0, 0);
        $today = new \DateTime('today');
        $interval = $postedDay->diff($today);
        $days = $interval->invert === 1 ? 0 : (int) $interval->days;
        $relative = $days === 0 ? 'today' : ($days === 1 ? '1 day ago' : $days . ' days ago');
        return 'Posted on ' . $postedAt->format('d M Y') . ' • ' . $relative;
    } catch (\Throwable $e) {
        return null;
    }
};

$renderRecommendedPane = static function (
    string $recType,
    array $jobs,
    string $tabLabel
) use ($recommendationType, $formatPostedMeta, $savedJobIds, $resolveAssetUrl): string {
    ob_start();
    $isActivePane = $recommendationType === $recType;
    ?>
    <div class="recommended-job-pane <?= $isActivePane ? '' : 'd-none' ?>" data-rec-pane="<?= esc($recType) ?>" data-rec-label="<?= esc($tabLabel) ?>">
        <?php if (!empty($jobs)): ?>
            <div class="results-bar">
                <span class="results-count"><strong><?= count($jobs) ?></strong> jobs matched in this recommendation view</span>
            </div>

            <div class="recommended-job-grid mb-4">
            <?php foreach ($jobs as $job): ?>
                <?php
                    $score = (float) ($job['match_score'] ?? 0);
                    $title = (string) ($job['title'] ?? 'Untitled Role');
                    $company = (string) ($job['company'] ?? 'Company');
                    $location = (string) ($job['location'] ?? 'N/A');
                    $postedMeta = $formatPostedMeta($job['created_at'] ?? null);
                    $isSaved = in_array((int) ($job['id'] ?? 0), $savedJobIds, true);
                    $type = strtolower((string) ($job['employment_type'] ?? ''));
                    $typeBadge = str_contains($type, 'part') ? 'badge-secondary' : 'badge-primary';
                    $matchPct = max(10, min(100, (int) round($score)));
                    $companyInitial = strtoupper(substr($company, 0, 1) ?: 'C');
                    $companyLogo = trim((string) ($job['company_logo'] ?? ''));
                    $matchLabel = $matchPct . '% match';
                ?>
                <article class="job-card recommended-job-card">
                    <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary py-0 px-2 job-card-save js-save-job-toggle <?= $isSaved ? 'is-saved' : '' ?>"
                        aria-label="<?= $isSaved ? 'Saved job' : 'Save job' ?>"
                        title="<?= $isSaved ? 'Saved' : 'Save Job' ?>"
                        data-save-url="<?= base_url($isSaved ? 'job/unsave/' . $job['id'] : 'job/save/' . $job['id']) ?>"
                        data-job-id="<?= (int) $job['id'] ?>"
                        data-saved="<?= $isSaved ? '1' : '0' ?>"
                        data-save-label-save="Save Job"
                        data-save-label-saved="Saved"
                    >
                        <i class="<?= $isSaved ? 'fas' : 'far' ?> fa-bookmark"></i>
                    </button>
                    <div class="job-card-icon">
                        <?php if ($companyLogo !== ''): ?>
                            <img src="<?= esc($resolveAssetUrl($companyLogo)) ?>" alt="<?= esc($company) ?>">
                        <?php else: ?>
                            <span><?= esc($companyInitial) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="job-card-body">
                        <div class="job-card-match-badge"><?= esc($matchLabel) ?></div>
                        <h3 class="job-card-title"><?= esc($title) ?></h3>
                        <p class="job-card-company"><?= esc($company) ?></p>
                        <div class="job-card-meta">
                            <span><i class="fas fa-map-pin"></i> <?= esc($location) ?></span>
                            <?php if ($postedMeta !== null): ?>
                                <span><i class="fas fa-clock"></i> <?= esc($postedMeta) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="job-card-tags">
                            <span class="badge <?= $typeBadge ?>"><?= esc($job['employment_type'] ?: 'Full Time') ?></span>
                            <span class="badge badge-secondary"><?= esc(substr($title, 0, 15) ?: 'Role') ?></span>
                        </div>
                        <?php if (!empty($job['match_reason'])): ?>
                            <div class="small text-muted mb-2"><?= esc($job['match_reason']) ?></div>
                        <?php endif; ?>
                        <div class="progress-container">
                            <div class="progress-bar-custom" style="width: <?= $matchPct ?>%;"></div>
                            <span class="progress-label"><?= $matchPct ?>% match</span>
                        </div>
                        <a href="<?= base_url('job/' . $job['id']) ?>" class="view-details">View Details &rarr;</a>
                    </div>
                </article>
            <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-star"></i>
                <h5>No suitable jobs found</h5>
                <p>No matches available in this recommendation view right now.</p>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return (string) ob_get_clean();
};

$request = service('request');
$queryParams = $request->getGet();
$buildJobsUrl = static function (array $overrides = [], array $remove = []) use ($queryParams): string {
    $params = $queryParams;
    foreach ($remove as $key) {
        unset($params[$key]);
    }

    foreach ($overrides as $key => $value) {
        if ($value === null || $value === '' || $value === []) {
            unset($params[$key]);
            continue;
        }
        $params[$key] = $value;
    }

    return base_url('jobs') . (empty($params) ? '' : '?' . http_build_query($params));
};

$activeFilterChips = [];
$addActiveChip = function (string $label, string $url) use (&$activeFilterChips): void {
    $activeFilterChips[] = [
        'label' => $label,
        'url' => $url,
    ];
};

if (!empty($filters['search'])) {
    $addActiveChip('Search: ' . (string) $filters['search'], $buildJobsUrl(['search' => null], ['search']));
}
if (!empty($filters['category'])) {
    $addActiveChip('Category: ' . (string) $filters['category'], $buildJobsUrl(['category' => null], ['category']));
}
if (!empty($filters['location'])) {
    $addActiveChip('Location: ' . (string) $filters['location'], $buildJobsUrl(['location' => null], ['location']));
}
if (!empty($filters['work_mode'])) {
    $workModeLabel = $workModes[(string) $filters['work_mode']] ?? (string) $filters['work_mode'];
    $addActiveChip('Mode: ' . $workModeLabel, $buildJobsUrl(['work_mode' => null], ['work_mode']));
}
if (!empty($filters['salary_range'])) {
    $salaryLabel = $salaryRanges[(string) $filters['salary_range']] ?? (string) $filters['salary_range'];
    $addActiveChip('Salary: ' . $salaryLabel, $buildJobsUrl(['salary_range' => null], ['salary_range']));
}
foreach ((array) ($filters['employment_type'] ?? []) as $employmentType) {
    $employmentType = (string) $employmentType;
    $remaining = array_values(array_filter((array) ($filters['employment_type'] ?? []), static fn ($value) => (string) $value !== $employmentType));
    $addActiveChip('Type: ' . $employmentType, $buildJobsUrl(['employment_type' => $remaining], []));
}
foreach ((array) ($filters['experience_level'] ?? []) as $experienceLevel) {
    $experienceLevel = (string) $experienceLevel;
    $remaining = array_values(array_filter((array) ($filters['experience_level'] ?? []), static fn ($value) => (string) $value !== $experienceLevel));
    $addActiveChip('Experience: ' . $experienceLevel, $buildJobsUrl(['experience_level' => $remaining], []));
}
if (!empty($filters['posted_within'])) {
    $postedLabels = ['1' => 'Today', '3' => 'Last 3 days', '7' => 'Last week', '14' => 'Last 2 weeks'];
    $postedLabel = $postedLabels[(string) $filters['posted_within']] ?? (string) $filters['posted_within'];
    $addActiveChip('Posted: ' . $postedLabel, $buildJobsUrl(['posted_within' => null], ['posted_within']));
}

$activeFilterCount = count($activeFilterChips);
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="jobs-page-jobboard">
<div class="container">
    <div class="page-board-header page-board-header-tight">
        <div class="page-board-copy">
            <span class="page-board-kicker"><i class="fas fa-sparkles"></i> AI-powered matching</span>
            <h1 class="page-board-title"><?= esc($jobsHeroTitle) ?></h1>
            <p class="page-board-subtitle"><?= esc($jobsHeroSubtitle) ?></p>
            <?php if ($showFilters): ?>
            <div class="custom-breadcrumbs">
                <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                <span class="mx-2 slash">/</span>
                <span><strong>Browse Jobs</strong></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<section class="site-section pt-0">
<div class="container">
<form method="GET" action="<?= base_url('jobs') ?>" id="filterForm">
    <input type="hidden" name="search" id="hiddenSearch" value="<?= esc($filters['search'] ?? '') ?>">
    <input type="hidden" name="tab" id="activeTabInput" value="<?= esc($activeTab) ?>">
    <input type="hidden" name="rec" id="recommendationTypeInput" value="<?= esc($recommendationType) ?>">

    <div class="jobs-layout <?= $showFilters ? '' : 'jobs-layout-no-sidebar' ?>">

        <?php if ($showFilters): ?>
            <div class="sidebar">
                <div class="sidebar-head">
                    <h5><i class="fas fa-sliders-h"></i> Filters</h5>
                    <a href="<?= base_url('jobs?tab=all') ?>" class="clear-link" data-jobs-filter-link="1">Clear all</a>
                </div>

            <?php if (!empty($categories)): ?>
            <div class="filter-section">
                <span class="filter-label">Category</span>
                <select name="category" onchange="submitFilters()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= esc($cat['category']) ?>" <?= ($filters['category'] ?? '') == $cat['category'] ? 'selected' : '' ?>>
                            <?= esc($cat['category']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <?php if (!empty($locations)): ?>
            <div class="filter-section">
                <span class="filter-label">Location</span>
                <select name="location" onchange="submitFilters()">
                    <option value="">All Locations</option>
                    <?php foreach ($locations as $loc): ?>
                        <option value="<?= esc($loc['location']) ?>" <?= ($filters['location'] ?? '') == $loc['location'] ? 'selected' : '' ?>>
                            <?= esc($loc['location']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="filter-section">
                <span class="filter-label">Work Mode</span>
                <select name="work_mode" onchange="submitFilters()">
                    <?php foreach ($workModes as $modeValue => $modeLabel): ?>
                        <option value="<?= esc($modeValue) ?>" <?= ($filters['work_mode'] ?? '') === $modeValue ? 'selected' : '' ?>>
                            <?= esc($modeLabel) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-section">
                <span class="filter-label">Salary Range</span>
                <select name="salary_range" onchange="submitFilters()">
                    <?php foreach ($salaryRanges as $rangeValue => $rangeLabel): ?>
                        <option value="<?= esc($rangeValue) ?>" <?= ($filters['salary_range'] ?? '') === $rangeValue ? 'selected' : '' ?>>
                            <?= esc($rangeLabel) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (!empty($employmentTypes)): ?>
            <div class="filter-section">
                <span class="filter-label">Job Type</span>
                <?php foreach ($employmentTypes as $type): ?>
                    <label class="check-item">
                        <input type="checkbox" name="employment_type[]" value="<?= esc($type['employment_type']) ?>"
                               <?= in_array($type['employment_type'], (array) ($filters['employment_type'] ?? []), true) ? 'checked' : '' ?>
                               onchange="submitFilters()">
                        <span class="check-box"></span>
                        <span class="check-text"><?= esc($type['employment_type']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($experienceLevels)): ?>
            <div class="filter-section">
                <span class="filter-label">Experience</span>
                <?php foreach ($experienceLevels as $exp): ?>
                    <label class="check-item">
                        <input type="checkbox" name="experience_level[]" value="<?= esc($exp['experience_level']) ?>"
                               <?= in_array($exp['experience_level'], (array) ($filters['experience_level'] ?? []), true) ? 'checked' : '' ?>
                               onchange="submitFilters()">
                        <span class="check-box"></span>
                        <span class="check-text"><?= esc($exp['experience_level']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="filter-section">
                <span class="filter-label">Posted Within</span>
                <?php foreach (['' => 'Any time', '1' => 'Today', '3' => 'Last 3 days', '7' => 'Last week', '14' => 'Last 2 weeks'] as $val => $label): ?>
                    <label class="check-item">
                        <input type="radio" name="posted_within" value="<?= $val ?>"
                               <?= ($filters['posted_within'] ?? '') == $val ? 'checked' : '' ?>
                               onchange="submitFilters()">
                        <span class="radio-box"></span>
                        <span class="check-text"><?= $label ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="jobs-main">

            <?php if ($showFilters): ?>
            <button type="button" class="mobile-filter-toggle" onclick="toggleMobileFilters()">
                <span><i class="fas fa-sliders-h" style="margin-right:8px"></i>Filters</span>
                <i class="fas fa-chevron-down" id="mobileFilterIcon"></i>
            </button>

            <div class="mobile-filter-drawer" id="mobileFilterDrawer">
                <div style="padding:20px;">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label style="font-size:.8rem;font-weight:600;color:var(--slate)">Category</label>
                            <select id="mobileCategory" class="form-control" style="font-size:.85rem">
                                <option value="">All</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= esc($cat['category']) ?>" <?= ($filters['category'] ?? '') == $cat['category'] ? 'selected' : '' ?>>
                                        <?= esc($cat['category']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label style="font-size:.8rem;font-weight:600;color:var(--slate)">Location</label>
                            <select id="mobileLocation" class="form-control" style="font-size:.85rem">
                                <option value="">All</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?= esc($loc['location']) ?>" <?= ($filters['location'] ?? '') == $loc['location'] ? 'selected' : '' ?>>
                                        <?= esc($loc['location']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label style="font-size:.8rem;font-weight:600;color:var(--slate)">Experience</label>
                            <select id="mobileExperience" class="form-control" style="font-size:.85rem">
                                <option value="">All</option>
                                <?php foreach ($experienceLevels as $exp): ?>
                                    <option value="<?= esc($exp['experience_level']) ?>" <?= in_array($exp['experience_level'], (array) ($filters['experience_level'] ?? []), true) ? 'selected' : '' ?>>
                                        <?= esc($exp['experience_level']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label style="font-size:.8rem;font-weight:600;color:var(--slate)">Job Type</label>
                            <select id="mobileEmploymentType" class="form-control" style="font-size:.85rem">
                                <option value="">All</option>
                                <?php foreach ($employmentTypes as $type): ?>
                                    <option value="<?= esc($type['employment_type']) ?>" <?= in_array($type['employment_type'], (array) ($filters['employment_type'] ?? []), true) ? 'selected' : '' ?>>
                                        <?= esc($type['employment_type']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label style="font-size:.8rem;font-weight:600;color:var(--slate)">Work Mode</label>
                            <select id="mobileWorkMode" class="form-control" style="font-size:.85rem">
                                <?php foreach ($workModes as $modeValue => $modeLabel): ?>
                                    <option value="<?= esc($modeValue) ?>" <?= ($filters['work_mode'] ?? '') === $modeValue ? 'selected' : '' ?>>
                                        <?= esc($modeLabel) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label style="font-size:.8rem;font-weight:600;color:var(--slate)">Salary Range</label>
                            <select id="mobileSalaryRange" class="form-control" style="font-size:.85rem">
                                <?php foreach ($salaryRanges as $rangeValue => $rangeLabel): ?>
                                    <option value="<?= esc($rangeValue) ?>" <?= ($filters['salary_range'] ?? '') === $rangeValue ? 'selected' : '' ?>>
                                        <?= esc($rangeLabel) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" onclick="applyMobileFilters()" style="flex:1;background:var(--ink);color:white;border:none;border-radius:8px;padding:10px;font-family:'Syne',sans-serif;font-weight:700;cursor:pointer;">Apply</button>
                        <a href="<?= base_url('jobs?tab=all') ?>" data-jobs-filter-link="1" style="flex:1;background:var(--smoke);color:var(--ink);border:1.5px solid var(--border);border-radius:8px;padding:10px;font-family:'Syne',sans-serif;font-weight:700;text-align:center;text-decoration:none;display:flex;align-items:center;justify-content:center;">Clear</a>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('career_suggestion')):
                $suggestion = session()->getFlashdata('career_suggestion'); ?>
            <div style="background:#f0fdf4;border:1.5px solid #bbf7d0;border-radius:12px;padding:16px 20px;margin-bottom:18px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap;">
                <div>
                    <strong style="color:var(--green2)"><i class="fas fa-rocket"></i> Career Transition Opportunity!</strong>
                    <p style="margin:4px 0 0;font-size:.88rem;color:var(--slate)"><?= esc($suggestion['message']) ?></p>
                </div>
                <a href="<?= base_url('career-transition') ?>" style="background:var(--green);color:var(--ink);border-radius:8px;padding:9px 18px;font-weight:700;font-family:'Syne',sans-serif;text-decoration:none;white-space:nowrap;font-size:.85rem;">
                    <i class="fas fa-graduation-cap"></i> Get Roadmap
                </a>
            </div>
            <?php endif; ?>

            <?php if ($showFilters): ?>
            <div id="tab-all" class="<?= $activeTab !== 'all' ? 'd-none' : '' ?>">
                <?php if (!$showFilters): ?>
                <div class="results-bar">
                    <span class="results-count">Use the header search bar to open filters for refining job results.</span>
                </div>
                <?php endif; ?>

                <div class="jobs-search-feedback">
                    <div class="jobs-search-feedback-copy">
                        <div class="jobs-search-feedback-kicker">Search feedback</div>
                        <p class="jobs-search-feedback-title">
                            <?= $activeFilterCount > 0 ? $activeFilterCount . ' active filter' . ($activeFilterCount === 1 ? '' : 's') . ' applied' : 'No active filters' ?>
                        </p>
                        <p class="jobs-search-feedback-text">
                            <?php if (!empty($filters['search'])): ?>
                                Showing results for <strong>"<?= esc($filters['search']) ?>"</strong>.
                            <?php else: ?>
                                Narrow by role, location, salary, and work mode to refine the list.
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="jobs-search-feedback-actions">
                        <a href="<?= base_url('jobs?tab=all') ?>" class="btn btn-outline-secondary btn-sm" data-jobs-filter-link="1">Clear all filters</a>
                    </div>
                </div>

                <?php if (!empty($activeFilterChips)): ?>
                    <div class="active-filter-chips">
                        <?php foreach ($activeFilterChips as $chip): ?>
                            <a href="<?= esc($chip['url']) ?>" class="active-filter-chip" data-jobs-filter-link="1">
                                <span><?= esc($chip['label']) ?></span>
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="results-bar">
                    <span class="results-count">
                        <?php if (!empty($filters['search'])): ?>
                            Results for <strong>"<?= esc($filters['search']) ?>"</strong> -
                        <?php endif; ?>
                        <strong><?= $totalJobs ?></strong> job<?= $totalJobs != 1 ? 's' : '' ?> found
                    </span>
                </div>

                <?php if (!empty($jobs)): ?>
                    <div class="row g-4 mb-4">
                    <?php foreach ($jobs as $job): ?>
                        <?php
                            $title = (string) ($job['title'] ?? 'Untitled Role');
                            $company = (string) ($job['company'] ?? 'Company');
                            $location = (string) ($job['location'] ?? 'N/A');
                            $postedMeta = $formatPostedMeta($job['created_at'] ?? null);
                            $isSaved = in_array((int) ($job['id'] ?? 0), $savedJobIds, true);
                            $type = strtolower((string) ($job['employment_type'] ?? ''));
                            $typeBadge = str_contains($type, 'part') ? 'badge-secondary' : 'badge-primary';
                            $companyInitial = strtoupper(substr($company, 0, 1) ?: 'C');
                            $companyLogo = trim((string) ($job['company_logo'] ?? ''));
                            $score = (int) round((float) ($job['match_score'] ?? 0));
                            $matchLabel = $score > 0 ? max(10, min(100, $score)) . '% match' : 'Open role';
                        ?>
                        <div class="col-12">
                            <div class="job-card">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary py-0 px-2 job-card-save js-save-job-toggle <?= $isSaved ? 'is-saved' : '' ?>"
                                    aria-label="<?= $isSaved ? 'Saved job' : 'Save job' ?>"
                                    title="<?= $isSaved ? 'Saved' : 'Save Job' ?>"
                                    data-save-url="<?= base_url($isSaved ? 'job/unsave/' . $job['id'] : 'job/save/' . $job['id']) ?>"
                                    data-job-id="<?= (int) $job['id'] ?>"
                                    data-saved="<?= $isSaved ? '1' : '0' ?>"
                                    data-save-label-save="Save Job"
                                    data-save-label-saved="Saved"
                                >
                                    <i class="<?= $isSaved ? 'fas' : 'far' ?> fa-bookmark"></i>
                                </button>
                                <div class="job-card-icon">
                                    <?php if ($companyLogo !== ''): ?>
                                        <img src="<?= esc($resolveAssetUrl($companyLogo)) ?>" alt="<?= esc($company) ?>">
                                    <?php else: ?>
                                        <span><?= esc($companyInitial) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="job-card-body">
                                    <div class="job-card-match-badge"><?= esc($matchLabel) ?></div>
                                    <h3 class="job-card-title"><?= esc($title) ?></h3>
                                    <p class="job-card-company"><?= esc($company) ?></p>
                                    <div class="job-card-meta">
                                        <span><i class="fas fa-map-pin"></i> <?= esc($location) ?></span>
                                        <?php if ($postedMeta !== null): ?>
                                            <span><i class="fas fa-clock"></i> <?= esc($postedMeta) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="job-card-tags">
                                        <span class="badge <?= $typeBadge ?>"><?= esc($job['employment_type'] ?: 'Full Time') ?></span>
                                        <span class="badge badge-secondary"><?= esc(substr($title, 0, 15) ?: 'Role') ?></span>
                                    </div>
                                    <div class="progress-container">
                                        <div class="progress-bar-custom" style="width: 100%;"></div>
                                        <span class="progress-label">Open role</span>
                                    </div>
                                    <a href="<?= base_url('job/' . $job['id']) ?>" class="view-details">View Details &rarr;</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    </div>

                    <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                    <div class="row pagination-wrap">
                        <div class="col-md-6 text-center text-md-left mb-3 mb-md-0">
                            <span>Showing page <?= $pager->getCurrentPage() ?> of <?= $pager->getPageCount() ?></span>
                        </div>
                        <div class="col-md-6 text-center text-md-right">
                            <div class="custom-pagination ml-auto">
                            <?php
                                $cur   = $pager->getCurrentPage();
                                $total = $pager->getPageCount();
                                $base  = preg_replace('/[?&]page=\d+/', '', current_url(true)->__toString());
                                $sep   = strpos($base, '?') !== false ? '&' : '?';
                                if ($cur > 1):
                            ?>
                                <a class="prev" href="<?= $base . $sep . 'page=' . ($cur - 1) ?>">Prev</a>
                            <?php endif; ?>
                                <div class="d-inline-block">
                                <?php for ($i = 1; $i <= $total; $i++): ?>
                                    <?php if ($i == $cur): ?>
                                        <a class="active" href="#"><?= $i ?></a>
                                    <?php else: ?>
                                        <a href="<?= $base . $sep . 'page=' . $i ?>"><?= $i ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                </div>
                            <?php if ($cur < $total): ?>
                                <a class="next" href="<?= $base . $sep . 'page=' . ($cur + 1) ?>">Next</a>
                            <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h5>No jobs found</h5>
                        <p>Try adjusting your search or clearing filters</p>
                        <a href="<?= base_url('jobs') ?>" style="display:inline-block;margin-top:12px;background:var(--ink);color:white;padding:10px 24px;border-radius:8px;text-decoration:none;font-family:'Syne',sans-serif;font-weight:700;">Clear All</a>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php if (!$showFilters): ?>
            <div id="tab-recommended" class="<?= $activeTab !== 'recommended' ? 'd-none' : '' ?>">

                <?php if (!empty($candidateSkills) || !empty($candidateInterests)): ?>
                <div class="profile-strip">
                    <?php if (!empty($candidateSkills)): ?>
                    <div class="profile-strip-section">
                        <div class="strip-label">Your Skills</div>
                        <?php foreach (array_slice($candidateSkills, 0, 5) as $sk): ?>
                            <span class="skill-chip"><?= esc($sk) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($candidateInterests)): ?>
                    <div class="profile-strip-section">
                        <div class="strip-label">Your Interests</div>
                        <?php foreach (array_slice($candidateInterests, 0, 5) as $int): ?>
                            <span class="interest-chip"><?= esc($int) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="tabs-row" style="margin-top:12px;">
                    <div class="tab-pills">
                        <button type="button" class="tab-pill <?= $recommendationType === 'applies' ? 'active' : '' ?>" data-rec-type="applies" onclick="switchRecommendation('applies', event)">
                            <i class="fas fa-history"></i> Based On Applies
                            <span class="pill-count"><?= count($suggestedJobsByApplies) ?></span>
                        </button>
                        <button type="button" class="tab-pill <?= $recommendationType === 'skills' ? 'active' : '' ?>" data-rec-type="skills" onclick="switchRecommendation('skills', event)">
                            <i class="fas fa-tools"></i> Based On Skills
                            <span class="pill-count"><?= count($suggestedJobsBySkills) ?></span>
                        </button>
                        <button type="button" class="tab-pill <?= $recommendationType === 'preferences' ? 'active' : '' ?>" data-rec-type="preferences" onclick="switchRecommendation('preferences', event)">
                            <i class="fas fa-heart"></i> Preferences / Interests
                            <span class="pill-count"><?= count($suggestedJobsByPreferences) ?></span>
                        </button>
                        <button type="button" class="tab-pill <?= $recommendationType === 'ai' ? 'active' : '' ?>" data-rec-type="ai" onclick="switchRecommendation('ai', event)">
                            <i class="fas fa-brain"></i> Other Recommendations
                            <span class="pill-count"><?= count($suggestedJobsByAi) ?></span>
                        </button>
                    </div>
                </div>

                <div class="recommended-jobs-stage">
                    <?= $renderRecommendedPane('applies', $suggestedJobsByApplies, 'Based On Applies') ?>
                    <?= $renderRecommendedPane('skills', $suggestedJobsBySkills, 'Based On Skills') ?>
                    <?= $renderRecommendedPane('preferences', $suggestedJobsByPreferences, 'Preferences / Interests') ?>
                    <?= $renderRecommendedPane('ai', $suggestedJobsByAi, 'Other Recommendations') ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</form>
</div>
</section>
</div>

<?= view('Layouts/candidate_footer') ?>
