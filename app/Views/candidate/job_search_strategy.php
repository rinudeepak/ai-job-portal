<?= view('Layouts/candidate_header', ['title' => 'Job Search Strategy Coach']) ?>

<?php
$jobSearchStrategy = $jobSearchStrategy ?? [];
$topSuggestedJobs = $topSuggestedJobs ?? [];
$recommendedIds = array_map('intval', (array) ($jobSearchStrategy['recommended_job_ids'] ?? []));
$recommendedJobs = array_values(array_filter($topSuggestedJobs, static function (array $job) use ($recommendedIds): bool {
    return in_array((int) ($job['id'] ?? 0), $recommendedIds, true);
}));
if (empty($recommendedJobs)) {
    $recommendedJobs = array_slice($topSuggestedJobs, 0, 3);
}
$source = (string) ($jobSearchStrategy['source'] ?? 'fallback');
?>

<div class="container">
    <div class="page-board-header page-board-header-tight">
        <div class="page-board-copy">
            <span class="page-board-kicker"><i class="fas fa-compass"></i> Search guidance</span>
            <h1 class="page-board-title">Job Search Strategy Coach</h1>
            <p class="page-board-subtitle">Turn your profile into a sharper search plan with target roles, action steps, and weekly priorities.</p>
            <div class="company-profile-meta">
                <span class="meta-chip"><strong><?= count($recommendedJobs ?? []) ?></strong> Suggested roles</span>
                <span class="meta-chip"><strong><?= strtoupper($source) ?></strong> Strategy source</span>
            </div>
        </div>
        <div class="page-board-actions">
            <a href="<?= base_url('jobs?tab=suggested') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-briefcase mr-1"></i> Suggested Jobs
            </a>
            <a href="<?= base_url('candidate/profile') ?>" class="btn btn-primary">
                <i class="fas fa-user-edit mr-1"></i> Update Profile
            </a>
        </div>
    </div>
</div>

