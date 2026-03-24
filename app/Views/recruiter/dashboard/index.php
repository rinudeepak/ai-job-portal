<?= view('Layouts/recruiter_header', ['title' => 'Admin Dashboard', 'showHero' => false]) ?>

<div class="recruiter-dashboard-jobboard">
    <?php
    $applicationsUrl = base_url('recruiter/jobs');
    $jobsUrl = base_url('recruiter/jobs');
    $conversionUrl = base_url('recruiter/dashboard') . '#conversion-metrics';
    $bookingsUrl = base_url('recruiter/slots/bookings');
    $postJobUrl = base_url('recruiter/post_job');
    $slotsUrl = base_url('recruiter/slots');
    $leaderboardUrl = base_url('recruiter/dashboard/leaderboard');
    $formatStageLabel = static function ($raw): string {
        $normalized = strtolower(trim((string) $raw));
        $map = [
            'applied' => 'Applied',
            'shortlisted (recruiter override)' => 'Shortlisted',
            'rejected (recruiter override)' => 'Rejected',
            'shortlisted' => 'Shortlisted',
            'rejected' => 'Rejected',
            'interview slot booked' => 'Interview Slot Booked',
            'selected' => 'Selected',
        ];

        return $map[$normalized] ?? ucwords(str_replace('_', ' ', (string) $raw));
    };
    $formatRate = static function ($value): string {
        if ($value === null || $value === '') {
            return 'N/A';
        }

        return number_format((float) $value, 1) . '%';
    };
    $rateClass = static function ($value, float $goodThreshold): string {
        if ($value === null || $value === '') {
            return 'secondary';
        }

        return ((float) $value) >= $goodThreshold ? 'success' : 'warning';
    };
    ?>
    <section class="recruiter-dashboard-hero">
        <div class="container">
            <div class="recruiter-dashboard-hero-grid">
                <div class="recruiter-dashboard-hero-copy">
                    <div class="status-pill recruiter-dashboard-status-pill">
                        <i class="fas fa-arrow-trend-up" style="color: #ff7b2a;"></i>
                        <?= number_format((int) $funnel['total_applications']) ?> Active Candidates
                    </div>

                    <h1 class="recruiter-dashboard-hero-title">
                        Find Your Next Great <span class="gradient-text">Hire</span>
                    </h1>

                    <p class="recruiter-dashboard-hero-subtitle">
                        Access a curated pool of talented professionals matched to your needs. Smart candidate matching powered by AI to fill your roles faster.
                    </p>

                </div>

                <div class="recruiter-dashboard-hero-aside">
                    <div class="recruiter-dashboard-hero-panel">
                        <div class="recruiter-dashboard-hero-panel-icon">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h3>Post a New Job</h3>
                        <p>Create a role, define the requirements, and start receiving qualified applicants faster.</p>
                        <a href="<?= $postJobUrl ?>" class="btn btn-primary w-100 mt-3">
                            <i class="fas fa-plus"></i> Post Job
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container recruiter-dashboard-main">
        <?php if (!empty($reminders ?? [])): ?>
        <div class="recruiter-reminders-grid mb-4">
            <?php foreach ($reminders as $reminder): ?>
                <a href="<?= esc($reminder['link'] ?? '#') ?>" class="recruiter-reminder-card tone-<?= esc($reminder['tone'] ?? 'primary') ?>">
                    <div class="recruiter-reminder-icon">
                        <i class="<?= esc($reminder['icon'] ?? 'fas fa-bell') ?>"></i>
                    </div>
                    <div class="recruiter-reminder-copy">
                        <div class="recruiter-reminder-title"><?= esc($reminder['label'] ?? '') ?></div>
                        <div class="recruiter-reminder-text"><?= esc($reminder['description'] ?? '') ?></div>
                    </div>
                    <div class="recruiter-reminder-action">
                        View <i class="fas fa-arrow-right ms-1"></i>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($noJobs)): ?>
        <div class="card shadow mb-4">
            <div class="card-body p-4 text-center">
                <h4 class="mb-2">No jobs posted yet</h4>
                <p class="text-muted mb-3">Post your first job to start receiving applications and build your hiring pipeline.</p>
                <a href="<?= $postJobUrl ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Post Your First Job
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Pending Actions Alert -->
        <?php if (empty($noJobs) && array_sum($pendingActions) > 0): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-triangle"></i> Pending Actions:</strong>
            <?php if ($pendingActions['pending_screening'] > 0): ?>
                <span class="badge badge-warning"><?= $pendingActions['pending_screening'] ?></span> applications to screen, 
            <?php endif; ?>
            <?php if ($pendingActions['ai_interviews_to_review'] > 0): ?>
                <span class="badge badge-info"><?= $pendingActions['ai_interviews_to_review'] ?></span> screening reviews pending, 
            <?php endif; ?>
            <?php if ($pendingActions['hr_interviews_today'] > 0): ?>
                <span class="badge badge-primary"><?= $pendingActions['hr_interviews_today'] ?></span> HR interviews today
            <?php endif; ?>
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
        <?php endif; ?>

        <!-- Quick Stats Row -->
        <div class="row mb-4">
        <div class="col-xl col-md-6 mb-3">
            <a href="<?= $applicationsUrl ?>" class="dashboard-stat-link">
            <div class="card recruiter-stat-card recruiter-stat-applications shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1 dashboard-metric-title">
                                Total Applications
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($funnel['total_applications']) ?>
                            </div>
                            <small class="text-muted d-block">Across all active jobs</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

        <div class="col-xl col-md-6 mb-3">
            <a href="<?= $jobsUrl ?>" class="dashboard-stat-link">
            <div class="card recruiter-stat-card recruiter-stat-openjobs shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1 dashboard-metric-title">
                                Open Jobs
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                <?= $jobStats['active_jobs'] ?>
                            </div>
                            <small class="text-muted d-block">Currently hiring</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

        <div class="col-xl col-md-6 mb-3">
            <a href="<?= $conversionUrl ?>" class="dashboard-stat-link">
            <div class="card recruiter-stat-card recruiter-stat-conversion shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1 dashboard-metric-title">
                                Conversion Rate
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                <?= $conversionMetrics['overall_conversion'] ?? 0 ?>%
                            </div>
                            <small class="text-muted d-block">Pipeline efficiency</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-pie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>

        <div class="col-xl col-md-6 mb-3">
            <a href="<?= $bookingsUrl ?>" class="dashboard-stat-link">
            <div class="card recruiter-stat-card recruiter-stat-bookings shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1 dashboard-metric-title">
                                Interview Bookings
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($jobStats['interview_bookings'] ?? 0) ?>
                            </div>
                            <small class="text-muted d-block">HR rounds scheduled</small>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>
        </div>

        <?php if (empty($noJobs)): ?>
        <div class="row mb-4 recruiter-action-center">
        <div class="col-xl-8 mb-3">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-bolt"></i> Action Center</h6>
                </div>
                <div class="card-body p-0">
                    <?php $hasActionCenterItems = ((int) ($pendingActions['pending_screening'] ?? 0) > 0) || ((int) ($pendingActions['ai_interviews_to_review'] ?? 0) > 0) || ((int) ($pendingActions['hr_interviews_today'] ?? 0) > 0); ?>
                    <?php if ($hasActionCenterItems): ?>
                        <?php if ((int) ($pendingActions['pending_screening'] ?? 0) > 0): ?>
                            <a href="<?= $jobsUrl ?>" class="action-item-link">
                                <div class="action-item-label">
                                    <strong>Screen New Applications</strong>
                                    <small class="text-muted d-block">Review and shortlist incoming candidates.</small>
                                </div>
                                <span class="badge badge-warning"><?= (int) ($pendingActions['pending_screening'] ?? 0) ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if ((int) ($pendingActions['ai_interviews_to_review'] ?? 0) > 0): ?>
                            <a href="<?= $jobsUrl ?>" class="action-item-link">
                                <div class="action-item-label">
                                    <strong>Review AI Interview Results</strong>
                                    <small class="text-muted d-block">Take recruiter decisions for AI-cleared candidates.</small>
                                </div>
                                <span class="badge badge-info"><?= (int) ($pendingActions['ai_interviews_to_review'] ?? 0) ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if ((int) ($pendingActions['hr_interviews_today'] ?? 0) > 0): ?>
                            <a href="<?= $bookingsUrl ?>" class="action-item-link">
                                <div class="action-item-label">
                                    <strong>Interviews Today</strong>
                                    <small class="text-muted d-block">Track today&#39;s booked interviews and status.</small>
                                </div>
                                <span class="badge badge-primary"><?= (int) ($pendingActions['hr_interviews_today'] ?? 0) ?></span>
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="recruiter-action-center-empty">
                            <div class="recruiter-action-center-empty-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <h6 class="mb-2">Everything is up to date</h6>
                            <p class="text-muted mb-3">No pending screenings or interviews right now. You&#39;re all caught up.</p>
                            <a href="<?= $jobsUrl ?>" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-briefcase mr-1"></i> Review Jobs
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-xl-4 mb-3">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-link"></i> Quick Links</h6>
                </div>
                <div class="card-body recruiter-quick-links">
                    <a href="<?= $jobsUrl ?>" class="btn btn-outline-secondary btn-block"><i class="fas fa-briefcase"></i> My Jobs</a>
                    <a href="<?= $postJobUrl ?>" class="btn btn-outline-secondary btn-block"><i class="fas fa-plus-circle"></i> Post New Job</a>
                    <a href="<?= $slotsUrl ?>" class="btn btn-outline-secondary btn-block"><i class="fas fa-calendar-alt"></i> Interview Slots</a>
                    <a href="<?= $bookingsUrl ?>" class="btn btn-outline-secondary btn-block"><i class="fas fa-calendar-check"></i> Interview Bookings</a>
                    <a href="<?= $leaderboardUrl ?>" class="btn btn-outline-secondary btn-block"><i class="fas fa-trophy"></i> Leaderboard</a>
                </div>
            </div>
        </div>
        </div>
        <?php endif; ?>

        <!-- Main Content Row -->
        <div class="row recruiter-pipeline-row" id="conversion-metrics">
        <!-- Recruitment Pipeline -->
        <div class="col-xl-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-chart-bar"></i> Recruitment Pipeline</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="pipeline-stat">
                                <div class="stat-icon bg-primary">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h3><?= number_format($funnel['total_applications']) ?></h3>
                                <p class="text-muted mb-0">Applications</p>
                            </div>
                        </div>
                        <?php $screeningCompleted = (int) ($funnel['shortlisted'] ?? 0) + (int) ($funnel['rejected'] ?? 0); ?>
                        <div class="col-3">
                            <div class="pipeline-stat">
                                <div class="stat-icon bg-info">
                                    <i class="fas fa-robot"></i>
                                </div>
                                <h3><?= number_format($screeningCompleted) ?></h3>
                                <p class="text-muted mb-0">Screening Completed</p>
                                <small class="text-success"><i class="fas fa-arrow-right"></i> <?= $funnel['total_applications'] > 0 ? round(($screeningCompleted / $funnel['total_applications']) * 100, 1) : 0 ?>% from applications</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="pipeline-stat">
                                <div class="stat-icon bg-success">
                                    <i class="fas fa-star"></i>
                                </div>
                                <h3><?= number_format($funnel['shortlisted']) ?></h3>
                                <p class="text-muted mb-0">Shortlisted</p>
                                <small class="text-success"><i class="fas fa-arrow-right"></i> <?= $screeningCompleted > 0 ? round(($funnel['shortlisted'] / $screeningCompleted) * 100, 1) : 0 ?>% from screened</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="pipeline-stat">
                                <div class="stat-icon bg-warning">
                                    <i class="fas fa-calendar-check"></i>
                                </div>
                                <h3><?= number_format($funnel['interview_slot_booked']) ?></h3>
                                <p class="text-muted mb-0">HR Interviews</p>
                                <small class="text-success"><i class="fas fa-arrow-right"></i> <?= $funnel['shortlisted'] > 0 ? round(($funnel['interview_slot_booked'] / $funnel['shortlisted']) * 100, 1) : 0 ?>% from shortlisted</small>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-light mt-3 mb-0">
                        <small class="text-muted"><i class="fas fa-info-circle"></i> Each stage shows conversion rate from previous stage</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stage Time Analytics -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-clock"></i> Stage Time Analytics</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($stageTimeAnalytics)): ?>
                        <?php
                        $stageAnalytics = $stageTimeAnalytics;
                        $maxHours = max(array_column($stageAnalytics, 'hours'));
                        $avgDays = array_sum(array_map(static function ($stage) {
                            return (float) ($stage['days'] ?? 0);
                        }, $stageAnalytics)) / max(count($stageAnalytics), 1);
                        $slowestStage = $stageAnalytics[0];
                        $fastestStage = $stageAnalytics[0];
                        foreach ($stageAnalytics as $stageItem) {
                            if ((float) ($stageItem['hours'] ?? 0) > (float) ($slowestStage['hours'] ?? 0)) {
                                $slowestStage = $stageItem;
                            }
                            if ((float) ($stageItem['hours'] ?? 0) < (float) ($fastestStage['hours'] ?? 0)) {
                                $fastestStage = $stageItem;
                            }
                        }
                        ?>
                        <div class="stage-insight-strip">
                            <div class="stage-insight-item">
                                <span class="stage-insight-label">Average stage time</span>
                                <strong><?= number_format($avgDays, 1) ?> days</strong>
                            </div>
                            <div class="stage-insight-item">
                                <span class="stage-insight-label">Slowest stage</span>
                                <strong><?= esc($formatStageLabel($slowestStage['stage'])) ?></strong>
                            </div>
                            <div class="stage-insight-item">
                                <span class="stage-insight-label">Fastest stage</span>
                                <strong><?= esc($formatStageLabel($fastestStage['stage'])) ?></strong>
                            </div>
                        </div>

                        <div class="stage-time-list">
                            <?php foreach (array_slice($stageAnalytics, 0, 3) as $stage): ?>
                                <?php $isBottleneck = $stage['hours'] > ($maxHours * 0.7); ?>
                                <div class="stage-time-item mb-3" data-aos="fade-left">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="stage-name-label"><?= esc($formatStageLabel($stage['stage'])) ?></span>
                                            <?php if ($isBottleneck): ?>
                                                <span class="badge badge-danger ml-1" title="Potential bottleneck">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-right">
                                            <strong class="text-primary"><?= $stage['days'] ?> days</strong>
                                            <small class="text-muted d-block"><?= $stage['hours'] ?>h</small>
                                        </div>
                                    </div>
                                    <div class="progress" style="height: 10px;">
                                        <div class="progress-bar <?= $stage['days'] > 7 ? 'bg-danger' : ($stage['days'] > 3 ? 'bg-warning' : 'bg-success') ?>"
                                             style="width: <?= min(($stage['hours'] / $maxHours) * 100, 100) ?>%"
                                             data-toggle="tooltip"
                                             title="<?= $stage['count'] ?> candidates">
                                        </div>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fas fa-users"></i> <?= $stage['count'] ?> candidates
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No stage data available yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>

        <!-- Conversion Metrics & Top Jobs Row -->
        <div class="row">
        <!-- Conversion Metrics -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Conversion Metrics</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Stage Transition</th>
                                    <th class="text-right">Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Application → AI Interview</td>
                                    <td class="text-right">
                                        <span class="badge badge-<?= $rateClass($conversionMetrics['application_to_ai_interview'] ?? null, 50) ?>">
                                            <?= $formatRate($conversionMetrics['application_to_ai_interview'] ?? null) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>AI Interview → Shortlist</td>
                                    <td class="text-right">
                                        <span class="badge badge-<?= $rateClass($conversionMetrics['ai_interview_to_shortlist'] ?? null, 40) ?>">
                                            <?= $formatRate($conversionMetrics['ai_interview_to_shortlist'] ?? null) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Shortlist → HR Interview</td>
                                    <td class="text-right">
                                        <span class="badge badge-<?= $rateClass($conversionMetrics['shortlist_to_hr_interview'] ?? null, 60) ?>">
                                            <?= $formatRate($conversionMetrics['shortlist_to_hr_interview'] ?? null) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>HR Interview → Selection</td>
                                    <td class="text-right">
                                        <span class="badge badge-<?= $rateClass($conversionMetrics['hr_interview_to_selection'] ?? null, 30) ?>">
                                            <?= $formatRate($conversionMetrics['hr_interview_to_selection'] ?? null) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td>Overall Conversion</td>
                                    <td class="text-right">
                                        <span class="badge badge-primary badge-lg">
                                            <?= number_format((float) ($conversionMetrics['overall_conversion'] ?? 0), 1) ?>%
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Jobs by Applications -->
        <div class="col-xl-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top Jobs by Applications</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($topJobs)): ?>
                        <?php 
                        $maxCount = max(array_column($topJobs, 'application_count'));
                        foreach ($topJobs as $job): 
                            $percentage = $maxCount > 0 ? ($job['application_count'] / $maxCount * 100) : 0;
                        ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="font-weight-bold"><?= esc($job['title']) ?></span>
                                    <span class="badge badge-primary"><?= $job['application_count'] ?></span>
                                </div>
                                <div class="progress" style="height: 12px;">
                                    <div class="progress-bar bg-gradient-primary" style="width: <?= $percentage ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted text-center">No job data available</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>

        <!-- Recent Applications -->
        <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Applications</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Candidate</th>
                                    <th>Job</th>
                                    <th>Status</th>
                                    <th>Applied</th>
                                    <!-- <th>Actions</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentApplications)): ?>
                                    <?php foreach ($recentApplications as $app): ?>
                                        <tr>
                                            <td>#<?= $app['id'] ?></td>
                                            <td>
                                                <strong><?= esc($app['candidate_name']) ?></strong>
                                            </td>
                                            <td><?= esc($app['job_title']) ?></td>
                                            <td>
                                                <?php
                                                $statusColors = [
                                                    'pending' => 'warning',
                                                    'shortlisted' => 'info',
                                                    'selected' => 'success',
                                                    'rejected' => 'danger'
                                                ];
                                                $color = $statusColors[$app['status']] ?? 'secondary';
                                                ?>
                                                <span class="badge badge-<?= $color ?>">
                                                    <?= ucwords(str_replace('_', ' ', $app['status'])) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($app['applied_at'])) ?></td>
                                            <!-- <td>
                                                <a href="<?= base_url('recruiter/applications/view/' . $app['id']) ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td> -->
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No recent applications</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>



<?= view('Layouts/recruiter_footer') ?>
