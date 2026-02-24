<?= view('Layouts/candidate_header', ['title' => 'Saved Jobs']) ?>
<?php $jobs = $jobs ?? []; ?>

<div class="jobs-page-jobboard">
<section class="section-hero overlay inner-page bg-image jobs-hero" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h1 class="text-white font-weight-bold">Saved Jobs</h1>
                <div class="custom-breadcrumbs">
                    <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                    <span class="mx-2 slash">/</span>
                    <a href="<?= base_url('jobs') ?>">Jobs</a>
                    <span class="mx-2 slash">/</span>
                    <span class="text-white"><strong>Saved Jobs</strong></span>
                </div>
                <p>Jobs you bookmarked for later.</p>
            </div>
        </div>
    </div>
</section>

<section class="site-section pt-0">
    <div class="container">
        <div class="jobs-layout" style="display:block;">
            <div class="jobs-main">
                <?php if (!empty($jobs)): ?>
                    <ul class="job-listings mb-4">
                        <?php foreach ($jobs as $job): ?>
                            <?php $initial = strtoupper(substr($job['company'] ?? 'J', 0, 1)); ?>
                            <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center smart-job-item">
                                <a href="<?= base_url('job/' . $job['id']) ?>"></a>
                                <div class="job-listing-logo">
                                    <div class="smart-job-logo">
                                        <?php if (!empty($job['company_logo'])): ?>
                                            <img src="<?= base_url($job['company_logo']) ?>" alt="<?= esc($job['company']) ?>" class="smart-job-logo-img">
                                        <?php else: ?>
                                            <?= $initial ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
                                    <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
                                        <h2><?= esc($job['title']) ?></h2>
                                        <strong><?= esc($job['company']) ?></strong>
                                        <div class="smart-job-extra mt-2">
                                            <span><i class="fas fa-layer-group"></i> <?= esc($job['experience_level']) ?></span>
                                        </div>
                                    </div>
                                    <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
                                        <span class="icon-room"></span> <?= esc($job['location']) ?>
                                    </div>
                                    <div class="job-listing-meta smart-job-meta">
                                        <span class="badge badge-success"><?= esc($job['employment_type'] ?: 'Full Time') ?></span>
                                        <a class="smart-view-link" href="<?= base_url('job/unsave/' . $job['id']) ?>">Remove</a>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-bookmark"></i>
                        <h5>No saved jobs yet</h5>
                        <p>Save jobs from listings and they will appear here.</p>
                        <a href="<?= base_url('jobs') ?>" style="display:inline-block;margin-top:12px;background:var(--ink);color:white;padding:10px 24px;border-radius:8px;text-decoration:none;font-family:'Syne',sans-serif;font-weight:700;">Browse Jobs</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
</div>

<?= view('Layouts/candidate_footer') ?>

