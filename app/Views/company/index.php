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
                <div class="panel-body">
                    <div class="row gx-2 gy-2 align-items-center">
                        <div class="col-12 col-md">
                            <input id="companySearchInput" type="text" class="form-control" placeholder="Search company career page, e.g. HubSpot, Shopify, Dropbox..." autocomplete="off" />
                        </div>
                        <div class="col-auto" style="min-width:220px;">
                            <select id="companySearchLimit" class="form-control" style="width:220px; min-width:220px; max-width:220px; padding-right:2.5rem; color:#212529; background-color:#fff;">
                                <option value="10">10 jobs</option>
                                <option value="25">25 jobs</option>
                                <option value="50">50 jobs</option>
                                <option value="100">100 jobs</option>
                            </select>
                        </div>
                        <div class="col-auto">
                            <button type="button" id="companySearchBtn" class="btn btn-primary" style="min-width:120px; white-space:nowrap;">
                                <i class="fas fa-search mr-1"></i> Search Jobs
                            </button>
                        </div>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        <i class="fas fa-info-circle mr-1"></i>
                        Search ANY company to fetch live jobs from their official careers page using AI.
                    </small>
                    <div id="companySearchResults" class="mt-4">
                        <p class="text-muted small mb-0">Search for a company to fetch live jobs from their official career page.</p>
                    </div>
                </div>
            </div>

            <?= view('company/_targets_section') ?>

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

