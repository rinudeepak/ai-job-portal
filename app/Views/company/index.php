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
            </div>
        </div>
    </div>

    <section class="site-section pt-0 content-wrap">
        <div class="container">
            <div class="detail-card companies-search-card mb-4">
                <div class="panel-body company-search-panel">
                    <form id="companySearchForm" method="get" action="<?= base_url('companies') ?>">
                        <div class="row align-items-end company-search-grid">
                            <div class="col-12 col-md-6">
                                <label class="small text-muted text-uppercase font-weight-bold">Search Company</label>
                                <input id="companySearchInput" name="q" type="text" class="form-control" value="<?= esc($filters['q'] ?? '') ?>" placeholder="Search company, e.g. HubSpot, Shopify, Dropbox..." autocomplete="off" />
                            </div>
                            <div class="col-12 col-md-3">
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
                            <div class="col-12 col-md-3">
                                <label class="small text-muted text-uppercase font-weight-bold">Location</label>
                                <input type="text" name="location" class="form-control" value="<?= esc($filters['location'] ?? '') ?>" placeholder="City or HQ">
                            </div>
                            <div class="col-12 col-md-2">
                                <label class="small text-muted text-uppercase font-weight-bold">Jobs</label>
                                <select name="limit" class="form-control">
                                    <?php $jobLimit = (int) ($filters['limit'] ?? 10); ?>
                                    <option value="1" <?= $jobLimit === 1 ? 'selected' : '' ?>>1</option>
                                    <option value="10" <?= $jobLimit === 10 ? 'selected' : '' ?>>10</option>
                                    <option value="100" <?= $jobLimit === 100 ? 'selected' : '' ?>>100</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-auto company-search-actions">
                                <button type="submit" class="btn btn-primary company-search-btn">
                                    <i class="fas fa-search mr-1"></i> Search
                                </button>
                                <a href="<?= base_url('companies') ?>" class="btn btn-outline-secondary company-search-btn">Clear</a>
                            </div>
                        </div>
                    </form>
                    <div class="search-help-line">
                        <i class="fas fa-info-circle mr-1"></i>
                        Search registered companies first. If there is no local record, we check the official careers site and show matching jobs.
                    </div>
                </div>
            </div>
            <style>
                .companies-search-card {
                    background: #fff;
                    border: 1px solid #e9edf8;
                    border-radius: 22px;
                    box-shadow: 0 14px 35px rgba(15, 23, 42, 0.05);
                }
                .company-search-panel {
                    padding: 1.6rem 1.6rem 1.25rem;
                }
                .company-search-grid > [class*="col-"] {
                    margin-bottom: 1rem;
                }
                .company-search-grid label {
                    margin-bottom: 0.45rem;
                    letter-spacing: .04em;
                }
                .company-search-actions {
                    display: flex;
                    flex-wrap: wrap;
                    gap: .75rem;
                    align-items: center;
                    margin-bottom: 1rem;
                }
                .company-search-btn {
                    min-width: 120px;
                    height: 48px;
                    border-radius: 14px;
                    white-space: nowrap;
                }
                .search-help-line {
                    color: #748097;
                    font-size: .9rem;
                    display: flex;
                    align-items: center;
                    padding-top: .35rem;
                }
                .company-directory-card {
                    border: 1px solid #e8edf8;
                    border-radius: 18px;
                    padding: 22px;
                    background: #fff;
                    box-shadow: 0 12px 26px rgba(15, 23, 42, 0.045);
                    display: flex;
                    flex-direction: column;
                    min-height: 100%;
                }
                .company-directory-description {
                    color: #4f5d73;
                    line-height: 1.7;
                }
                .company-directory-actions {
                    margin-top: auto;
                    display: flex;
                    justify-content: flex-end;
                }
                .company-directory-tags {
                    margin-top: 0.75rem;
                }
                .company-directory-tags .badge {
                    background: #eef2ff;
                    color: #2f4bbd;
                    font-weight: 600;
                }
                .empty-state {
                    text-align: center;
                    padding: 3rem 2rem;
                    border: 1px solid #e9edf8;
                    border-radius: 18px;
                    background: #fff;
                    color: #5b6476;
                }
                .empty-state i {
                    font-size: 3rem;
                    margin-bottom: 1rem;
                    color: #4f6dca;
                }
                .searching-state {
                    border: 1px solid #e9edf8;
                    border-radius: 18px;
                    background: #fff;
                    box-shadow: 0 12px 26px rgba(15, 23, 42, 0.04);
                    padding: 1.25rem 1.4rem;
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                }
                .searching-state__icon {
                    width: 48px;
                    height: 48px;
                    border-radius: 14px;
                    background: linear-gradient(135deg, #f0f6ff 0%, #e8f7f4 100%);
                    color: #0b66ff;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    flex: 0 0 auto;
                }
                .searching-state__text h5 {
                    margin-bottom: .2rem;
                }
                .searching-state__text p {
                    margin-bottom: 0;
                    color: #677487;
                }
                @media (max-width: 575px) {
                    .company-directory-info {
                        grid-template-columns: 1fr;
                    }
                    .company-search-panel {
                        padding: 1.2rem 1rem 1rem;
                    }
                    .company-search-actions {
                        width: 100%;
                    }
                    .company-search-btn {
                        flex: 1 1 140px;
                    }
                }
            </style>

            <?php if (empty($companies)): ?>
                <?php if (!empty(trim((string) ($filters['q'] ?? '')))): ?>
                    <div id="companySearchResults" class="mt-4">
                        <div class="searching-state mb-4">
                            <div class="searching-state__icon">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                            <div class="searching-state__text">
                                <h5>Searching AI for <strong><?= esc($filters['q']) ?></strong></h5>
                                <p>No registered company matched your search. We are checking the official careers site and pulling available jobs.</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-building"></i>
                        <h5>No companies found</h5>
                        <p>Try a different company name, location, or industry filter.</p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="companies-directory-grid company-directory-grid mb-4">
                        <?php foreach ($companies as $company): ?>
                        <?php
                            $companyName = (string) ($company['name'] ?? 'Company');
                            $companyInitial = strtoupper(substr($companyName, 0, 1) ?: 'C');
                            $companyIndustry = trim((string) ($company['industry'] ?? ''));
                            $companyIndustryLabel = $companyIndustry !== '' ? $companyIndustry : 'Industry not specified';
                            $companyHq = trim((string) ($company['hq'] ?? ''));
                            $companyHqLabel = $companyHq !== '' ? $companyHq : 'HQ not specified';
                            $companySize = trim((string) ($company['size'] ?? ''));
                            $companySizeLabel = $companySize !== '' ? $companySize : 'Size not specified';
                            $openJobsCount = (int) ($company['open_jobs_count'] ?? 0);
                            $companyMeta = array_filter([
                                ['label' => 'Industry', 'value' => $companyIndustryLabel, 'icon' => 'fas fa-industry'],
                                ['label' => 'HQ', 'value' => $companyHqLabel, 'icon' => 'fas fa-map-pin'],
                            ]);
                        ?>
                        <article class="job-card company-directory-card" data-company-id="<?= (int) $company['id'] ?>" data-company-name="<?= esc($companyName) ?>">
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
                            <p class="job-card-company"><?= esc($companyIndustryLabel) ?></p>
                            <div class="job-card-meta company-directory-meta">
                                <span><i class="fas fa-map-pin"></i> <?= esc($companyHqLabel) ?></span>
                                <span class="company-job-count" data-default-label="open jobs" data-live-label="live jobs available">
                                    <i class="fas fa-briefcase"></i>
                                    <span class="company-job-count-number"><?= $openJobsCount ?></span>
                                    <span class="company-job-count-label">open jobs</span>
                                </span>
                            </div>
                            <div class="job-card-tags company-directory-tags">
                                <span class="badge badge-primary"><?= esc($companySizeLabel) ?></span>
                            </div>
                            <div class="company-directory-actions">
                                <a href="<?= base_url('jobs?company=' . urlencode($companyName)) ?>" class="company-directory-jobs-link <?= $openJobsCount > 0 ? '' : 'd-none' ?>">See jobs</a>
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

            <?php if (!empty(trim((string) ($filters['q'] ?? '')))): ?>
                <div id="companyLiveJobsResults" class="mt-4"></div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
(function waitForjQuery() {
    if (typeof window.jQuery === 'undefined') {
        return window.setTimeout(waitForjQuery, 50);
    }
    var $ = window.jQuery;
    var searchUrl = '<?= base_url('companies/search-jobs') ?>';
    var csrfName = '<?= csrf_token() ?>';
    var csrfHash = '<?= csrf_hash() ?>';
    var fallbackCompanyQuery = <?= json_encode(trim((string) ($filters['q'] ?? ''))) ?>;
    var fallbackJobLimit = <?= json_encode((int) ($filters['limit'] ?? 10)) ?>;
    var useAiFallback = <?= json_encode(!empty(trim((string) ($filters['q'] ?? ''))) && empty($companies)) ?>;

    $(function () {
        function escHtml(value) {
            return $('<div>').text(value || '').html();
        }

        function updateCompanyCardLiveJobs(result) {
            if (!result || !result.saved_company_id || !result.count || result.count <= 0) {
                return;
            }

            var $card = $('[data-company-id="' + result.saved_company_id + '"]');
            if (!$card.length) {
                return;
            }

            var $count = $card.find('.company-job-count');
            if ($count.length) {
                $count.addClass('text-success');
                $count.find('.company-job-count-number').text(result.count);
                $count.find('.company-job-count-label').text('live jobs available');
            }

            var $jobsLink = $card.find('.company-directory-jobs-link');
            if ($jobsLink.length) {
                $jobsLink.removeClass('d-none');
                $jobsLink.text('See live jobs');
            }
        }

        function getJobResultsTarget() {
            if ($('#companyLiveJobsResults').length) {
                return $('#companyLiveJobsResults');
            }
            if ($('#companyJobResults').length) {
                return $('#companyJobResults');
            }
            return null;
        }

        function renderCompanyCard(info) {
            if (!info || !info.name) {
                return '';
            }

            let html = '<article class="job-card company-directory-card mb-4">';
            html += '<div class="job-card-icon company-directory-logo">';
            if (info.logo_url) {
                html += '<img src="' + escHtml(info.logo_url) + '" alt="' + escHtml(info.name) + '">';
            } else {
                html += '<span>' + escHtml(info.name.charAt(0).toUpperCase() || 'C') + '</span>';
            }
            html += '</div>';
            html += '<h3 class="job-card-title">';
            html += '<a href="<?= base_url("companies") ?>">' + escHtml(info.name) + '</a>';
            html += '</h3>';
            html += '<p class="job-card-company">' + escHtml(info.industry || 'Industry not specified') + '</p>';
            html += '<div class="job-card-meta company-directory-meta">';
            html += '<span><i class="fas fa-map-pin"></i> ' + escHtml(info.hq || info.location || 'HQ not specified') + '</span>';
            html += '<span class="company-job-count" data-default-label="open jobs" data-live-label="live jobs available">';
            html += '<i class="fas fa-briefcase"></i> <span class="company-job-count-number">0</span> <span class="company-job-count-label">open jobs</span>';
            html += '</span>';
            html += '</div>';
            html += '<div class="job-card-tags company-directory-tags">';
            html += '<span class="badge badge-primary">' + escHtml(info.size || 'Size not specified') + '</span>';
            html += '</div>';
            html += '<div class="company-directory-actions">';
            html += '<a href="<?= base_url("jobs?company=") ?>' + encodeURIComponent(info.name) + '" class="company-directory-jobs-link">See jobs</a>';
            html += '</div>';
            html += '</article>';
            return html;
        }

        function renderJobResults(result) {
            let html = '';
            var $target = getJobResultsTarget();
            if (!$target) {
                return;
            }
            if (!result.jobs || result.jobs.length === 0) {
                html += '<div class="alert alert-info mb-0">';
                html += '<strong>' + escHtml(result.company) + '</strong> has no open roles detected on the career page right now.';
                html += '</div>';
                $target.html(html);
                return;
            }

            html += '<div class="table-responsive"><table class="table table-sm mb-0">';
            html += '<thead><tr><th>Role</th><th>Location</th><th>Department</th><th></th></tr></thead><tbody>';
            $.each(result.jobs, function (index, job) {
                html += '<tr>';
                html += '<td><strong>' + escHtml(job.title) + '</strong></td>';
                html += '<td><i class="fas fa-map-pin mr-1 text-muted"></i>' + escHtml(job.location || 'Not specified') + '</td>';
                html += '<td><span class="badge badge-light">' + escHtml(job.department || '-') + '</span></td>';
                html += '<td>';
                html += '<a href="' + escHtml(job.apply_url) + '" target="_blank" rel="noopener" class="btn btn-sm btn-primary mr-1">';
                html += '<i class="fas fa-external-link-alt mr-1"></i>Apply</a>';
                html += '</td>';
                html += '</tr>';
            });
            html += '</tbody></table></div>';
            
            let sourceLabel = 'official career page (AI)';
            html += '<div class="mt-3 pt-3 border-top">';
            html += '<small class="text-muted">' + result.count + ' roles from ' + sourceLabel + '</small>';
            html += '</div>';
            
            $target.html(html);
        }

        function fetchCompanyInfo(companyName) {
            if (!companyName) {
                return;
            }

            $('#companySearchResults').html(
                '<div class="searching-state mb-4">' +
                    '<div class="searching-state__icon">' +
                        '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>' +
                    '</div>' +
                    '<div class="searching-state__text">' +
                        '<h5>Searching AI for <strong>' + escHtml(companyName) + '</strong></h5>' +
                        '<p>Gathering company details from the official website and careers page.</p>' +
                    '</div>' +
                '</div>'
            );

            var data = {
                company_name: companyName,
                info_only: 1,
                [csrfName]: csrfHash
            };

            $.post(searchUrl, data)
                .done(function (result) {
                    let html = renderCompanyCard(result.company_info || { name: companyName });
                    if (result.saved_company_id && result.saved_company_id > 0) {
                        html = '<div class="alert alert-success mb-3"><i class="fas fa-check mr-2"></i>Company added to directory!</div>' + html;
                    }
                    $('#companySearchResults').html(html);
                    fetchCompanyJobs(companyName, fallbackJobLimit);
                })
                .fail(function (xhr) {
                    let msg = '<div class="alert alert-danger">';
                    msg += xhr.responseJSON?.error || 'Company details fetch failed. ';
                    msg += 'Please try again later.</div>';
                    $('#companySearchResults').html(msg);
                });
        }

        function fetchCompanyJobs(companyName, limit) {
            if (!companyName) {
                return;
            }

            $('#companyJobResults').html(
                '<div class="searching-state mb-3">' +
                    '<div class="searching-state__icon">' +
                        '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div>' +
                    '</div>' +
                    '<div class="searching-state__text">' +
                        '<h5>Loading jobs for <strong>' + escHtml(companyName) + '</strong></h5>' +
                        '<p>Pulling the best matching roles from the company careers page.</p>' +
                    '</div>' +
                '</div>'
            );

            var data = {
                company_name: companyName,
                limit: limit || fallbackJobLimit,
                [csrfName]: csrfHash
            };

            $.post(searchUrl, data)
                .done(function (result) {
                    renderJobResults(result);
                    updateCompanyCardLiveJobs(result);
                })
                .fail(function (xhr) {
                    let msg = '<div class="alert alert-danger">';
                    msg += xhr.responseJSON?.error || 'Job search failed. ';
                    msg += 'Please try again later.</div>';
                    $('#companyJobResults').html(msg);
                });
        }

        if (useAiFallback) {
            fetchCompanyInfo(fallbackCompanyQuery);
        }

        if (fallbackCompanyQuery && !useAiFallback) {
            fetchCompanyJobs(fallbackCompanyQuery, fallbackJobLimit);
        }

        $(document).on('click', '#aiSeeJobsBtn', function (e) {
            e.preventDefault();
            fetchCompanyJobs(fallbackCompanyQuery, fallbackJobLimit);
        });

    });
})();
</script>
<?= view('Layouts/candidate_footer') ?>
