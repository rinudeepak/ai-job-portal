
<?= view('Layouts/candidate_header', ['title' => 'Job Listing']) ?>

<!-- Custom CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">

<!-- Job List Area Start -->
<div class="job-listing-area pt-5 pb-120">
    <div class="container">
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-xl-3 col-lg-3 col-md-4">
                <form method="GET" action="<?= base_url('jobs') ?>" id="filterForm">
                    <div class="row">
                        <div class="col-12">
                            <div class="small-section-tittle2 mb-45">
                                <div class="ion"> <svg 
                                    xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink"
                                    width="20px" height="12px">
                                <path fill-rule="evenodd"  fill="rgb(27, 207, 107)"
                                    d="M7.778,12.000 L12.222,12.000 L12.222,10.000 L7.778,10.000 L7.778,12.000 ZM-0.000,-0.000 L-0.000,2.000 L20.000,2.000 L20.000,-0.000 L-0.000,-0.000 ZM3.333,7.000 L16.667,7.000 L16.667,5.000 L3.333,5.000 L3.333,7.000 Z"/>
                                </svg>
                                </div>
                                <h4>Filter Jobs</h4>
                            </div>
                        </div>
                    </div>
                    <!-- Job Category Listing start -->
                    <div class="job-category-listing mb-50">
                        <!-- Job Category -->
                        <?php if (!empty($categories)): ?>
                        <div class="single-listing">
                           <div class="small-section-tittle2">
                                 <h4>Job Category</h4>
                           </div>
                            <div class="select-job-items2">
                                <select name="category" onchange="submitFilters()">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= esc($cat['category']) ?>" <?= ($filters['category'] ?? '') == $cat['category'] ? 'selected' : '' ?>>
                                            <?= esc($cat['category']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Job Type -->
                        <?php if (!empty($employmentTypes)): ?>
                        <div class="single-listing">
                            <div class="select-Categories pt-80 pb-50">
                                <div class="small-section-tittle2">
                                    <h4>Job Type</h4>
                                </div>
                                <?php foreach ($employmentTypes as $type): ?>
                                    <label class="container"><?= esc($type['employment_type']) ?>
                                        <input type="checkbox" name="employment_type[]" value="<?= esc($type['employment_type']) ?>" 
                                               <?= in_array($type['employment_type'], $filters['employment_type'] ?? []) ? 'checked' : '' ?>
                                               onchange="submitFilters()">
                                        <span class="checkmark"></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Job Location -->
                        <?php if (!empty($locations)): ?>
                        <div class="single-listing">
                           <div class="small-section-tittle2">
                                 <h4>Job Location</h4>
                           </div>
                            <div class="select-job-items2">
                                <select name="location" onchange="submitFilters()">
                                    <option value="">All Locations</option>
                                    <?php foreach ($locations as $loc): ?>
                                        <option value="<?= esc($loc['location']) ?>" <?= ($filters['location'] ?? '') == $loc['location'] ? 'selected' : '' ?>>
                                            <?= esc($loc['location']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Experience Level -->
                        <?php if (!empty($experienceLevels)): ?>
                        <div class="single-listing">
                            <div class="select-Categories pt-80 pb-50">
                                <div class="small-section-tittle2">
                                    <h4>Experience</h4>
                                </div>
                                <?php foreach ($experienceLevels as $exp): ?>
                                    <label class="container"><?= esc($exp['experience_level']) ?>
                                        <input type="checkbox" name="experience_level[]" value="<?= esc($exp['experience_level']) ?>" 
                                               <?= in_array($exp['experience_level'], $filters['experience_level'] ?? []) ? 'checked' : '' ?>
                                               onchange="submitFilters()">
                                        <span class="checkmark"></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Posted Within -->
                        <div class="single-listing">
                            <div class="select-Categories pb-50">
                                <div class="small-section-tittle2">
                                    <h4>Posted Within</h4>
                                </div>
                                <label class="container">Any
                                    <input type="radio" name="posted_within" value="" <?= empty($filters['posted_within']) ? 'checked' : '' ?> onchange="submitFilters()">
                                    <span class="checkmark"></span>
                                </label>
                                <label class="container">Today
                                    <input type="radio" name="posted_within" value="1" <?= ($filters['posted_within'] ?? '') == '1' ? 'checked' : '' ?> onchange="submitFilters()">
                                    <span class="checkmark"></span>
                                </label>
                                <label class="container">Last 2 days
                                    <input type="radio" name="posted_within" value="2" <?= ($filters['posted_within'] ?? '') == '2' ? 'checked' : '' ?> onchange="submitFilters()">
                                    <span class="checkmark"></span>
                                </label>
                                <label class="container">Last 3 days
                                    <input type="radio" name="posted_within" value="3" <?= ($filters['posted_within'] ?? '') == '3' ? 'checked' : '' ?> onchange="submitFilters()">
                                    <span class="checkmark"></span>
                                </label>
                                <label class="container">Last 5 days
                                    <input type="radio" name="posted_within" value="5" <?= ($filters['posted_within'] ?? '') == '5' ? 'checked' : '' ?> onchange="submitFilters()">
                                    <span class="checkmark"></span>
                                </label>
                                <label class="container">Last 10 days
                                    <input type="radio" name="posted_within" value="10" <?= ($filters['posted_within'] ?? '') == '10' ? 'checked' : '' ?> onchange="submitFilters()">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Clear Filters Button -->
                        <div class="single-listing">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="clearAllFilters()">
                                Clear All Filters
                            </button>
                        </div>
                    </div>
                    <!-- Job Category Listing End -->
                </form>
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
                <form method="GET" action="<?= base_url('jobs') ?>">
                    <div class="row">
                        <?php if (!empty($categories)): ?>
                        <div class="col-12 mb-3">
                            <label>Category</label>
                            <select name="category" class="form-control">
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
                        <div class="col-6 mb-3">
                            <label>Location</label>
                            <select name="location" class="form-control">
                                <option value="">All Locations</option>
                                <?php foreach ($locations as $loc): ?>
                                    <option value="<?= esc($loc['location']) ?>" <?= ($filters['location'] ?? '') == $loc['location'] ? 'selected' : '' ?>>
                                        <?= esc($loc['location']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($experienceLevels)): ?>
                        <div class="col-6 mb-3">
                            <label>Experience</label>
                            <select name="experience_level" class="form-control">
                                <option value="">All Levels</option>
                                <?php foreach ($experienceLevels as $exp): ?>
                                    <option value="<?= esc($exp['experience_level']) ?>" <?= ($filters['experience_level'] ?? '') == $exp['experience_level'] ? 'selected' : '' ?>>
                                        <?= esc($exp['experience_level']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6">
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </div>
                        <div class="col-6">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="clearAllFilters()">Clear</button>
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