<div class="container py-5">
    <style>
        .strategy-shell {
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr);
            gap: 24px;
            align-items: start;
        }
        .strategy-sidebar,
        .strategy-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }
        .strategy-sidebar {
            position: sticky;
            top: 110px;
            padding: 22px;
        }
        .strategy-main {
            display: grid;
            gap: 20px;
        }
        .strategy-card {
            padding: 24px;
        }
        .strategy-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
            margin-bottom: 14px;
        }
        .strategy-badge.is-ai {
            border-color: #bbf7d0;
            background: #dcfce7;
            color: #166534;
        }
        .strategy-title {
            font-size: 2rem;
            line-height: 1.15;
            color: #111827;
            margin-bottom: 10px;
        }
        .strategy-summary {
            color: #475569;
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 0;
        }
        .strategy-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 14px;
        }
        .strategy-list {
            margin: 0;
            padding-left: 18px;
            color: #475569;
        }
        .strategy-list li + li {
            margin-top: 8px;
        }
        .strategy-chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .strategy-chip {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid #dbeafe;
            background: #f8fbff;
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 700;
        }
        .strategy-sidebar h3 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 12px;
        }
        .strategy-sidebar-block + .strategy-sidebar-block {
            margin-top: 18px;
            padding-top: 18px;
            border-top: 1px solid #eef2f7;
        }
        .strategy-job {
            border: 1px solid #eef2f7;
            border-radius: 14px;
            padding: 16px;
            background: #fbfdff;
        }
        .strategy-job + .strategy-job {
            margin-top: 12px;
        }
        .strategy-job-title {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }
        .strategy-job-title a {
            color: inherit;
            text-decoration: none;
        }
        .strategy-job-title a:hover {
            color: #1d4ed8;
        }
        .strategy-job-meta {
            color: #64748b;
            font-size: .94rem;
            margin-bottom: 10px;
        }
        @media (max-width: 991.98px) {
            .strategy-shell {
                grid-template-columns: 1fr;
            }
            .strategy-sidebar {
                position: static;
            }
        }
    </style>

    <div class="strategy-shell">
        <aside class="strategy-sidebar">
            <div class="strategy-sidebar-block">
                <h3>Focus Now</h3>
                <p class="text-muted mb-0">Use this page to narrow your search, improve profile clarity, and apply to higher-fit roles instead of broad volume.</p>
            </div>
            <?php if (!empty($jobSearchStrategy['target_roles'])): ?>
                <div class="strategy-sidebar-block">
                    <h3>Target Roles</h3>
                    <div class="strategy-chip-list">
                        <?php foreach ((array) $jobSearchStrategy['target_roles'] as $role): ?>
                            <span class="strategy-chip"><?= esc($role) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <div class="strategy-sidebar-block">
                <a href="<?= base_url('jobs?tab=suggested') ?>" class="btn btn-outline-primary btn-sm btn-block">View Suggested Jobs</a>
                <a href="<?= base_url('candidate/profile') ?>" class="btn btn-outline-secondary btn-sm btn-block mt-2">Update Profile</a>
            </div>
        </aside>

        <div class="strategy-main">
            <section class="strategy-card">
                <span class="strategy-badge <?= $source === 'ai' ? 'is-ai' : '' ?>">
                    <?= $source === 'ai' ? 'AI-generated strategy' : 'Structured fallback strategy' ?>
                </span>
                <h2 class="strategy-title"><?= esc($jobSearchStrategy['title'] ?? 'Job Search Strategy Coach') ?></h2>
                <p class="strategy-summary"><?= esc($jobSearchStrategy['summary'] ?? 'Use this plan to make your next applications more selective and better aligned to your strongest opportunities.') ?></p>
            </section>

            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <section class="strategy-card h-100">
                        <div class="strategy-section-title">Priority Actions</div>
                        <ul class="strategy-list">
                            <?php foreach ((array) ($jobSearchStrategy['priority_actions'] ?? []) as $item): ?>
                                <li><?= esc($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                </div>
                <div class="col-lg-6">
                    <section class="strategy-card h-100">
                        <div class="strategy-section-title">Profile Fixes</div>
                        <ul class="strategy-list">
                            <?php foreach ((array) ($jobSearchStrategy['profile_fixes'] ?? []) as $item): ?>
                                <li><?= esc($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <section class="strategy-card h-100">
                        <div class="strategy-section-title">Application Strategy</div>
                        <ul class="strategy-list">
                            <?php foreach ((array) ($jobSearchStrategy['application_strategy'] ?? []) as $item): ?>
                                <li><?= esc($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                </div>
                <div class="col-lg-6">
                    <section class="strategy-card h-100">
                        <div class="strategy-section-title">Weekly Plan</div>
                        <ul class="strategy-list">
                            <?php foreach ((array) ($jobSearchStrategy['weekly_plan'] ?? []) as $item): ?>
                                <li><?= esc($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                </div>
            </div>

            <?php if (!empty($recommendedJobs)): ?>
                <section class="strategy-card">
                    <div class="strategy-section-title">Recommended Roles To Target</div>
                    <?php foreach ($recommendedJobs as $job): ?>
                        <div class="strategy-job">
                            <div class="strategy-job-title">
                                <a href="<?= base_url('job/' . (int) $job['id']) ?>"><?= esc($job['title'] ?? 'Untitled Role') ?></a>
                            </div>
                            <div class="strategy-job-meta">
                                <?= esc($job['company'] ?? 'Company') ?>
                                <span class="mx-2">&bull;</span>
                                <?= esc($job['location'] ?? 'N/A') ?>
                                <span class="mx-2">&bull;</span>
                                <?= (int) round($job['match_score'] ?? 0) ?>% match
                            </div>
                            <a href="<?= base_url('job/' . (int) $job['id']) ?>" class="btn btn-outline-primary btn-sm">Open Job</a>
                        </div>
                    <?php endforeach; ?>
                </section>
            <?php endif; ?>

            <?php if (!empty($jobSearchStrategy['watchouts'])): ?>
                <section class="strategy-card">
                    <div class="strategy-section-title">Watchouts</div>
                    <ul class="strategy-list">
                        <?php foreach ((array) ($jobSearchStrategy['watchouts'] ?? []) as $item): ?>
                            <li><?= esc($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= view('Layouts/candidate_footer') ?>
