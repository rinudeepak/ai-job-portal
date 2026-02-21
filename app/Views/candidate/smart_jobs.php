<?= view('Layouts/candidate_header', ['title' => 'Find Jobs']) ?>
<?php
// Safe defaults — prevents undefined variable errors if old controller is used
$activeTab          = $activeTab          ?? 'all';
$filters            = $filters            ?? [];
$suggestedJobs      = $suggestedJobs      ?? [];
$candidateSkills    = $candidateSkills    ?? [];
$candidateInterests = $candidateInterests ?? [];
$behavior           = $behavior           ?? [];
$useAi              = $useAi              ?? false;
$totalJobs          = $totalJobs          ?? 0;
$jobs               = $jobs               ?? [];
$locations          = $locations          ?? [];
$categories         = $categories         ?? [];
$experienceLevels   = $experienceLevels   ?? [];
$employmentTypes    = $employmentTypes    ?? [];
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=Syne:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- ── SEARCH HERO ── -->
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
                <p>Search across all jobs. Filter by skills, type, and location. Get AI-matched suggestions.</p>
                <div class="search-wrap">
                    <input type="text" id="searchInput"
                           value="<?= esc($filters['search'] ?? '') ?>"
                           placeholder="Job title, skills, company... e.g. React developer remote"
                           onkeydown="if(event.key==='Enter'){doSearch();}">
                    <button type="button" class="search-btn" onclick="doSearch()">
                        <i class="fas fa-search"></i>
                        <span>Search</span>
                    </button>
                </div>
                <div class="keyword-pills">
                    <?php foreach (['PHP Developer','React','Remote','Full Stack','Data Analyst','DevOps','Python','Java'] as $kw): ?>
                        <span class="kpill" onclick="setSearch('<?= esc($kw) ?>')"><?= esc($kw) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="site-section pt-0">
