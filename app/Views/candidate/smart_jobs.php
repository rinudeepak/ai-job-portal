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
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="jobs-page-jobboard">
<section class="section-hero overlay inner-page bg-image jobs-hero" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h1 class="text-white font-weight-bold">Find Jobs</h1>
                <div class="custom-breadcrumbs">
                    <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                    <span class="mx-2 slash">/</span>
                    <span class="text-white"><strong>Jobs</strong></span>
                </div>
                <p><?= $showFilters ? 'Search and filter jobs from the results.' : 'Get recommendations by applications, skills, preferences, and AI ranking.' ?></p>
            </div>
        </div>
    </div>
</section>

<section class="site-section pt-0">
<div class="container">
<form method="GET" action="<?= base_url('jobs') ?>" id="filterForm">
    <input type="hidden" name="search" id="hiddenSearch" value="<?= esc($filters['search'] ?? '') ?>">
    <input type="hidden" name="tab" id="activeTabInput" value="<?= esc($activeTab) ?>">
    <input type="hidden" name="rec" id="recommendationTypeInput" value="<?= esc($recommendationType) ?>">

    <div class="jobs-layout">

        <?php if ($showFilters): ?>
            <div class="sidebar">
                <div class="sidebar-head">
                    <h5><i class="fas fa-sliders-h"></i> Filters</h5>
                    <a href="<?= base_url('jobs?tab=all') ?>" class="clear-link">Clear all</a>
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
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" onclick="applyMobileFilters()" style="flex:1;background:var(--ink);color:white;border:none;border-radius:8px;padding:10px;font-family:'Syne',sans-serif;font-weight:700;cursor:pointer;">Apply</button>
                        <a href="<?= base_url('jobs?tab=all') ?>" style="flex:1;background:var(--smoke);color:var(--ink);border:1.5px solid var(--border);border-radius:8px;padding:10px;font-family:'Syne',sans-serif;font-weight:700;text-align:center;text-decoration:none;display:flex;align-items:center;justify-content:center;">Clear</a>
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

                <div class="results-bar">
                    <span class="results-count">
                        <?php if (!empty($filters['search'])): ?>
                            Results for <strong>"<?= esc($filters['search']) ?>"</strong> -
                        <?php endif; ?>
                        <strong><?= $totalJobs ?></strong> job<?= $totalJobs != 1 ? 's' : '' ?> found
                    </span>
                </div>

                <?php if (!empty($jobs)): ?>
                    <ul class="job-listings mb-4">
                    <?php foreach ($jobs as $job): ?>
                        <?php
                            $initial = strtoupper(substr((string) ($job['company'] ?? 'J'), 0, 1));
                            $isSaved = in_array((int) ($job['id'] ?? 0), $savedJobIds, true);
                        ?>
                        <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center">
                            <a href="<?= base_url('job/' . $job['id']) ?>" aria-label="Open <?= esc($job['title']) ?>"></a>
                            <div class="job-listing-logo">
                                <?php if (!empty($job['company_logo'])): ?>
                                    <img src="<?= base_url($job['company_logo']) ?>" alt="<?= esc($job['company']) ?>" class="img-fluid">
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center rounded bg-light text-muted" style="width: 90px; height: 90px; font-size: 28px;">
                                        <?= $initial ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
                                <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
                                    <h2><?= esc($job['title']) ?></h2>
                                    <strong><?= esc($job['company']) ?></strong>
                                    <?php $postedMeta = $formatPostedMeta($job['created_at'] ?? null); ?>
                                    <?php if ($postedMeta !== null): ?>
                                        <div class="small text-muted mt-1"><?= esc($postedMeta) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
                                    <span class="icon-room"></span> <?= esc($job['location']) ?>
                                </div>
                                <div class="job-listing-meta">
                                    <?php
                                        $type = strtolower((string) ($job['employment_type'] ?? ''));
                                        $typeBadge = str_contains($type, 'part') ? 'badge-danger' : 'badge-success';
                                    ?>
                                    <span class="badge <?= $typeBadge ?>"><?= esc($job['employment_type'] ?: 'Full Time') ?></span>
                                    <div class="mt-2">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary py-0 px-2 <?= $isSaved ? 'border-success text-success' : '' ?>"
                                            aria-label="<?= $isSaved ? 'Saved job' : 'Save job' ?>"
                                            title="<?= $isSaved ? 'Saved' : 'Save Job' ?>"
                                            onclick="event.stopPropagation();window.location.href='<?= base_url($isSaved ? 'job/unsave/' . $job['id'] : 'job/save/' . $job['id']) ?>';"
                                        >
                                            <i class="<?= $isSaved ? 'fas' : 'far' ?> fa-bookmark"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    </ul>

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
                        <button type="button" class="tab-pill <?= $recommendationType === 'applies' ? 'active' : '' ?>" onclick="switchRecommendation('applies', event)">
                            <i class="fas fa-history"></i> Based On Applies
                            <span class="pill-count"><?= count($suggestedJobsByApplies) ?></span>
                        </button>
                        <button type="button" class="tab-pill <?= $recommendationType === 'skills' ? 'active' : '' ?>" onclick="switchRecommendation('skills', event)">
                            <i class="fas fa-tools"></i> Based On Skills
                            <span class="pill-count"><?= count($suggestedJobsBySkills) ?></span>
                        </button>
                        <button type="button" class="tab-pill <?= $recommendationType === 'preferences' ? 'active' : '' ?>" onclick="switchRecommendation('preferences', event)">
                            <i class="fas fa-heart"></i> Preferences / Interests
                            <span class="pill-count"><?= count($suggestedJobsByPreferences) ?></span>
                        </button>
                        <button type="button" class="tab-pill <?= $recommendationType === 'ai' ? 'active' : '' ?>" onclick="switchRecommendation('ai', event)">
                            <i class="fas fa-brain"></i> Other Recommendations
                            <span class="pill-count"><?= count($suggestedJobsByAi) ?></span>
                        </button>
                    </div>
                </div>

                <?php if (!empty($activeRecommendedJobs)): ?>
                    <div class="results-bar">
                        <span class="results-count"><strong><?= count($activeRecommendedJobs) ?></strong> jobs matched in this recommendation view</span>
                    </div>

                    <ul class="job-listings mb-4">
                    <?php foreach ($activeRecommendedJobs as $index => $job): ?>
                        <?php
                            $score = $job['match_score'] ?? 0;
                            $initial = strtoupper(substr((string) ($job['company'] ?? 'J'), 0, 1));
                            $isSaved = in_array((int) ($job['id'] ?? 0), $savedJobIds, true);
                        ?>
                        <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center">
                            <a href="<?= base_url('job/' . $job['id']) ?>" aria-label="Open <?= esc($job['title']) ?>"></a>
                            <div class="job-listing-logo">
                                <?php if (!empty($job['company_logo'])): ?>
                                    <img src="<?= base_url($job['company_logo']) ?>" alt="<?= esc($job['company']) ?>" class="img-fluid">
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center rounded bg-light text-muted" style="width: 90px; height: 90px; font-size: 28px;">
                                        <?= $initial ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
                                <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
                                    <h2><?= esc($job['title']) ?></h2>
                                    <strong><?= esc($job['company']) ?></strong>
                                    <?php $postedMeta = $formatPostedMeta($job['created_at'] ?? null); ?>
                                    <?php if ($postedMeta !== null): ?>
                                        <div class="small text-muted mt-1"><?= esc($postedMeta) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($job['match_reason'])): ?>
                                    <div class="ai-reason mt-2"><i class="fas fa-lightbulb"></i> <?= esc($job['match_reason']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
                                    <span class="icon-room"></span> <?= esc($job['location']) ?>
                                </div>
                                <div class="job-listing-meta">
                                    <?php
                                        $type = strtolower((string) ($job['employment_type'] ?? ''));
                                        $typeBadge = str_contains($type, 'part') ? 'badge-danger' : 'badge-success';
                                    ?>
                                    <span class="badge <?= $typeBadge ?>"><?= esc($job['employment_type'] ?: 'Full Time') ?></span>
                                    <div class="mt-1 small text-muted"><?= round((float) $score) ?>% match</div>
                                    <div class="mt-2">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-secondary py-0 px-2 <?= $isSaved ? 'border-success text-success' : '' ?>"
                                            aria-label="<?= $isSaved ? 'Saved job' : 'Save job' ?>"
                                            title="<?= $isSaved ? 'Saved' : 'Save Job' ?>"
                                            onclick="event.stopPropagation();window.location.href='<?= base_url($isSaved ? 'job/unsave/' . $job['id'] : 'job/save/' . $job['id']) ?>';"
                                        >
                                            <i class="<?= $isSaved ? 'fas' : 'far' ?> fa-bookmark"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-star"></i>
                        <h5>No suitable jobs found</h5>
                        <p>No matches available in this recommendation view right now.</p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </div>
</form>
</div>
</section>
</div>

<script>
function switchTab(tab, e) {
    if (e) e.preventDefault();

    const url = new URL('<?= base_url('jobs') ?>', window.location.origin);
    const recInput = document.getElementById('recommendationTypeInput');
    const recType = recInput ? recInput.value : 'skills';

    if (tab === 'recommended') {
        url.searchParams.set('tab', 'recommended');
        url.searchParams.set('rec', recType || 'skills');
        window.location.href = url.toString();
        return;
    }

    url.searchParams.set('tab', 'all');
    window.location.href = url.toString();
}

function switchRecommendation(recType, e) {
    if (e) e.preventDefault();

    const url = new URL('<?= base_url('jobs') ?>', window.location.origin);
    url.searchParams.set('tab', 'recommended');
    url.searchParams.set('rec', recType);
    window.location.href = url.toString();
}

function submitFilters() {
    document.getElementById('activeTabInput').value = 'all';
    document.getElementById('filterForm').submit();
}

function toggleMobileFilters() {
    const drawer = document.getElementById('mobileFilterDrawer');
    const icon = document.getElementById('mobileFilterIcon');
    if (!drawer || !icon) return;

    const isOpen = drawer.classList.contains('open');
    drawer.classList.toggle('open', !isOpen);
    icon.className = isOpen ? 'fas fa-chevron-down' : 'fas fa-chevron-up';
}

function applyMobileFilters() {
    const mobileCategory = document.getElementById('mobileCategory');
    const mobileLocation = document.getElementById('mobileLocation');
    const mobileExperience = document.getElementById('mobileExperience');
    const mobileEmploymentType = document.getElementById('mobileEmploymentType');

    const desktopCategory = document.querySelector('.sidebar select[name="category"]');
    const desktopLocation = document.querySelector('.sidebar select[name="location"]');

    if (desktopCategory && mobileCategory) desktopCategory.value = mobileCategory.value;
    if (desktopLocation && mobileLocation) desktopLocation.value = mobileLocation.value;

    const expChecks = document.querySelectorAll('.sidebar input[name="experience_level[]"]');
    expChecks.forEach(function (cb) {
        cb.checked = !!(mobileExperience && mobileExperience.value && cb.value === mobileExperience.value);
    });

    const typeChecks = document.querySelectorAll('.sidebar input[name="employment_type[]"]');
    typeChecks.forEach(function (cb) {
        cb.checked = !!(mobileEmploymentType && mobileEmploymentType.value && cb.value === mobileEmploymentType.value);
    });

    document.getElementById('activeTabInput').value = 'all';
    submitFilters();
}
</script>

<?= view('Layouts/candidate_footer') ?>
