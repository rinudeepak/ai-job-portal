<?= view('Layouts/candidate_header', ['title' => 'Company Profile']) ?>

<div class="job-details-jobboard">
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1 class="text-white font-weight-bold"><?= esc($company['name'] ?? 'Company Profile') ?></h1>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                        <span class="mx-2 slash">/</span>
                        <a href="<?= base_url('jobs') ?>">Jobs</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong>Company Profile</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section content-wrap">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="mr-3">
                                <?php if (!empty($company['logo'])): ?>
                                    <img src="<?= base_url($company['logo']) ?>" alt="Company Logo" style="width:72px;height:72px;object-fit:cover;border-radius:10px;border:1px solid #ddd;">
                                <?php else: ?>
                                    <div style="width:72px;height:72px;border-radius:10px;background:#1f2937;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;">
                                        <?= esc(strtoupper(substr((string) ($company['name'] ?? 'C'), 0, 1))) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div>
                                <h3 class="mb-1"><?= esc($company['name'] ?? 'Company') ?></h3>
                                <?php if (!empty($company['short_description'])): ?>
                                    <p class="text-muted mb-0"><?= esc($company['short_description']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2"><strong>Industry:</strong> <?= esc($company['industry'] ?? 'Not specified') ?></div>
                            <div class="col-md-6 mb-2"><strong>Company Size:</strong> <?= esc($company['size'] ?? 'Not specified') ?></div>
                            <div class="col-md-6 mb-2"><strong>HQ:</strong> <?= esc($company['hq'] ?? 'Not specified') ?></div>
                            <div class="col-md-6 mb-2"><strong>Branches:</strong> <?= esc($company['branches'] ?? 'Not specified') ?></div>
                            <div class="col-md-12 mb-2">
                                <strong>Website:</strong>
                                <?php if (!empty($company['website'])): ?>
                                    <a href="<?= esc($company['website']) ?>" target="_blank" rel="noopener"><?= esc($company['website']) ?></a>
                                <?php else: ?>
                                    <span>Not specified</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5>About Company</h5>
                        <p class="mb-0"><?= nl2br(esc($company['what_we_do'] ?? 'No description available.')) ?></p>
                    </div>
                </div>

                <?php if (!empty($company['mission_values'])): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5>Mission / Values</h5>
                            <p class="mb-0"><?= nl2br(esc($company['mission_values'])) ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($openJobs)): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5>Open Jobs (<?= (int) $openJobsCount ?>)</h5>
                            <ul class="list-unstyled mb-0">
                                <?php foreach ($openJobs as $job): ?>
                                    <li class="mb-2">
                                        <a href="<?= base_url('job/' . $job['id']) ?>"><?= esc($job['title']) ?></a>
                                        <span class="text-muted">- <?= esc($job['location']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Contact</h5>
                        <?php if ((int) ($company['contact_public'] ?? 0) === 1): ?>
                            <p class="mb-2"><strong>Email:</strong> <?= esc($company['contact_email'] ?: 'Not specified') ?></p>
                            <p class="mb-0"><strong>Phone:</strong> <?= esc($company['contact_phone'] ?: 'Not specified') ?></p>
                        <?php else: ?>
                            <p class="mb-0 text-muted">Contact info is private.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<?= view('Layouts/candidate_footer') ?>