<script>
(function waitForjQuery() {
    if (typeof window.jQuery === 'undefined') {
        return window.setTimeout(waitForjQuery, 50);
    }
    var $ = window.jQuery;
    var searchUrl = '<?= base_url('companies/search-jobs') ?>';
    var csrfName = '<?= csrf_token() ?>';
    var csrfHash = '<?= csrf_hash() ?>';

    $(function () {
        function escHtml(value) {
            return $('<div>').text(value || '').html();
        }

        var currentSearchCompany = '';

        function renderJobs(result) {
            currentSearchCompany = result.company;

            if (!result.jobs || result.jobs.length === 0) {
                let msg = '<p class="text-muted small">No open jobs found for <strong>' + escHtml(result.company) + '</strong>.</p>';
                msg += '<p class="text-info small mt-2"><i class="fas fa-magic mr-1"></i>AI scan completed - no open roles on career page.</p>';
                $('#companySearchResults').html(msg);
                return;
            }

            let html = '<div class="table-responsive"><table class="table table-sm mb-0">';
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
            
            $('#companySearchResults').html(html);
        }



        function loadTargets() {
            $('#loadTargetsBtn').show();
            $('#refreshTargetsBtn').prop('disabled', true);
            
            $.get(targetsUrl, { [csrfName]: csrfHash })
                .done(function(response) {
                    if (response.success && response.companies && response.companies.length > 0) {
                        let html = '<div class="row">';
                        $.each(response.companies, function(i, company) {
                            let initial = company.company_name.charAt(0).toUpperCase();
                            html += '<div class="col-md-6 col-lg-4 mb-3">';
                            html += '<div class="card h-100 target-company-card">';
                            html += '<div class="card-body">';
                            html += '<div class="target-company-header d-flex">';
                            html += '<div class="target-logo rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width:45px;height:45px;font-size:16px;font-weight-bold;">' + initial + '</div>';
                            html += '<div class="flex-grow-1">';
                            html += '<h6 class="mb-1">' + escHtml(company.company_name) + '</h6>';
                            html += '<small class="text-muted">' + company.jobs_count + ' jobs</small>';
                            html += '</div>';
                            html += '<div class="dropdown">';
                            html += '<button class="btn btn-sm btn-link p-0 text-muted dropdown-toggle" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>';
                            html += '<div class="dropdown-menu dropdown-menu-right">';
                            html += '<a class="dropdown-item refresh-target-single" href="#" data-id="' + company.id + '"><i class="fas fa-sync-alt mr-1"></i>Refresh jobs</a>';
                            html += '<a class="dropdown-item text-danger" href="<?= base_url("target-company/remove/") ?>' + company.id + '" onclick="return confirm(\'Remove from targets?\')">';
                            html += '<i class="fas fa-trash mr-1"></i>Remove</a>';
                            html += '</div></div></div>';
                            
                            if (company.recent_jobs && company.recent_jobs.length > 0) {
                                html += '<div class="mt-2">';
                                $.each(company.recent_jobs.slice(0,2), function(j, job) {
                                    html += '<div class="small-job-item mb-1 pb-1 border-bottom">';
                                    html += '<div class="font-weight-bold text-truncate" style="max-width:250px;" title="' + escHtml(job.title) + '">' + escHtml(job.title) + '</div>';
                                    html += '<small class="text-muted">' + escHtml(job.location) + '</small>';
                                    html += '</div>';
                                });
                                if (company.recent_jobs.length > 2) {
                                    html += '<small class="text-muted">+' + (company.recent_jobs.length - 2) + ' more</small>';
                                }
                                html += '</div>';
                            } else {
                                html += '<small class="text-muted mt-2 d-block">No recent jobs (click refresh)</small>';
                            }
                            html += '</div></div></div>';
                        });
                        html += '</div>';
                        $('#targetsContent').html(html);
                    } else {
                        $('#targetsContent').html('<p class="text-muted mb-0"><i class="fas fa-star mr-1"></i>No target companies yet. Search above to add your first!</p>');
                    }
                })
                .fail(function() {
                    $('#targetsContent').html('<p class="text-danger small">Failed to load targets. <button class="btn btn-sm btn-link p-0" onclick="loadTargets()">Retry</button></p>');
                })
                .always(function() {
                    $('#loadTargetsBtn').hide();
                    $('#refreshTargetsBtn').prop('disabled', false);
                });
        }

        $('#companySearchBtn').on('click', function () {
            var companyName = $('#companySearchInput').val().trim();
            var limit = parseInt($('#companySearchLimit').val(), 10) || 10;

            if (companyName === '') {
                $('#companySearchResults').html('<p class="text-danger small">Please enter a company name to search.</p>');
                return;
            }

            $('#companySearchBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Loading...');
            $('#companySearchResults').html('<p class="text-muted small">Searching jobs for <strong>' + escHtml(companyName) + '</strong> from official career page (AI)...</p>');

            var data = { 
                company_name: companyName, 
                limit: limit,
                [csrfName]: csrfHash 
            };

            $.post(searchUrl, data)
                .done(renderJobs)
                .fail(function (xhr) {
                    let msg = '<div class="alert alert-danger">';
                    msg += xhr.responseJSON?.error || 'Search failed. ';
                    msg += 'Please try again or check connection.</div>';
                    $('#companySearchResults').html(msg);
                })
                .always(function () {
                    $('#companySearchBtn').prop('disabled', false).html('<i class="fas fa-search mr-1"></i> Search Jobs');
                });
        });

        $('#refreshTargetsBtn').on('click', loadTargets);
        loadTargets();

        $(document).on('click', '.refresh-target-single, .refresh-jobs-btn', function(e) {
            e.preventDefault();
            var targetId = $(this).data('target') || $(this).data('id');
            var $btn = $(this);
            
            $btn.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);
            
            $.post(refreshUrl + '/' + targetId, { [csrfName]: csrfHash })
                .done(function(response) {
                    if (response.success) {
                        let msg = 'Refreshed! Found ' + response.count + ' new jobs.';
                        if (response.count === 0) msg = 'No new jobs found.';
                        alert(msg);
                        loadTargets();
                    } else {
                        alert('Refresh failed: ' + (response.error || 'Unknown error'));
                    }
                })
                .fail(function() {
                    alert('Refresh failed. Try again.');
                })
                .always(function() {
                    $btn.html('<i class="fas fa-sync-alt"></i>').prop('disabled', false);
                });
        });
    });
})();
</script>
<?= view('Layouts/candidate_footer') ?>


        $('#companySearchInput').on('keypress', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                $('#companySearchBtn').trigger('click');
            }
        });
    });
})();
</script>
<?= view('Layouts/candidate_footer') ?>
