<?= view('Layouts/candidate_header', ['title' => 'Saved Jobs']) ?>
<?php $jobs = $jobs ?? []; ?>

<div class="jobs-page-jobboard saved-jobs-jobboard">
    <div class="container">
        <div class="page-board-header page-board-header-tight">
            <div class="page-board-copy">
                <span class="page-board-kicker"><i class="fas fa-bookmark"></i> Your shortlist</span>
                <h1 class="page-board-title">Saved Jobs</h1>
                <p class="page-board-subtitle">Jobs you bookmarked for later. Open a card to review the details or remove it from your saved list.</p>
            </div>
            <div class="job-details-header-actions">
                <a href="<?= base_url('jobs') ?>" class="btn btn-primary">
                    <i class="fas fa-search mr-1"></i> Browse Jobs
                </a>
            </div>
        </div>
    </div>

    <section class="site-section pt-0">
        <div class="container">
            <?php if (!empty($jobs)): ?>
                <div class="results-bar">
                    <span class="results-count"><strong><?= count($jobs) ?></strong> saved job<?= count($jobs) !== 1 ? 's' : '' ?></span>
                </div>

                <div class="saved-job-grid mb-4">
                    <?php foreach ($jobs as $job): ?>
                        <?php
                            $title = (string) ($job['title'] ?? 'Untitled Role');
                            $company = (string) ($job['company'] ?? 'Company');
                            $location = (string) ($job['location'] ?? 'N/A');
                            $experience = (string) ($job['experience_level'] ?? 'Not specified');
                            $type = strtolower((string) ($job['employment_type'] ?? ''));
                            $typeBadge = str_contains($type, 'part') ? 'badge-secondary' : 'badge-primary';
                            $initial = strtoupper(substr($company, 0, 1) ?: 'J');
                            $postedAt = !empty($job['created_at']) ? date('d M Y', strtotime((string) $job['created_at'])) : null;
                        ?>
                        <article class="job-card saved-job-card">
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-secondary py-0 px-2 job-card-save"
                                aria-label="Remove saved job"
                                title="Remove"
                                onclick="event.preventDefault();event.stopPropagation();window.location.href='<?= base_url('job/unsave/' . $job['id']) ?>';"
                            >
                                <i class="fas fa-bookmark"></i>
                            </button>
                            <div class="job-card-icon saved-job-logo">
                                <?php if (!empty($job['company_logo'])): ?>
                                    <img src="<?= base_url($job['company_logo']) ?>" alt="<?= esc($company) ?>">
                                <?php else: ?>
                                    <span><?= esc($initial) ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 class="job-card-title"><?= esc($title) ?></h3>
                            <p class="job-card-company"><?= esc($company) ?></p>
                            <div class="job-card-meta">
                                <span><i class="fas fa-map-pin"></i> <?= esc($location) ?></span>
                                <span><i class="fas fa-layer-group"></i> <?= esc($experience) ?></span>
                                <?php if ($postedAt !== null): ?>
                                    <span><i class="fas fa-clock"></i> <?= esc($postedAt) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="job-card-tags">
                                <span class="badge <?= $typeBadge ?>"><?= esc($job['employment_type'] ?: 'Full Time') ?></span>
                                <span class="badge badge-secondary"><?= esc(substr($title, 0, 15) ?: 'Role') ?></span>
                            </div>
                            <a href="<?= base_url('job/' . $job['id']) ?>" class="view-details">View Details &rarr;</a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-bookmark"></i>
                    <h5>No saved jobs yet</h5>
                    <p>Save jobs from listings and they will appear here.</p>
                    <a href="<?= base_url('jobs') ?>" style="display:inline-block;margin-top:12px;background:var(--ink);color:white;padding:10px 24px;border-radius:8px;text-decoration:none;font-family:'Syne',sans-serif;font-weight:700;">Browse Jobs</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
