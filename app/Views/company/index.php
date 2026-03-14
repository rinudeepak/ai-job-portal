<?= view('Layouts/candidate_header', ['title' => 'Companies']) ?>

<div class="job-details-jobboard companies-directory-page">
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h1 class="text-white font-weight-bold">Explore Companies</h1>
                    <p class="text-white-50 mb-3">Search employers, compare company profiles, and discover open roles.</p>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong>Companies</strong></span>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="companies-directory-hero-stats">
                        <div class="companies-directory-stat">
                            <strong><?= number_format((int) ($totalCompanies ?? 0)) ?></strong>
                            <span>Companies</span>
                        </div>
                        <div class="companies-directory-stat">
                            <strong><?= number_format((int) ($totalOpenJobs ?? 0)) ?></strong>
                            <span>Open Jobs</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section content-wrap">
        <div class="container">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <form method="get" action="<?= base_url('companies') ?>">
                        <div class="row">
                            <div class="col-md-5 mb-3">
                                <label class="small text-muted text-uppercase font-weight-bold">Search Company</label>
                                <input type="text" name="q" class="form-control" value="<?= esc($filters['q'] ?? '') ?>" placeholder="Company name or keyword">
                            </div>
                            <div class="col-md-3 mb-3">
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
                            <div class="col-md-2 mb-3">
                                <label class="small text-muted text-uppercase font-weight-bold">Location</label>
                                <input type="text" name="location" class="form-control" value="<?= esc($filters['location'] ?? '') ?>" placeholder="City or HQ">
                            </div>
                            <div class="col-md-2 mb-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-block mr-2">Search</button>
                                <a href="<?= base_url('companies') ?>" class="btn btn-outline-secondary btn-block mt-0">Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (empty($companies)): ?>
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center py-5">
                        <h4 class="mb-2">No companies found</h4>
                        <p class="text-muted mb-0">Try a different company name, location, or industry filter.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="companies-directory-grid">
                    <?php foreach ($companies as $company): ?>
                        <article class="companies-directory-card">
                            <div class="companies-directory-card-top">
                                <div class="companies-directory-logo">
                                    <?php if (!empty($company['logo'])): ?>
                                        <img src="<?= base_url($company['logo']) ?>" alt="<?= esc($company['name'] ?? 'Company') ?>">
                                    <?php else: ?>
                                        <span><?= esc(strtoupper(substr((string) ($company['name'] ?? 'C'), 0, 1))) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h3 class="companies-directory-title">
                                        <a href="<?= base_url('company/' . (int) $company['id']) ?>"><?= esc($company['name'] ?? 'Company') ?></a>
                                    </h3>
                                    <p class="companies-directory-meta mb-0">
                                        <?= esc($company['industry'] ?: 'Industry not specified') ?>
                                        <?php if (!empty($company['hq'])): ?>
                                            <span class="mx-1">|</span><?= esc($company['hq']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>

                            <?php
                            $companyDescription = trim((string) ($company['short_description'] ?? ''));
                            if ($companyDescription === '') {
                                $companyDescription = trim((string) ($company['what_we_do'] ?? ''));
                            }
                            ?>
                            <?php if ($companyDescription !== ''): ?>
                                <p class="companies-directory-description"><?= esc($companyDescription) ?></p>
                            <?php endif; ?>

                            <div class="companies-directory-info">
                                <span><i class="fas fa-briefcase mr-1"></i><?= (int) ($company['open_jobs_count'] ?? 0) ?> open jobs</span>
                                <span><i class="fas fa-users mr-1"></i><?= esc($company['size'] ?: 'Size not specified') ?></span>
                            </div>

                            <div class="companies-directory-actions">
                                <a href="<?= base_url('company/' . (int) $company['id']) ?>" class="btn btn-outline-primary btn-sm">View Company</a>
                                <?php if ((int) ($company['open_jobs_count'] ?? 0) > 0): ?>
                                    <a href="<?= base_url('jobs?company=' . urlencode((string) ($company['name'] ?? ''))) ?>" class="btn btn-primary btn-sm">See Jobs</a>
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
