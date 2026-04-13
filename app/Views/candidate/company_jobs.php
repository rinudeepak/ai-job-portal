<?= view('Layouts/candidate_header', ['title' => $title]) ?>

<div class="jobs-page-jobboard">
    <section class="hero dashboard-hero">
        <div class="container">
            <h1 class="hero-title">
                Jobs at <span class="gradient-text"><?= esc($company_name) ?></span>
            </h1>
            <p class="hero-subtitle">
                Showing <?= $total_jobs ?> open positions
            </p>
        </div>
    </section>

    <div class="container dashboard-layout" style="padding: 40px 0;">
        <div class="dashboard-main">
            <?php if ($total_jobs === 0): ?>
                <div class="empty-state">
                    <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                    <h5>No jobs found</h5>
                    <p class="text-muted">There are currently no open positions at <?= esc($company_name) ?></p>
                    <a href="<?= base_url('jobs') ?>" class="btn btn-primary mt-3">Browse all jobs</a>
                </div>
            <?php else: ?>
                <!-- Internal Jobs -->
                <?php if (!empty($internal_jobs)): ?>
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-4">
                            <h3 class="mb-0">Our Postings</h3>
                            <span class="badge badge-primary ml-2"><?= count($internal_jobs) ?></span>
                        </div>
                        <div class="job-listings">
                            <?php foreach ($internal_jobs as $job): ?>
                                <div class="job-listing smart-job-item">
                                    <div class="job-listing-logo">
                                        <?php if (!empty($company['logo'])): ?>
                                            <img src="<?= esc($company['logo']) ?>" alt="<?= esc($company_name) ?>" class="smart-job-logo-img">
                                        <?php else: ?>
                                            <div class="smart-job-logo">
                                                <?= strtoupper(substr($company_name, 0, 1)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="job-listing-content">
                                        <div class="job-listing-position">
                                            <h2>
                                                <a href="<?= base_url('job/' . $job['id']) ?>" class="job-title-link">
                                                    <?= esc($job['title']) ?>
                                                </a>
                                            </h2>
                                            <p class="smart-job-extra">
                                                <i class="fas fa-map-marker-alt"></i> <?= esc($job['location'] ?? 'N/A') ?>
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($job['posted_at'])) ?>
                                            </p>
                                        </div>
                                        <div class="job-listing-about">
                                            <p><?= substr(strip_tags($job['description'] ?? ''), 0, 150) ?>...</p>
                                        </div>
                                        <div class="job-tags">
                                            <span class="jtag type"><?= esc($job['employment_type'] ?? 'Full-time') ?></span>
                                            <span class="jtag"><?= esc($job['experience_level'] ?? 'Mid-level') ?></span>
                                        </div>
                                    </div>
                                    <div class="smart-job-meta">
                                        <a href="<?= base_url('job/' . $job['id']) ?>" class="smart-view-link">
                                            View Details →
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- External Jobs from Indeed -->
                <?php if (!empty($external_jobs)): ?>
                    <div class="mb-5">
                        <div class="d-flex align-items-center mb-4">
                            <h3 class="mb-0">Similar Positions</h3>
                            <span class="badge badge-secondary ml-2"><?= count($external_jobs) ?></span>
                            <small class="text-muted ml-2">(from Indeed)</small>
                        </div>
                        <div class="job-listings">
                            <?php foreach ($external_jobs as $job): ?>
                                <div class="job-listing smart-job-item">
                                    <div class="job-listing-logo">
                                        <div class="smart-job-logo">
                                            <?= strtoupper(substr($job['company'], 0, 1)) ?>
                                        </div>
                                    </div>
                                    <div class="job-listing-content">
                                        <div class="job-listing-position">
                                            <h2>
                                                <a href="<?= esc($job['url']) ?>" target="_blank" class="job-title-link">
                                                    <?= esc($job['title']) ?>
                                                </a>
                                            </h2>
                                            <p class="smart-job-extra">
                                                <i class="fas fa-map-marker-alt"></i> <?= esc($job['location']) ?>
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-calendar"></i> <?= esc($job['posted_date']) ?>
                                            </p>
                                        </div>
                                        <div class="job-listing-about">
                                            <p><?= substr($job['summary'], 0, 150) ?>...</p>
                                        </div>
                                        <div class="job-tags">
                                            <span class="jtag type"><?= esc($job['job_type']) ?></span>
                                            <span class="jtag">Indeed</span>
                                        </div>
                                    </div>
                                    <div class="smart-job-meta">
                                        <a href="<?= esc($job['url']) ?>" target="_blank" class="smart-view-link">
                                            View on Indeed →
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="mt-5 text-center">
                <a href="<?= base_url('jobs') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left"></i> Back to all jobs
                </a>
            </div>
        </div>
    </div>
</div>

<?= view('Layouts/candidate_footer') ?>
