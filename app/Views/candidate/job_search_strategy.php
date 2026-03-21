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

<div class="strategy-jobboard">
<div class="container">
    <div class="page-board-header page-board-header-tight">
        <div class="page-board-copy">
            <span class="page-board-kicker"><i class="fas fa-compass"></i> Search guidance</span>
            <h1 class="page-board-title">Job Search Strategy Coach</h1>
            <p class="page-board-subtitle">Turn your profile into a sharper search plan with target roles, action steps, and weekly priorities.</p>
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
</div>

<?= view('Layouts/candidate_footer') ?>