<div class="container">
<!-- Single unified form — search input + all sidebar filters share this form -->
<form method="GET" action="<?= base_url('jobs') ?>" id="filterForm">
    <input type="hidden" name="search" id="hiddenSearch" value="<?= esc($filters['search'] ?? '') ?>">
    <input type="hidden" name="tab"    id="activeTabInput" value="<?= esc($activeTab) ?>">

    <div class="jobs-layout">

        <!-- ── SIDEBAR (desktop) ── -->
        <div class="sidebar">
            <div class="sidebar-head">
                <h5><i class="fas fa-sliders-h"></i> Filters</h5>
                <a href="<?= base_url('jobs?tab=' . esc($activeTab)) ?>" class="clear-link">Clear all</a>
            </div>

            <!-- Category -->
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

            <!-- Location -->
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

            <!-- Employment Type -->
            <?php if (!empty($employmentTypes)): ?>
            <div class="filter-section">
                <span class="filter-label">Job Type</span>
                <?php foreach ($employmentTypes as $type): ?>
                    <label class="check-item">
                        <input type="checkbox" name="employment_type[]" value="<?= esc($type['employment_type']) ?>"
                               <?= in_array($type['employment_type'], (array)($filters['employment_type'] ?? [])) ? 'checked' : '' ?>
                               onchange="submitFilters()">
                        <span class="check-box"></span>
                        <span class="check-text"><?= esc($type['employment_type']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Experience Level -->
            <?php if (!empty($experienceLevels)): ?>
            <div class="filter-section">
                <span class="filter-label">Experience</span>
                <?php foreach ($experienceLevels as $exp): ?>
                    <label class="check-item">
                        <input type="checkbox" name="experience_level[]" value="<?= esc($exp['experience_level']) ?>"
                               <?= in_array($exp['experience_level'], (array)($filters['experience_level'] ?? [])) ? 'checked' : '' ?>
                               onchange="submitFilters()">
                        <span class="check-box"></span>
                        <span class="check-text"><?= esc($exp['experience_level']) ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Posted Within -->
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

        <!-- ── MAIN CONTENT ── -->
        <div class="jobs-main">

            <!-- Mobile filter toggle -->
            <button type="button" class="mobile-filter-toggle" onclick="toggleMobileFilters()">
                <span><i class="fas fa-sliders-h" style="margin-right:8px"></i>Filters</span>
                <i class="fas fa-chevron-down" id="mobileFilterIcon"></i>
            </button>

            <!-- Mobile filter drawer -->
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
                                    <option value="<?= esc($exp['experience_level']) ?>" <?= in_array($exp['experience_level'], (array)($filters['experience_level'] ?? []), true) ? 'selected' : '' ?>>
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
                                    <option value="<?= esc($type['employment_type']) ?>" <?= in_array($type['employment_type'], (array)($filters['employment_type'] ?? []), true) ? 'selected' : '' ?>>
                                        <?= esc($type['employment_type']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" onclick="applyMobileFilters()" style="flex:1;background:var(--ink);color:white;border:none;border-radius:8px;padding:10px;font-family:'Syne',sans-serif;font-weight:700;cursor:pointer;">Apply</button>
                        <a href="<?= base_url('jobs?tab=' . esc($activeTab)) ?>" style="flex:1;background:var(--smoke);color:var(--ink);border:1.5px solid var(--border);border-radius:8px;padding:10px;font-family:'Syne',sans-serif;font-weight:700;text-align:center;text-decoration:none;display:flex;align-items:center;justify-content:center;">Clear</a>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="tabs-row">
                <div class="tab-pills">
                    <button type="button" class="tab-pill <?= $activeTab === 'all' ? 'active' : '' ?>"
                            onclick="switchTab('all', event)">
                        <i class="fas fa-briefcase"></i> All Jobs
                        <span class="pill-count"><?= $totalJobs ?></span>
                    </button>
                    <button type="button" class="tab-pill <?= $activeTab === 'suggested' ? 'active' : '' ?>"
                            onclick="switchTab('suggested', event)">
                        <i class="fas fa-star"></i> For You
                        <span class="pill-count"><?= count($suggestedJobs) ?></span>
                    </button>
                </div>
            </div>

            <!-- Career transition alert -->
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


            <!-- ════ ALL JOBS TAB ════ -->
            <div id="tab-all" class="<?= $activeTab !== 'all' ? 'd-none' : '' ?>">

                <div class="results-bar">
                    <span class="results-count">
                        <?php if (!empty($filters['search'])): ?>
                            Results for <strong>"<?= esc($filters['search']) ?>"</strong> ·
                        <?php endif; ?>
                        <strong><?= $totalJobs ?></strong> job<?= $totalJobs != 1 ? 's' : '' ?> found
                    </span>
                </div>

                <?php if (!empty($jobs)): ?>
                    <ul class="job-listings mb-4">
                    <?php foreach ($jobs as $job): ?>
                        <?php
                            $score   = $job['match_score'] ?? 0;
                            $cls     = $score >= 70 ? 'high' : ($score >= 40 ? 'mid' : 'low');
                            $initial = strtoupper(substr($job['company'] ?? 'J', 0, 1));
                            $companyRefId = (int) ($job['company_id'] ?? 0);
                            if ($companyRefId <= 0) {
                                $companyRefId = (int) ($job['recruiter_id'] ?? 0);
                            }
                            $companyProfileUrl = $companyRefId > 0 ? base_url('company/' . $companyRefId) : '#';
                        ?>
                        <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center smart-job-item">
                            <div class="job-listing-logo">
                                <a href="<?= esc($companyProfileUrl) ?>" title="View company profile">
                                    <div class="smart-job-logo">
                                        <?php if (!empty($job['company_logo'])): ?>
                                            <img src="<?= base_url($job['company_logo']) ?>" alt="<?= esc($job['company']) ?>" class="smart-job-logo-img">
                                        <?php else: ?>
                                            <?= $initial ?>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>
                            <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
                                <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
                                    <h2><a class="job-title-link" href="<?= base_url('job/' . $job['id']) ?>"><?= esc($job['title']) ?></a></h2>
                                    <strong>
                                        <a href="<?= esc($companyProfileUrl) ?>"><?= esc($job['company']) ?></a>
                                    </strong>
                                    <div class="smart-job-extra mt-2">
                                        <span><i class="fas fa-layer-group"></i> <?= esc($job['experience_level']) ?></span>
                                    </div>
                                    <div class="job-tags mt-2">
                                        <?php
                                            $skills = array_slice(array_map('trim', explode(',', $job['required_skills'] ?? '')), 0, 4);
                                            foreach ($skills as $sk):
                                                if (trim($sk)):
                                        ?>
                                            <span class="jtag"><?= esc(trim($sk)) ?></span>
                                        <?php endif; endforeach; ?>
                                    </div>
                                </div>
                                <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
                                    <span class="icon-room"></span> <?= esc($job['location']) ?>
                                </div>
                                <div class="job-listing-meta smart-job-meta">
                                    <?php
                                        $type = strtolower((string) ($job['employment_type'] ?? ''));
                                        $typeBadge = str_contains($type, 'part') ? 'badge-danger' : 'badge-success';
                                    ?>
                                    <span class="badge <?= $typeBadge ?>"><?= esc($job['employment_type'] ?: 'Full Time') ?></span>
                                    <?php if ($score > 0): ?>
                                        <small class="smart-match-text"><?= round($score) ?>% match</small>
                                    <?php endif; ?>
                                    <a class="smart-view-link" href="<?= base_url('job/' . $job['id']) ?>">
                                        View Job <i class="fas fa-arrow-right"></i>
                                    </a>
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


            <!-- ════ FOR YOU TAB ════ -->
            <div id="tab-suggested" class="<?= $activeTab !== 'suggested' ? 'd-none' : '' ?>">

                <!-- Profile strip -->
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
                    <?php if (!empty($behavior['top_categories'])): ?>
                    <div class="profile-strip-section">
                        <div class="strip-label">Your Pattern</div>
                        <?php foreach (array_slice($behavior['top_categories'], 0, 3) as $cat): ?>
                            <span class="pattern-chip"><?= esc($cat['category']) ?></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <div>
                        <?php if ($useAi): ?>
                            <span style="font-size:.75rem;background:#f0fdf4;border:1px solid #bbf7d0;color:var(--green2);border-radius:999px;padding:4px 12px;font-weight:600;">
                                <i class="fas fa-robot"></i> AI Enhanced
                            </span>
                        <?php else: ?>
                            <a href="<?= base_url('jobs?tab=suggested&ai=1') ?>" style="font-size:.75rem;background:var(--smoke);border:1px solid var(--border);color:var(--slate);border-radius:999px;padding:4px 12px;font-weight:600;text-decoration:none;">
                                <i class="fas fa-robot"></i> Enable AI
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($suggestedJobs)): ?>
                    <div class="results-bar">
                        <span class="results-count"><strong><?= count($suggestedJobs) ?></strong> jobs matched for you</span>
                    </div>

                    <ul class="job-listings mb-4">
                    <?php foreach ($suggestedJobs as $index => $job): ?>
                        <?php
                            $score   = $job['match_score'] ?? 0;
                            $cls     = $score >= 70 ? 'high' : ($score >= 40 ? 'mid' : 'low');
                            $isTop   = $index < 3;
                            $initial = strtoupper(substr($job['company'] ?? 'J', 0, 1));
                            $companyRefId = (int) ($job['company_id'] ?? 0);
                            if ($companyRefId <= 0) {
                                $companyRefId = (int) ($job['recruiter_id'] ?? 0);
                            }
                            $companyProfileUrl = $companyRefId > 0 ? base_url('company/' . $companyRefId) : '#';
                        ?>
                        <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center smart-job-item <?= $isTop ? 'top-match' : '' ?>">
                            <?php if ($isTop): ?><div class="top-badge">Top Match</div><?php endif; ?>
                            <div class="job-listing-logo">
                                <a href="<?= esc($companyProfileUrl) ?>" title="View company profile">
                                    <div class="smart-job-logo">
                                        <?php if (!empty($job['company_logo'])): ?>
                                            <img src="<?= base_url($job['company_logo']) ?>" alt="<?= esc($job['company']) ?>" class="smart-job-logo-img">
                                        <?php else: ?>
                                            <?= $initial ?>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>
                            <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
                                <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
                                    <h2><a class="job-title-link" href="<?= base_url('job/' . $job['id']) ?>"><?= esc($job['title']) ?></a></h2>
                                    <strong>
                                        <a href="<?= esc($companyProfileUrl) ?>"><?= esc($job['company']) ?></a>
                                    </strong>
                                    <div class="smart-job-extra mt-2">
                                        <span><i class="fas fa-layer-group"></i> <?= esc($job['experience_level']) ?></span>
                                    </div>
                                    <div class="job-tags mt-2">
                                        <?php
                                            $skills = array_slice(array_map('trim', explode(',', $job['required_skills'] ?? '')), 0, 4);
                                            foreach ($skills as $sk):
                                                if (trim($sk)):
                                        ?>
                                            <span class="jtag"><?= esc(trim($sk)) ?></span>
                                        <?php endif; endforeach; ?>
                                    </div>
                                    <?php if (!empty($job['match_reason'])): ?>
                                    <div class="ai-reason mt-2"><i class="fas fa-lightbulb"></i> <?= esc($job['match_reason']) ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
                                    <span class="icon-room"></span> <?= esc($job['location']) ?>
                                </div>
                                <div class="job-listing-meta smart-job-meta">
                                    <?php
                                        $type = strtolower((string) ($job['employment_type'] ?? ''));
                                        $typeBadge = str_contains($type, 'part') ? 'badge-danger' : 'badge-success';
                                    ?>
                                    <span class="badge <?= $typeBadge ?>"><?= esc($job['employment_type'] ?: 'Full Time') ?></span>
                                    <small class="smart-match-text"><?= round($score) ?>% match</small>
                                    <a class="smart-view-link" href="<?= base_url('job/' . $job['id']) ?>">
                                        View Job <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-star"></i>
                        <h5>No suggestions yet</h5>
                        <p>Add skills and interests to your profile to get personalized job matches</p>
                        <a href="<?= base_url('candidate/profile') ?>" style="display:inline-block;margin-top:12px;background:var(--green);color:var(--ink);padding:10px 24px;border-radius:8px;text-decoration:none;font-family:'Syne',sans-serif;font-weight:700;">Update Profile</a>
                    </div>
                <?php endif; ?>
            </div>

        </div><!-- /jobs-main -->
    </div><!-- /jobs-layout -->
</form>
</div><!-- /container -->
</section>
</div>

<script>
function resetLeftFilterOptions() {
    const form = document.getElementById('filterForm');
    if (!form) return;

    form.querySelectorAll('select[name="category"], select[name="location"]').forEach(function (el) {
        el.value = '';
    });

    form.querySelectorAll('input[name="employment_type[]"], input[name="experience_level[]"]').forEach(function (el) {
        el.checked = false;
    });

    const postedAny = form.querySelector('input[name="posted_within"][value=""]');
    if (postedAny) postedAny.checked = true;

    const mobileCategory = document.getElementById('mobileCategory');
    const mobileLocation = document.getElementById('mobileLocation');
    const mobileExperience = document.getElementById('mobileExperience');
    const mobileEmploymentType = document.getElementById('mobileEmploymentType');
    if (mobileCategory) mobileCategory.value = '';
    if (mobileLocation) mobileLocation.value = '';
    if (mobileExperience) mobileExperience.value = '';
    if (mobileEmploymentType) mobileEmploymentType.value = '';
}

function resetAllJobsFilters() {
    const form = document.getElementById('filterForm');
    if (!form) return;

    const searchInput = document.getElementById('searchInput');
    const hiddenSearch = document.getElementById('hiddenSearch');
    if (searchInput) searchInput.value = '';
    if (hiddenSearch) hiddenSearch.value = '';

    form.querySelectorAll('select[name="category"], select[name="location"]').forEach(function (el) {
        el.value = '';
    });

    form.querySelectorAll('input[name="employment_type[]"], input[name="experience_level[]"]').forEach(function (el) {
        el.checked = false;
    });

    const postedAny = form.querySelector('input[name="posted_within"][value=""]');
    if (postedAny) postedAny.checked = true;
}

function switchTab(tab, e) {
    if (e) e.preventDefault();

    const jobsUrl = '<?= base_url('jobs') ?>';
    const url = new URL(window.location.href);
    const aiParam = url.searchParams.get('ai');

    if (tab === 'suggested') {
        // Suggested tab should not carry search/filter state from All Jobs.
        resetAllJobsFilters();
        const nextUrl = new URL(jobsUrl);
        nextUrl.searchParams.set('tab', 'suggested');
        if (aiParam) nextUrl.searchParams.set('ai', aiParam);
        window.location.href = nextUrl.toString();
        return;
    }

    // Returning to All tab should open clean All Jobs list by default.
    const nextUrl = new URL(jobsUrl);
    nextUrl.searchParams.set('tab', 'all');
    if (aiParam) nextUrl.searchParams.set('ai', aiParam);
    window.location.href = nextUrl.toString();
}

function submitFilters() {
    // Filters are for All Jobs tab only.
    document.getElementById('activeTabInput').value = 'all';

    // When using left filters, clear search bar.
    const searchBar = document.getElementById('searchInput');
    const hiddenSearch = document.querySelector('#filterForm input[name="search"]');
    if (searchBar) searchBar.value = '';
    if (hiddenSearch) hiddenSearch.value = '';

    document.getElementById('filterForm').submit();
}

function toggleMobileFilters() {
    const drawer = document.getElementById('mobileFilterDrawer');
    const icon   = document.getElementById('mobileFilterIcon');
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

function doSearch() {
    // When using top search, clear left filters.
    resetLeftFilterOptions();

    // Copy visible search input into hidden field, force All Jobs tab, then submit.
    document.getElementById('hiddenSearch').value = document.getElementById('searchInput').value;
    document.getElementById('activeTabInput').value = 'all';
    document.getElementById('filterForm').submit();
}

function setSearch(kw) {
    document.getElementById('searchInput').value = kw;
    doSearch();
}
</script>

<?= view('Layouts/candidate_footer') ?>


