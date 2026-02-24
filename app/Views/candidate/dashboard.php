<?= view('Layouts/candidate_header', ['title' => 'Dashboard']) ?>

<?php
$suggestions = session()->get('career_suggestions') ?? [];
$activeSuggestions = array_filter($suggestions, function ($s) {
    return isset($s['expires_at']) && time() < $s['expires_at'];
});
$avgScore = $stats['average_ai_score'] ?? 0;
$recentApps = array_slice($applications ?? [], 0, 5);
$topSuggestedJobs = $topSuggestedJobs ?? [];
?>

<div class="dashboard-jobboard">
    <section class="home-section section-hero overlay bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-lg-10 text-center">
                    <h1 class="text-white font-weight-bold mb-3">Welcome Back to Your Career Dashboard</h1>
                    <p class="lead text-white mb-4">Track your applications, interviews, and AI progress with the same HireMatrix visual style.</p>
                    <div>
                        <span class="metric-chip">Applications: <?= $stats['total_applications'] ?? 0 ?></span>
                        <span class="metric-chip">Active: <?= $stats['active_applications'] ?? 0 ?></span>
                        <span class="metric-chip">Interviews: <?= $stats['interviews_scheduled'] ?? 0 ?></span>
                        <span class="metric-chip">AI Score: <?= $avgScore > 0 ? number_format($avgScore, 1) : 'N/A' ?></span>
                    </div>
                    <div class="mt-4">
                        <a href="<?= base_url('jobs') ?>" class="btn btn-primary btn-lg mr-2">Browse Jobs</a>
                        <a href="<?= base_url('candidate/applications') ?>" class="btn btn-outline-white border-width-2 btn-lg">My Applications</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container dashboard-alerts">
        <?php if (!empty($activeSuggestions)): ?>
            <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                <h5><i class="fas fa-lightbulb"></i> Career Growth Suggestions</h5>
                <p class="mb-3">You explored roles that may require extra skills. Suggested paths:</p>
                <div class="row">
                    <?php foreach ($activeSuggestions as $suggestion): ?>
                        <div class="col-md-6 mb-2">
                            <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded border">
                                <div>
                                    <strong><?= esc($suggestion['job_title']) ?></strong>
                                    <div class="small text-muted">Skill gap detected for this target role.</div>
                                </div>
                                <a href="#" onclick="confirmCareerReset(event, '<?= urlencode($suggestion['job_title']) ?>')" class="btn btn-sm btn-outline-primary">
                                    Explore
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="dismissAllSuggestions()">Dismiss All</button>
            </div>
        <?php endif; ?>

        <?php if (!empty($pendingActions) && count($pendingActions) > 0): ?>
            <div class="alert alert-warning shadow-sm" role="alert">
                <h5 class="mb-2"><i class="fas fa-tasks"></i> You have <?= count($pendingActions) ?> pending action(s)</h5>
                <ul class="mb-0 pl-3">
                    <?php foreach ($pendingActions as $action): ?>
                        <li class="mb-2">
                            <strong><?= esc($action['title']) ?>:</strong> <?= esc($action['description']) ?>
                            <?php if (!empty($action['link'])): ?>
                                <a href="<?= $action['link'] ?>" class="btn btn-sm btn-primary ml-2"><?= $action['button_text'] ?? 'Take Action' ?></a>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <section class="py-5 bg-image overlay-primary fixed overlay" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="next">
        <div class="container">
            <div class="row mb-5 justify-content-center">
                <div class="col-md-8 text-center">
                    <h2 class="section-title mb-2 text-white">Your Dashboard Stats</h2>
                    <p class="lead text-white">Live overview of your hiring pipeline and profile performance.</p>
                </div>
            </div>
            <div class="row pb-0 block__19738 section-counter">
                <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <strong class="number"><?= $stats['total_applications'] ?? 0 ?></strong>
                    </div>
                    <span class="caption">Total Applications</span>
                </div>
                <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <strong class="number"><?= $stats['active_applications'] ?? 0 ?></strong>
                    </div>
                    <span class="caption">Active Applications</span>
                </div>
                <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <strong class="number"><?= $stats['interviews_scheduled'] ?? 0 ?></strong>
                    </div>
                    <span class="caption">Interviews Scheduled</span>
                </div>
                <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <strong class="number"><?= $avgScore > 0 ? number_format($avgScore, 1) : 'N/A' ?></strong>
                    </div>
                    <span class="caption">Average AI Score</span>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section">
        <div class="container">
            <div class="row mb-5 justify-content-center">
                <div class="col-md-7 text-center">
                    <h2 class="section-title mb-2">Top Suggested Jobs</h2>
                    <p class="text-muted">Best matches based on your profile and behavior.</p>
                </div>
            </div>

            <?php if (!empty($topSuggestedJobs)): ?>
                <ul class="job-listings mb-4">
                    <?php foreach ($topSuggestedJobs as $job): ?>
                        <?php
                            $score = (int) round($job['match_score'] ?? 0);
                            $initial = strtoupper(substr($job['company'] ?? 'J', 0, 1));
                        ?>
                        <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center">
                            <a href="<?= base_url('job/' . $job['id']) ?>"></a>
                            <div class="job-listing-logo">
                                <?php if (!empty($job['company_logo'])): ?>
                                    <img src="<?= base_url($job['company_logo']) ?>" alt="<?= esc($job['company'] ?? 'Company') ?>" class="img-fluid">
                                <?php else: ?>
                                    <div class="d-flex align-items-center justify-content-center rounded bg-light text-muted" style="width: 90px; height: 90px; font-size: 28px;">
                                        <?= esc($initial) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
                                <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
                                    <h2><?= esc($job['title'] ?? 'Untitled Role') ?></h2>
                                    <strong><?= esc($job['company'] ?? 'Company') ?></strong>
                                </div>
                                <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
                                    <span class="icon-room"></span> <?= esc($job['location'] ?? 'N/A') ?>
                                </div>
                                <div class="job-listing-meta">
                                    <span class="badge badge-success"><?= $score ?>% Match</span>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="text-center mb-5">
                    <a href="<?= base_url('jobs?tab=suggested') ?>" class="btn btn-outline-primary btn-lg">View More Suggestions</a>
                </div>
            <?php else: ?>
                <div class="text-center bg-white rounded shadow-sm p-4 mb-5">
                    <p class="mb-3 text-muted">No suggestions yet. Add more skills and interests to improve recommendations.</p>
                    <a href="<?= base_url('candidate/profile') ?>" class="btn btn-outline-primary">Update Profile</a>
                </div>
            <?php endif; ?>

            <div class="row mb-5 justify-content-center">
                <div class="col-md-7 text-center">
                    <h2 class="section-title mb-2">Recent Applications</h2>
                </div>
            </div>

            <?php if (empty($applications)): ?>
                <div class="text-center bg-white rounded shadow-sm p-5 mb-4">
                    <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No Applications Yet</h4>
                    <p class="text-muted mb-4">Start exploring opportunities and submit your first application.</p>
                    <a href="<?= base_url('jobs') ?>" class="btn btn-primary btn-lg">Browse Jobs</a>
                </div>
            <?php else: ?>
                <ul class="job-listings mb-5">
                    <?php foreach ($recentApps as $application): ?>
                        <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center">
                            <a href="<?= base_url('job/' . $application['job_id']) ?>" target="_blank"></a>
                            <div class="job-listing-logo">
                                <div class="d-flex align-items-center justify-content-center rounded bg-light text-muted" style="width: 90px; height: 90px; font-size: 28px;">
                                    <span class="icon-briefcase"></span>
                                </div>
                            </div>
                            <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
                                <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
                                    <h2><?= esc($application['job_title']) ?></h2>
                                    <strong>Applied <?= date('M d, Y', strtotime($application['applied_at'])) ?></strong>
                                </div>
                                <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
                                    <?= esc($application['company_name'] ?? '-') ?>
                                </div>
                                <div class="job-listing-meta">
                                    <span class="badge status-badge badge-<?= getStatusBadgeColor($application['status']) ?>">
                                        <?= ucwords(str_replace('_', ' ', $application['status'])) ?>
                                    </span>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div class="text-center mb-4">
                <a href="<?= base_url('candidate/applications') ?>" class="btn btn-primary btn-lg">View All Applications</a>
            </div>
        </div>
    </section>

    <section class="site-section py-4 bg-light">
        <div class="container">
            <div class="row justify-content-center mb-4">
                <div class="col-md-7 text-center">
                    <h2 class="section-title mb-2">Quick Actions</h2>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-3 mb-4">
                    <a href="<?= base_url('jobs') ?>" class="action-card d-block text-center">
                        <span class="icon-search d-inline-block mb-3" style="font-size:34px;"></span>
                        <h5>Browse Jobs</h5>
                        <p class="mb-0 text-muted">Find matching roles.</p>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <a href="<?= base_url('candidate/my-bookings') ?>" class="action-card d-block text-center">
                        <span class="icon-calendar d-inline-block mb-3" style="font-size:34px;"></span>
                        <h5>My Interviews</h5>
                        <p class="mb-0 text-muted">Check schedules.</p>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <a href="<?= base_url('candidate/profile') ?>" class="action-card d-block text-center">
                        <span class="icon-user d-inline-block mb-3" style="font-size:34px;"></span>
                        <h5>My Profile</h5>
                        <p class="mb-0 text-muted">Update profile data.</p>
                    </a>
                </div>
                <div class="col-md-6 col-lg-3 mb-4">
                    <a href="<?= base_url('career-transition') ?>" class="action-card d-block text-center">
                        <span class="icon-trending_up d-inline-block mb-3" style="font-size:34px;"></span>
                        <h5>Career Transition AI</h5>
                        <p class="mb-0 text-muted">Build your path.</p>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
function getStatusBadgeColor($status)
{
    $colors = [
        'applied' => 'warning',
        'ai_interview_started' => 'info',
        'ai_interview_completed' => 'primary',
        'shortlisted' => 'success',
        'rejected' => 'danger',
        'interview_slot_booked' => 'warning',
        'selected' => 'success'
    ];

    return $colors[$status] ?? 'secondary';
}
?>

<?= view('Layouts/candidate_footer') ?>
