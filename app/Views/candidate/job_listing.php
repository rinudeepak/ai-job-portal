<?= view('Layouts/candidate_header', ['title' => 'Job Listing']) ?>

<!-- Custom CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">

<!-- Job List Area Start -->
<div class="job-listing-area pt-5 pb-120">
    <div class="container">
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-xl-3 col-lg-3 col-md-4">
                <div class="job-filter-sidebar">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-filter"></i> Filter Jobs</h5>
                            <button class="btn btn-sm btn-outline-secondary" onclick="clearAllFilters()">Clear
                                All</button>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="<?= base_url('jobs') ?>" id="filterForm">

                                <!-- Location Filter -->
                                <div class="filter-section mb-4">
                                    <h6><i class="fas fa-map-marker-alt"></i> Location</h6>
                                    <select name="location" class="form-select form-control" onchange="submitFilters()">
                                        <option value="">All Locations</option>
                                        <?php foreach ($locations as $loc): ?>
                                            <option value="<?= esc($loc['location']) ?>"
                                                <?= ($filters['location'] == $loc['location']) ? 'selected' : '' ?>>
                                                <?= esc($loc['location']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remote" value="1"
                                            <?= $filters['remote'] ? 'checked' : '' ?> onchange="submitFilters()">
                                        <label class="form-check-label">Remote Only</label>
                                    </div>
                                </div>

                                <!-- Experience Level -->
                                <div class="filter-section mb-4">
                                    <h6><i class="fas fa-briefcase"></i> Experience Level</h6>
                                    <select name="experience_level" class="form-select form-control"
                                        onchange="submitFilters()">
                                        <option value="">All Levels</option>
                                        <?php foreach ($experienceLevels as $exp): ?>
                                            <option value="<?= esc($exp['experience_level']) ?>"
                                                <?= ($filters['experience_level'] == $exp['experience_level']) ? 'selected' : '' ?>>
                                                <?= esc($exp['experience_level']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Employment Type -->
                                <div class="filter-section mb-4">
                                    <h6><i class="fas fa-clock"></i> Employment Type</h6>
                                    <select name="employment_type" class="form-select form-control"
                                        onchange="submitFilters()">
                                        <option value="">All Types</option>
                                        <?php foreach ($employmentTypes as $emp): ?>
                                            <option value="<?= esc($emp['employment_type']) ?>"
                                                <?= ($filters['employment_type'] == $emp['employment_type']) ? 'selected' : '' ?>>
                                                <?= esc($emp['employment_type']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- Posted Within -->
                                <div class="filter-section mb-4">
                                    <h6><i class="fas fa-calendar"></i> Posted Within</h6>
                                    <select name="posted_within" class="form-select form-control"
                                        onchange="submitFilters()">
                                        <option value="">Any Time</option>
                                        <option value="24h" <?= ($filters['posted_within'] == '24h') ? 'selected' : '' ?>>
                                            Last 24 Hours</option>
                                        <option value="7d" <?= ($filters['posted_within'] == '7d') ? 'selected' : '' ?>>
                                            Last Week</option>
                                        <option value="30d" <?= ($filters['posted_within'] == '30d') ? 'selected' : '' ?>>
                                            Last Month</option>
                                    </select>
                                </div>

                                <!-- Skills Match -->
                                <?php if (session()->get('user_id')): ?>
                                    <div class="filter-section mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="skills_match" value="1"
                                                <?= $filters['skills_match'] ? 'checked' : '' ?> onchange="submitFilters()">
                                            <label class="form-check-label">
                                                <i class="fas fa-code"></i> Match My Skills
                                            </label>
                                        </div>
                                        <small class="text-muted">Show jobs matching your resume skills</small>
                                    </div>
                                <?php endif; ?>

                                <!-- Sort Options -->
                                <div class="filter-section mb-4">
                                    <h6><i class="fas fa-sort"></i> Sort By</h6>
                                    <select name="sort" class="form-select form-control" onchange="submitFilters()">
                                        <option value="newest" <?= ($filters['sort'] == 'newest') ? 'selected' : '' ?>>
                                            Newest First</option>
                                        <option value="relevance" <?= ($filters['sort'] == 'relevance') ? 'selected' : '' ?>>Relevance</option>
                                        <option value="location" <?= ($filters['sort'] == 'location') ? 'selected' : '' ?>>
                                            Location</option>
                                    </select>
                                </div>

                            </form>
                        </div>
                    </div>

                    <!-- Active Filters -->
                    <?php if (array_filter($filters)): ?>
                        <div class="card shadow-sm mt-3">
                            <div class="card-body">
                                <h6><i class="fas fa-tags"></i> Active Filters</h6>
                                <div class="active-filters">
                                    <?php if ($filters['location']): ?>
                                        <span class="badge bg-primary me-1 mb-1">
                                            <?= esc($filters['location']) ?>
                                            <a href="<?= current_url() ?>?<?= http_build_query(array_merge($filters, ['location' => ''])) ?>"
                                                class="text-white ms-1">×</a>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($filters['experience_level']): ?>
                                        <span class="badge bg-success me-1 mb-1">
                                            <?= esc($filters['experience_level']) ?>
                                            <a href="<?= current_url() ?>?<?= http_build_query(array_merge($filters, ['experience_level' => ''])) ?>"
                                                class="text-white ms-1">×</a>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($filters['employment_type']): ?>
                                        <span class="badge bg-info me-1 mb-1">
                                            <?= esc($filters['employment_type']) ?>
                                            <a href="<?= current_url() ?>?<?= http_build_query(array_merge($filters, ['employment_type' => ''])) ?>"
                                                class="text-white ms-1">×</a>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($filters['remote']): ?>
                                        <span class="badge bg-warning me-1 mb-1">
                                            Remote Only
                                            <a href="<?= current_url() ?>?<?= http_build_query(array_merge($filters, ['remote' => ''])) ?>"
                                                class="text-white ms-1">×</a>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($filters['posted_within']): ?>
                                        <span class="badge bg-secondary me-1 mb-1">
                                            <?= $filters['posted_within'] == '24h' ? 'Last 24h' : ($filters['posted_within'] == '7d' ? 'Last Week' : 'Last Month') ?>
                                            <a href="<?= current_url() ?>?<?= http_build_query(array_merge($filters, ['posted_within' => ''])) ?>"
                                                class="text-white ms-1">×</a>
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($filters['skills_match']): ?>
                                        <span class="badge bg-dark me-1 mb-1">
                                            Skills Match
                                            <a href="<?= current_url() ?>?<?= http_build_query(array_merge($filters, ['skills_match' => ''])) ?>"
                                                class="text-white ms-1">×</a>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Job Results -->
            <div class="col-xl-9 col-lg-9 col-md-8">
                <!-- Featured_job_start -->
                <section class="featured-job-area">
                    <div class="container">
                        <!-- Count of Job list Start -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="count-job mb-35 d-flex justify-content-between align-items-center">
                                    <?php if (empty($jobs)): ?>
                                        <span>No jobs found matching your criteria.</span>
                                    <?php else: ?>
                                        <span><strong><?= esc($totalJobs) ?></strong> Jobs found</span>
                                    <?php endif; ?>

                                    <!-- Mobile Filter Toggle -->
                                    <button class="btn btn-outline-primary d-md-none" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#mobileFilters">
                                        <i class="fas fa-filter"></i> Filters
                                    </button>
                                </div>
                            </div>
                        </div>
                        <!-- Count of Job list End -->

                        <?php if (!empty($jobs)): ?>
                            <?php foreach ($jobs as $job): ?>
                                <!-- single-job-content -->
                                <div class="single-job-items mb-30">
                                    <div class="job-items">
                                        <div class="company-img">
                                            <a href="#"><img src="assets/img/icon/job-list1.png" alt=""></a>
                                        </div>
                                        <div class="job-tittle job-tittle2">
                                            <a href="<?= base_url('job/' . $job['id']) ?>">
                                                <h4><?= esc($job['title']) ?></h4>
                                            </a>
                                            <ul>
                                                <li><?= esc($job['company']) ?></li>
                                                <li><i class="fas fa-map-marker-alt"></i><?= esc($job['location']) ?></li>
                                                <li><?= esc($job['experience_level']) ?></li>

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="items-link items-link2 f-right">
                                        <span style="color: inherit; text-decoration: none;"><?= esc($job['employment_type']) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- No Jobs Found -->
                            <div class="text-center py-5">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No jobs found</h5>
                                <p class="text-muted">Try adjusting your filters or search criteria</p>
                                <button class="btn btn-primary" onclick="clearAllFilters()">Clear All Filters</button>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
                <!-- Featured_job_end -->
            </div>
        </div>
    </div>
</div>
<!-- Job List Area End -->
<!--Pagination Start  -->
<?php if (isset($pager) && $pager->getPageCount() > 1): ?>
    <div class="pagination-area pb-115 text-center">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="single-wrap d-flex justify-content-center">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-start">
                                <?php
                                $currentPage = $pager->getCurrentPage();
                                $totalPages = $pager->getPageCount();
                                $uri = $pager->getPageURI(1);
                                $baseUri = preg_replace('/[?&]page=\d+/', '', $uri);
                                $separator = strpos($baseUri, '?') !== false ? '&' : '?';

                                // Previous button
                                if ($currentPage > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= $baseUri . $separator . 'page=' . ($currentPage - 1) ?>">
                                            <span class="ti-angle-left"></span>
                                        </a>
                                    </li>
                                <?php endif;

                                // Page numbers
                                for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= $baseUri . $separator . 'page=' . $i ?>">
                                            <?= sprintf('%02d', $i) ?>
                                        </a>
                                    </li>
                                <?php endfor;

                                // Next button
                                if ($currentPage < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= $baseUri . $separator . 'page=' . ($currentPage + 1) ?>">
                                            <span class="ti-angle-right"></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<!--Pagination End  -->

<!-- Mobile Filters Collapse -->
<div class="collapse d-md-none" id="mobileFilters">
    <div class="container">
        <div class="card">
            <div class="card-body">
                <!-- Mobile filter content (same as sidebar) -->
                <form method="GET" action="<?= base_url('jobs') ?>">
                    <div class="row">
                        <div class="col-6">
                            <label>Location</label>
                            <select name="location" class="form-control">
                                <option value="">All Locations</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?= esc($loc['location']) ?>" <?= ($filters['location'] == $loc['location']) ? 'selected' : '' ?>>
                                        <?= esc($loc['location']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label>Experience</label>
                            <select name="experience_level" class="form-control">
                                <option value="">All Levels</option>
                                <?php foreach ($experienceLevels as $exp): ?>
                                    <option value="<?= esc($exp['experience_level']) ?>"
                                        <?= ($filters['experience_level'] == $exp['experience_level']) ? 'selected' : '' ?>>
                                        <?= esc($exp['experience_level']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Custom JavaScript -->
<script>
    function submitFilters() {
        document.getElementById('filterForm').submit();
    }

    function clearAllFilters() {
        window.location.href = '<?= base_url('jobs') ?>';
    }
</script>

<?= view('layouts/candidate_footer') ?>