<?= view('Layouts/candidate_header', ['title' => 'Companies']) ?>
<?php
$totalCompanies = (int) ($totalCompanies ?? 0);
$totalOpenJobs = (int) ($totalOpenJobs ?? 0);
$filters = $filters ?? [];
?>

<div class="job-details-jobboard companies-directory-page companies-directory-jobboard">
    <div class="container">
        <div class="company-profile-header">
            <div class="page-board-copy">
                <span class="page-board-kicker"><i class="fas fa-building"></i> Employer directory</span>
                <h1 class="page-board-title">Explore Companies</h1>
                <p class="page-board-subtitle">Search employers, compare company profiles, and discover open roles.</p>
                <div class="company-profile-meta">
                    <span class="meta-chip"><strong><?= number_format($totalCompanies) ?></strong> Companies</span>
                    <span class="meta-chip"><strong><?= number_format($totalOpenJobs) ?></strong> Open Jobs</span>
                </div>
            </div>
            <div class="company-profile-actions">
                <a href="#companies-filter" class="btn btn-primary">
                    <i class="fas fa-search mr-1"></i> Search Companies
                </a>
                <a href="<?= base_url('jobs') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-briefcase mr-1"></i> Browse Jobs
                </a>
            </div>
        </div>
    </div>

    <section class="site-section pt-0 content-wrap">
        <div class="container">
            <div class="detail-card companies-filter-card" id="companies-filter">
                <form method="get" action="<?= base_url('companies') ?>">
                    <div class="row g-3">
                        <div class="col-lg-5 col-md-6 mb-3">
                            <label class="small text-muted text-uppercase font-weight-bold">Search Company</label>
                            <input type="text" name="q" class="form-control" value="<?= esc($filters['q'] ?? '') ?>" placeholder="Company name or keyword">
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3">
                            <label class="small text-muted text-uppercase font-weight-bold">Industry</label>
                            <select name="industry" class="form-control">
                                <option value="">All industries</option>
                                <?php foreach (($industries ?? []) as $industryOption): ?>
                                    <option value="<?= esc($industryOption) ?>" <?= ($filters['industry'] ?? '') === $industryOption ? 'selected' : '' ?>>
                                        <?= esc($industryOption) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6 mb-3">
                            <label class="small text-muted text-uppercase font-weight-bold">Location</label>
                            <input type="text" name="location" class="form-control" value="<?= esc($filters['location'] ?? '') ?>" placeholder="City or HQ">
                        </div>
                        <div class="col-lg-2 col-md-6 mb-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary btn-block mr-2">Search</button>
                            <a href="<?= base_url('companies') ?>" class="btn btn-outline-secondary btn-block mt-0">Clear</a>
                        </div>
                    </div>
                </form>
            </div>

            <?php if (empty($companies)): ?>
                <div class="empty-state">
                    <i class="fas fa-building"></i>
                    <h5>No companies found</h5>
                    <p>Try a different company name, location, or industry filter.</p>
                </div>
            <?php else: ?>
                <div class="companies-directory-grid company-directory-grid mb-4">
                    <?php foreach ($companies as $company): ?>
                        <?php
                            $companyName = (string) ($company['name'] ?? 'Company');
                            $companyInitial = strtoupper(substr($companyName, 0, 1) ?: 'C');
                            $companyDescription = trim((string) ($company['short_description'] ?? ''));
                            if ($companyDescription === '') {
                                $companyDescription = trim((string) ($company['what_we_do'] ?? ''));
                            }
                        ?>
                        <article class="job-card company-directory-card">
                            <div class="job-card-icon company-directory-logo">
                                <?php if (!empty($company['logo'])): ?>
                                    <img src="<?= base_url($company['logo']) ?>" alt="<?= esc($companyName) ?>">
                                <?php else: ?>
                                    <span><?= esc($companyInitial) ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 class="job-card-title">
                                <a href="<?= base_url('company/' . (int) $company['id']) ?>"><?= esc($companyName) ?></a>
                            </h3>
                            <p class="job-card-company"><?= esc($company['industry'] ?: 'Industry not specified') ?></p>
                            <div class="job-card-meta company-directory-meta">
                                <span><i class="fas fa-map-pin"></i> <?= esc($company['hq'] ?: 'HQ not specified') ?></span>
                                <span><i class="fas fa-briefcase"></i> <?= (int) ($company['open_jobs_count'] ?? 0) ?> open jobs</span>
                            </div>
                            <?php if ($companyDescription !== ''): ?>
                                <p class="company-directory-description"><?= esc($companyDescription) ?></p>
                            <?php endif; ?>
                            <div class="job-card-tags company-directory-tags">
                                <span class="badge badge-primary"><?= esc($company['size'] ?: 'Size not specified') ?></span>
                            </div>
                            <div class="company-directory-actions">
                                <a href="<?= base_url('company/' . (int) $company['id']) ?>" class="view-details">View Company &rarr;</a>
                                <?php if ((int) ($company['open_jobs_count'] ?? 0) > 0): ?>
                                    <a href="<?= base_url('jobs?company=' . urlencode($companyName)) ?>" class="company-directory-jobs-link">See jobs</a>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>

                <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
                    <div class="row pagination-wrap mt-4">
                        <div class="col-md-6 text-center text-md-left mb-3 mb-md-0">
                            <span>Showing page <?= $pager->getCurrentPage() ?> of <?= $pager->getPageCount() ?></span>
                        </div>
                        <div class="col-md-6 text-center text-md-right">
                            <div class="custom-pagination ml-auto">
                                <?php
                                $cur = $pager->getCurrentPage();
                                $total = $pager->getPageCount();
                                $base = preg_replace('/[?&]page=\d+/', '', current_url(true)->__toString());
                                $sep = strpos($base, '?') !== false ? '&' : '?';
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
            <?php endif; ?>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
