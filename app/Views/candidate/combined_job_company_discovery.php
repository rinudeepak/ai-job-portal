                <?= view('Layouts/candidate_header', ['title' => 'Company & Job Discovery']) ?>


<div class="job-details-jobboard combined-discovery-page">
    <div class="container">
        <div class="page-board-header page-board-header-tight">
            <div class="page-board-copy">
                <span class="page-board-kicker"><i class="fas fa-magnifying-glass"></i> AI Discovery</span>
                <h1 class="page-board-title">Company & Job Discovery</h1>
                <p class="page-board-subtitle">Search for live job listings from top multinational companies or browse our comprehensive company directory.</p>
            </div>
        </div>
    </div>

    <section class="site-section pt-0 content-wrap">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <!-- Classic Search UI -->
                    <div class="detail-card mb-4 shadow-sm">
                        <div class="panel-body company-search-panel p-4">
                            <form id="unifiedDiscoverySearchForm" method="get" action="<?= base_url('candidate/company-job-discovery') ?>">
                                <div class="row align-items-end company-search-grid">
                                    <div class="col-12 col-md-10">
                                        <label class="small text-muted text-uppercase font-weight-bold">Search Company</label>
                                        <div class="company-autocomplete">
                                            <input type="text" id="mncCompanySearchInput" name="q" class="form-control" value="<?= esc($filters['q'] ?? '') ?>" placeholder="Search company, e.g. HubSpot, Google, Amazon..." autocomplete="off">
                                            <div class="company-autocomplete-dropdown" id="companyAutocompleteDropdown"></div>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-2">
                                        <label class="small text-muted text-uppercase font-weight-bold d-block">&nbsp;</label>
                                        <button class="btn btn-primary w-100" type="submit" id="searchMainBtn">
                                            <i class="fas fa-search mr-1"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- MNC Discovery Results (Dynamic) -->
                    <div id="mncDiscoverySection">
                        <div id="mncLoadingSpinner" class="text-center my-5 d-none">
                            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <h5 class="mt-4 font-weight-bold">Analyzing public job boards with AI...</h5>
                            <p class="text-muted">This usually takes 10-50 seconds as we verify recent listings.</p>
                        </div>
                        
                        <div id="mncDiscoveryResults" class="mt-4 d-none">
                            <div class="row">
                                <!-- Left Panel: Job Listings -->
                                <div class="col-lg-8">
                                    <div id="mncJobListingsPanel">
                                        <!-- Job results will be loaded here -->
                                    </div>
                                </div>
                                <!-- Right Panel: Company Information -->
                                <div class="col-lg-4">
                                    <div id="mncCompanyInfoPanel" class="sticky-top" style="top: 20px;">
                                        <!-- Company card will be injected here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Company Directory Section (Paginated) -->
                    <?php if (!$shouldAutoTriggerAiSearch): // Only show this section if AI search is NOT auto-triggered ?>
                        <div id="companyDirectorySection" class="mt-5">
                            <div class="d-flex align-items-center mb-4">
                                <h4 class="mb-0 font-weight-bold">Registered Companies</h4>
                                <span class="badge badge-light border ml-2">Directory Listing</span>
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
                                        $companyName    = (string) ($company['name'] ?? 'Company');
                                        $companyInitial = strtoupper(substr($companyName, 0, 1) ?: 'C');
                                        $companyIndustry = trim((string) ($company['industry'] ?? ''));
                                        $companyHq      = trim((string) ($company['hq'] ?? ''));
                                        $companySize    = trim((string) ($company['size'] ?? ''));
                                        $companyLogo    = trim((string) ($company['logo'] ?? ''));
                                        $companyWebsite = trim((string) ($company['website'] ?? ''));
                                        $websiteHost    = $companyWebsite !== '' ? (parse_url($companyWebsite, PHP_URL_HOST) ?: $companyWebsite) : '';
                                        $websiteHost    = preg_replace('/^www\./i', '', (string) $websiteHost) ?? '';
                                        $googleLogoUrl  = $websiteHost !== '' ? '<https://www.google.com/s2/favicons?domain=>' . rawurlencode($websiteHost) . '&sz=96' : '';
                                        $logoUrl        = $companyLogo !== '' ? base_url($companyLogo) : $googleLogoUrl;
                                        $fallbackHtml   = '<span>' . esc($companyInitial) . '</span>';
                                        $logoErrorJs    = "if(this.dataset.googleLogo&&this.src!==this.dataset.googleLogo){this.src=this.dataset.googleLogo;}else{this.parentNode.innerHTML='" . $fallbackHtml . "';}";
                                        ?>
                                        <article class="job-card company-directory-card" data-company-id="<?= (int) $company['id'] ?>" data-company-name="<?= esc($companyName) ?>">
                                            <div class="job-card-icon company-directory-logo">
                                                <?php if ($logoUrl !== ''): ?>
                                                    <img src="<?= esc($logoUrl) ?>"
                                                         alt="<?= esc($companyName) ?>"
                                                         data-google-logo="<?= esc($googleLogoUrl) ?>"
                                                         onerror="<?= esc($logoErrorJs, 'attr') ?>">
                                                <?php else: ?>
                                                    <span><?= esc($companyInitial) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <h3 class="job-card-title">
                                                <a href="<?= base_url('company/' . (int) $company['id']) ?>"><?= esc($companyName) ?></a>
                                            </h3>
                                            <p class="job-card-company"><?= esc($companyIndustry ?: 'Industry not specified') ?></p>
                                            <div class="job-card-meta company-directory-meta">
                                                <span><i class="fas fa-map-pin"></i> <?= esc($companyHq ?: 'HQ not specified') ?></span>
                                            </div>
                                            <div class="job-card-tags company-directory-tags">
                                                <span class="badge badge-primary"><?= esc($companySize ?: 'Size not specified') ?></span>
                                            </div>
                                            <div class="company-directory-actions">
                                                <a href="<?= base_url('jobs?company=' . urlencode($companyName)) ?>"
                                                   class="company-directory-jobs-link">
                                                    <i class="fas fa-briefcase mr-1) "></i> <span class="jobs-link-text">See live jobs</span>
                                                </a>
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
                                                $cur   = $pager->getCurrentPage();
                                                $total = $pager->getPageCount();
                                                // Construct base URL for pagination, preserving current filters
                                                $paginationBaseUrl = base_url('candidate/company-job-discovery') . '?' . http_build_query(array_filter($filters));
                                                $sep   = strpos($paginationBaseUrl, '?') !== false ? '&' : '?';
                                                if ($cur > 1): ?>
                                                    <a class="prev" href="<?= $paginationBaseUrl . $sep . 'page=' . ($cur - 1) ?>">Prev</a>
                                                <?php endif; ?>
                                                <div class="d-inline-block">
                                                    <?php for ($i = 1; $i <= $total; $i++): ?>
                                                        <?php if ($i == $cur): ?>
                                                            <a class="active" href="#"><?= $i ?></a>
                                                        <?php else: ?>
                                                            <a href="<?= $paginationBaseUrl . $sep . 'page=' . $i ?>"><?= $i ?></a>
                                                        <?php endif; ?>
                                                    <?php endfor; ?>
                                                </div>
                                                <?php if ($cur < $total): ?>
                                                    <a class="next" href="<?= $paginationBaseUrl . $sep . 'page=' . ($cur + 1) ?>">Next</a>
                                                <?php endif; ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // MNC Job Discovery elements
    const mncSearchInput = document.getElementById('mncCompanySearchInput');
    const mncSearchButton = document.getElementById('searchMainBtn');
    const mncLoadingSpinner = document.getElementById('mncLoadingSpinner');
    const mncDiscoveryResultsDiv = document.getElementById('mncDiscoveryResults');
    const mncJobListingsPanel = document.getElementById('mncJobListingsPanel');
    const mncCompanyInfoPanel = document.getElementById('mncCompanyInfoPanel');
    const autocompleteDropdown = document.getElementById('companyAutocompleteDropdown');

    const hasSearchQuery = <?= $hasSearchQuery ? 'true' : 'false' ?>;
    const foundRegisteredCompanies = <?= $foundRegisteredCompanies ? 'true' : 'false' ?>;
    const shouldAutoTriggerAiSearch = <?= $shouldAutoTriggerAiSearch ? 'true' : 'false' ?>;
    const companyDirectorySection = document.getElementById('companyDirectorySection');

    // Autocomplete functionality
    let autocompleteTimeout = null;
    let currentSuggestions = [];
    let selectedIndex = -1;

    mncSearchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        if (query.length < 2) {
            hideAutocomplete();
            return;
        }

        clearTimeout(autocompleteTimeout);
        autocompleteTimeout = setTimeout(() => {
            fetchCompanySuggestions(query);
        }, 300);
    });

    mncSearchInput.addEventListener('keydown', function(e) {
        if (!autocompleteDropdown.classList.contains('show')) return;

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, currentSuggestions.length - 1);
            updateSelectedItem();
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateSelectedItem();
        } else if (e.key === 'Enter') {
            if (selectedIndex >= 0) {
                e.preventDefault();
                selectSuggestion(currentSuggestions[selectedIndex]);
            }
        } else if (e.key === 'Escape') {
            hideAutocomplete();
        }
    });

    document.addEventListener('click', function(e) {
        if (!mncSearchInput.contains(e.target) && !autocompleteDropdown.contains(e.target)) {
            hideAutocomplete();
        }
    });

    function fetchCompanySuggestions(query) {
        autocompleteDropdown.innerHTML = '<div class="company-autocomplete-loading"><i class="fas fa-spinner fa-spin"></i> Loading suggestions...</div>';
        autocompleteDropdown.classList.add('show');

        fetch('<?= base_url('candidate/company-jobs/suggestions') ?>?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success' && Array.isArray(data.suggestions)) {
                    currentSuggestions = data.suggestions;
                    selectedIndex = -1;
                    renderSuggestions(data.suggestions);
                } else {
                    hideAutocomplete();
                }
            })
            .catch(error => {
                console.error('Autocomplete error:', error);
                hideAutocomplete();
            });
    }

    function renderSuggestions(suggestions) {
        if (suggestions.length === 0) {
            autocompleteDropdown.innerHTML = '<div class="company-autocomplete-loading">No companies found</div>';
            return;
        }

        let html = '';
        suggestions.forEach((suggestion, index) => {
            const isInternal = suggestion.source === 'internal';
            const badge = isInternal
                ? '<span class="company-autocomplete-badge">Registered</span>'
                : '';

            const logoHtml = suggestion.logo
                ? `<img src="${escapeHtml(suggestion.logo)}" alt="" class="company-autocomplete-logo" onerror="this.style.display='none'">`
                : `<span class="company-autocomplete-logo-fallback">${escapeHtml(suggestion.name.charAt(0).toUpperCase())}</span>`;

            const meta = [];
            if (suggestion.domain) meta.push(suggestion.domain);
            const metaText = meta.join('');

            html += `
                <div class="company-autocomplete-item" data-index="${index}">
                    <div class="company-autocomplete-logo-wrap">${logoHtml}</div>
                    <div class="company-autocomplete-text">
                        <div class="company-autocomplete-name">${escapeHtml(suggestion.name)}${badge}</div>
                        ${metaText ? `<div class="company-autocomplete-meta">${escapeHtml(metaText)}</div>` : ''}
                    </div>
                </div>
            `;
        });

        autocompleteDropdown.innerHTML = html;
        autocompleteDropdown.classList.add('show');

        autocompleteDropdown.querySelectorAll('.company-autocomplete-item').forEach(item => {
            item.addEventListener('click', function() {
                selectSuggestion(currentSuggestions[parseInt(this.dataset.index)]);
            });
        });
    }

    function updateSelectedItem() {
        const items = autocompleteDropdown.querySelectorAll('.company-autocomplete-item');
        items.forEach((item, index) => {
            item.classList.toggle('active', index === selectedIndex);
        });

        if (selectedIndex >= 0 && items[selectedIndex]) {
            items[selectedIndex].scrollIntoView({ block: 'nearest' });
        }
    }

    function selectSuggestion(suggestion) {
        mncSearchInput.value = suggestion.name;
        hideAutocomplete();
        mncSearchInput.focus();
    }

    function hideAutocomplete() {
        autocompleteDropdown.classList.remove('show');
        autocompleteDropdown.innerHTML = '';
        currentSuggestions = [];
        selectedIndex = -1;
    }

    // Auto-trigger AI discovery if a search query is present on load
    if (shouldAutoTriggerAiSearch) {
        fetchMncJobs();
    }

    // Function for MNC Job Discovery
    function fetchMncJobs() {
        const companyName = mncSearchInput.value.trim();
        if (companyName === '') {
            alert('Please enter a company name.');
            return;
        }
        const limit = '10'; // Standard limit per search

        mncDiscoveryResultsDiv.classList.add('d-none');
        // Scroll to AI Discovery section if it's being triggered
        document.getElementById('mncDiscoverySection').scrollIntoView({ behavior: 'smooth', block: 'start' });
        mncCompanyInfoPanel.innerHTML = '';
        mncJobListingsPanel.innerHTML = '';
        mncLoadingSpinner.classList.remove('d-none');
        mncSearchButton.disabled = true;

        fetch('<?= base_url('mnc/discover') ?>?company=' + encodeURIComponent(companyName) + '&limit=' + encodeURIComponent(limit))
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(`Server Error (${response.status}): ${text.substring(0, 100)}...`);
                    });
                }
                return response.json();
            })
            .then(data => {
                mncLoadingSpinner.classList.add('d-none');
                mncSearchButton.disabled = false;
                mncDiscoveryResultsDiv.classList.remove('d-none');

                if (data.success) {
                    // Render Company Info Card
                    if (data.company_info) {
                        const info = data.company_info;
                        const companyName = info.name || data.company || 'Company';
                        const companyInitial = companyName.charAt(0).toUpperCase();
                        const websiteUrl = normalizeUrl(info.website || '');
                        const websiteLabel = websiteUrl ? websiteUrl.replace(/^https?:\/\//, '').replace(/\/$/, '') : '';
                        const websiteHost = websiteLabel.replace(/^www\./, '').split('/')[0];
                        const googleLogoUrl = websiteHost ? `https://www.google.com/s2/favicons?domain=${encodeURIComponent(websiteHost)}&sz=96` : '';
                        const logoSrc = info.logo_url || googleLogoUrl;
                        const logoHtml = logoSrc
                            ? `<img src="${escapeHtml(logoSrc)}" alt="${escapeHtml(companyName)}" data-google-logo="${escapeHtml(googleLogoUrl)}" onerror="window.hmCompanyLogoFallback(this, '${escapeHtml(companyInitial)}')">`
                            : `<span>${escapeHtml(companyInitial)}</span>`;
                        let socialHtml = '';
                        ['linkedin', 'twitter', 'facebook', 'instagram', 'youtube'].forEach(platform => {
                            if (info[platform]) {
                                socialHtml += `<a href="${escapeHtml(normalizeUrl(info[platform]))}" target="_blank" rel="noopener" class="mr-3 text-muted"><i class="fab fa-${platform} fa-lg"></i></a>`;
                            }
                        });
                        const websiteHtml = websiteUrl
                            ? `<div class="mb-2"><i class="fas fa-globe text-muted mr-2"></i> <a href="${escapeHtml(websiteUrl)}" target="_blank" rel="noopener" class="text-truncate d-inline-block" style="max-width: 100%">${escapeHtml(websiteLabel)}</a></div>`
                            : '';

                        const companyCardHtml = `
                            <article class="job-card company-directory-card">
                                <div class="job-card-icon company-logo-wrapper">
                                    ${logoHtml}
                                </div>
                                <div class="mt-3">
                                    <h3 class="job-card-title mb-1" style="font-size: 1.25rem;">${escapeHtml(companyName)}</h3>
                                    <p class="job-card-company mb-3">${escapeHtml(info.industry || 'MNC')}</p>
                                    
                                    <div class="job-card-meta mb-3">
                                        <div class="mb-2"><i class="fas fa-map-pin text-muted mr-2"></i> ${escapeHtml(info.hq || 'Global HQ')}</div>
                                        <div class="mb-2"><i class="fas fa-users text-muted mr-2"></i> ${escapeHtml(info.size || 'Enterprise')}</div>
                                        ${websiteHtml}
                                    </div>
                                    
                                    <hr>
                                    <p class="small text-secondary mb-3">${escapeHtml(info.short_description || 'Public profile fetched via AI discovery engine.')}</p>
                                    
                                    <div class="mt-3 d-flex align-items-center">
                                        ${socialHtml}
                                    </div>
                                </div>
                            </article>
                        `;
                        mncCompanyInfoPanel.innerHTML = companyCardHtml;
                    }

                    // Render Job Listings
                    if (Array.isArray(data.jobs) && data.jobs.length > 0) {
                        let jobsHtml = '';
                        data.jobs.forEach(job => {
                            const applyUrl = normalizeUrl(job.apply_url || '');
                            const jobId = String(job.id || '');
                            const isSaved = job.is_saved === true || job.is_saved === '1' || job.is_saved === 1;
                            const saveUrl = jobId ? `<?= base_url('mnc/job/save/') ?>${encodeURIComponent(jobId)}` : '';
                            const unsaveUrl = jobId ? `<?= base_url('mnc/job/unsave/') ?>${encodeURIComponent(jobId)}` : '';
                            const saveButtonHtml = jobId ? `
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary py-0 px-2 job-card-save js-save-job-toggle ${isSaved ? 'is-saved' : ''}"
                                    style="position: absolute; bottom: 20px; right: 20px; top: auto;"
                                    aria-label="${isSaved ? 'Saved job' : 'Save job'}"
                                    title="${isSaved ? 'Saved' : 'Save Job'}"
                                    data-save-url="${escapeHtml(isSaved ? unsaveUrl : saveUrl)}"
                                    data-save-url-save="${escapeHtml(saveUrl)}"
                                    data-save-url-unsave="${escapeHtml(unsaveUrl)}"
                                    data-saved="${isSaved ? '1' : '0'}"
                                    data-save-label-save="Save Job"
                                    data-save-label-saved="Saved"
                                >
                                    <i class="js-save-icon ${isSaved ? 'fas' : 'far'} fa-bookmark"></i>
                                </button>
                            ` : '';
                            jobsHtml += `
                                <article class="job-card mb-3" style="position: relative;">
                                    <div class="job-card-icon">
                                        <span><i class="fas fa-briefcase"></i></span>
                                    </div>
                                    <div class="job-card-body">
                                        <h3 class="job-card-title">${escapeHtml(job.title || 'Untitled Role')}</h3>
                                        <p class="job-card-company">${escapeHtml(job.company_name || 'MNC')}</p>
                                        <div class="job-card-meta">
                                            <span><i class="fas fa-map-pin"></i> ${escapeHtml(job.location || 'N/A')}</span>
                                            <span><i class="fas fa-clock"></i> ${escapeHtml(job.posted_at_raw || 'Recently')}</span>
                                        </div>
                                        
                                        <a href="${escapeHtml(applyUrl || '#')}" target="_blank" rel="noopener" class="view-details">Apply Now &rarr;</a>
                                    </div>
                                    ${saveButtonHtml}
                                </article>
                            `;
                        });
                        mncJobListingsPanel.innerHTML = jobsHtml;
                    } else {
                        mncJobListingsPanel.innerHTML = `
                            <div class="alert alert-info py-4" role="alert">
                                <p class="mb-0">No live jobs found for ${escapeHtml(data.company || companyName)}</p>
                            </div>`;
                    }
                } else {
                    mncJobListingsPanel.innerHTML = `<div class="alert alert-danger" role="alert">${escapeHtml(data.error || 'Failed to fetch jobs. Please try again later.')}</div>`;
                }
            })
            .catch(error => {
                console.error('MNC Discovery Error:', error);
                mncLoadingSpinner.classList.add('d-none');
                mncSearchButton.disabled = false;
                mncDiscoveryResultsDiv.classList.remove('d-none');
                mncJobListingsPanel.innerHTML = `<div class="alert alert-danger" role="alert">${escapeHtml(error.message || 'Failed to fetch jobs. Please try again later.')}</div>`;
            });
    }

    // Helper functions (from mnc_job_discovery_view.php)
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(String(str ?? '')));
        return div.innerHTML;
    }

    function normalizeUrl(url) {
        const value = String(url || '').trim();
        if (value === '' || value === '#') {
            return '';
        }

        if (/^https?:\/\//i.test(value)) {
            return value;
        }

        return 'https://' + value.replace(/^\/+/, '');
    }

    window.hmCompanyLogoFallback = function(img, initial) {
        const googleLogo = img.dataset.googleLogo || '';
        // If the failed source was Clearbit, immediately try the Google Favicon fallback
        if (img.src.includes('clearbit.com') && googleLogo && img.src !== googleLogo) {
            
            img.src = googleLogo;
            return;
        }
        if (googleLogo && img.src !== googleLogo) {
            img.src = googleLogo;
            return;
        }
        img.parentNode.innerHTML = `<span>${escapeHtml(initial || 'C')}</span>`;
    };
});
</script>

<?= view('Layouts/candidate_footer') ?>        