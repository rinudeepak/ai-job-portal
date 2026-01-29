<?= view('layouts/recruiter_header', ['title' => 'Admin Dashboard']) ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-tachometer-alt"></i> Recruitment Dashboard</h2>
            <p class="text-muted">Overview of recruitment metrics and analytics</p>
        </div>
        <div>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary dropdown-toggle" data-toggle="dropdown">
                    <i class="fas fa-download"></i> Export
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="<?= base_url('recruiter/dashboard/export-excel?type=overview') ?>">
                        <i class="fas fa-file-excel"></i> Overview Report
                    </a>
                    <a class="dropdown-item" href="<?= base_url('recruiter/dashboard/export-excel?type=funnel') ?>">
                        <i class="fas fa-filter"></i> Funnel Analysis
                    </a>
                    <a class="dropdown-item" href="<?= base_url('recruiter/dashboard/export-excel?type=detailed') ?>">
                        <i class="fas fa-list"></i> Detailed Report
                    </a>
                </div>
            </div>
            <a href="<?= base_url('recruiter/dashboard/leaderboard') ?>" class="btn btn-primary">
                <i class="fas fa-trophy"></i> View Leaderboard
            </a>
        </div>
    </div>

    <!-- Pending Actions Alert -->
    <?php if (array_sum($pendingActions) > 0): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong><i class="fas fa-exclamation-triangle"></i> Pending Actions:</strong>
        <?php if ($pendingActions['pending_screening'] > 0): ?>
            <span class="badge badge-warning"><?= $pendingActions['pending_screening'] ?></span> applications to screen, 
        <?php endif; ?>
        <?php if ($pendingActions['ai_interviews_to_review'] > 0): ?>
            <span class="badge badge-info"><?= $pendingActions['ai_interviews_to_review'] ?></span> AI interviews to review, 
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
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Applications
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($funnel['total_applications']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Selected
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                <?= $funnel['selected'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Active Jobs
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                <?= $jobStats['active_jobs'] ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-briefcase fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Conversion Rate
                            </div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">
                                <?= $conversionMetrics['overall_conversion'] ?? 0 ?>%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Candidate Funnel -->
        <div class="col-xl-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Candidate Funnel Overview</h6>
                    <a href="<?= base_url('recruiter/dashboard/export-excel?type=funnel') ?>" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download"></i> Export
                    </a>
                </div>
                <div class="card-body">
                    <div class="funnel-chart">
                        <div class="funnel-stage" style="width: 100%;" data-count="<?= $funnel['total_applications'] ?>">
                            <div class="funnel-label">
                                <span class="stage-name">Total Applications</span>
                                <span class="stage-count"><?= $funnel['total_applications'] ?></span>
                            </div>
                        </div>
                        
                        <div class="funnel-stage" style="width: <?= $funnel['total_applications'] > 0 ? ($funnel['screening'] / $funnel['total_applications'] * 100) : 0 ?>%;" data-count="<?= $funnel['screening'] ?>">
                            <div class="funnel-label">
                                <span class="stage-name">Screening</span>
                                <span class="stage-count"><?= $funnel['screening'] ?></span>
                            </div>
                        </div>
                        
                        <div class="funnel-stage" style="width: <?= $funnel['total_applications'] > 0 ? ($funnel['ai_interview_completed'] / $funnel['total_applications'] * 100) : 0 ?>%;" data-count="<?= $funnel['ai_interview_completed'] ?>">
                            <div class="funnel-label">
                                <span class="stage-name">AI Interview Completed</span>
                                <span class="stage-count"><?= $funnel['ai_interview_completed'] ?></span>
                            </div>
                        </div>
                        
                        <div class="funnel-stage" style="width: <?= $funnel['total_applications'] > 0 ? ($funnel['shortlisted'] / $funnel['total_applications'] * 100) : 0 ?>%;" data-count="<?= $funnel['shortlisted'] ?>">
                            <div class="funnel-label">
                                <span class="stage-name">Shortlisted</span>
                                <span class="stage-count"><?= $funnel['shortlisted'] ?></span>
                            </div>
                        </div>
                        
                        <div class="funnel-stage" style="width: <?= $funnel['total_applications'] > 0 ? ($funnel['hr_interview_completed'] / $funnel['total_applications'] * 100) : 0 ?>%;" data-count="<?= $funnel['hr_interview_completed'] ?>">
                            <div class="funnel-label">
                                <span class="stage-name">HR Interview Completed</span>
                                <span class="stage-count"><?= $funnel['hr_interview_completed'] ?></span>
                            </div>
                        </div>
                        
                        <div class="funnel-stage" style="width: <?= $funnel['total_applications'] > 0 ? ($funnel['selected'] / $funnel['total_applications'] * 100) : 0 ?>%;" data-count="<?= $funnel['selected'] ?>">
                            <div class="funnel-label">
                                <span class="stage-name">Selected</span>
                                <span class="stage-count"><?= $funnel['selected'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stage Time Analytics -->
        <div class="col-xl-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Average Time in Stage</h6>
                </div>
                <div class="card-body">
                    <?php foreach ($stageTimeAnalytics as $stage => $days): ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-sm"><?= ucwords(str_replace('_', ' ', $stage)) ?></span>
                                <span class="text-sm font-weight-bold"><?= $days ?> days</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar <?= $days > 7 ? 'bg-danger' : ($days > 3 ? 'bg-warning' : 'bg-success') ?>" 
                                     style="width: <?= min($days / 14 * 100, 100) ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php if (empty($stageTimeAnalytics)): ?>
                        <p class="text-muted text-center">No data available</p>
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
                                        <span class="badge badge-<?= ($conversionMetrics['application_to_ai_interview'] ?? 0) > 50 ? 'success' : 'warning' ?>">
                                            <?= $conversionMetrics['application_to_ai_interview'] ?? 0 ?>%
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>AI Interview → Shortlist</td>
                                    <td class="text-right">
                                        <span class="badge badge-<?= ($conversionMetrics['ai_interview_to_shortlist'] ?? 0) > 40 ? 'success' : 'warning' ?>">
                                            <?= $conversionMetrics['ai_interview_to_shortlist'] ?? 0 ?>%
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Shortlist → HR Interview</td>
                                    <td class="text-right">
                                        <span class="badge badge-<?= ($conversionMetrics['shortlist_to_hr_interview'] ?? 0) > 60 ? 'success' : 'warning' ?>">
                                            <?= $conversionMetrics['shortlist_to_hr_interview'] ?? 0 ?>%
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>HR Interview → Selection</td>
                                    <td class="text-right">
                                        <span class="badge badge-<?= ($conversionMetrics['hr_interview_to_selection'] ?? 0) > 30 ? 'success' : 'warning' ?>">
                                            <?= $conversionMetrics['hr_interview_to_selection'] ?? 0 ?>%
                                        </span>
                                    </td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td>Overall Conversion</td>
                                    <td class="text-right">
                                        <span class="badge badge-primary badge-lg">
                                            <?= $conversionMetrics['overall_conversion'] ?? 0 ?>%
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
                                    <th>Actions</th>
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
                                            <td><?= date('M d, Y', strtotime($app['created_at'])) ?></td>
                                            <td>
                                                <a href="<?= base_url('recruiter/applications/view/' . $app['id']) ?>" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
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

<style>
/* Funnel Chart Styles */
.funnel-chart {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px 0;
}

.funnel-stage {
    background: linear-gradient(to right, #4e73df, #224abe);
    color: white;
    padding: 15px;
    margin: 5px 0;
    min-width: 200px;
    border-radius: 5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.funnel-stage:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.funnel-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-weight: 600;
}

.stage-count {
    background: rgba(255,255,255,0.2);
    padding: 3px 10px;
    border-radius: 15px;
    font-size: 0.9em;
}

/* Card Styles */
.border-left-primary {
    border-left: 0.25rem solid #4e73df!important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a!important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc!important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e!important;
}

.text-gray-300 {
    color: #dddfeb!important;
}

.text-gray-800 {
    color: #5a5c69!important;
}

.bg-gradient-primary {
    background: linear-gradient(to right, #4e73df, #224abe);
}

.card {
    border: none;
    border-radius: 0.5rem;
}

.badge-lg {
    font-size: 1rem;
    padding: 0.5rem 0.75rem;
}
</style>

<?= view('layouts/recruiter_footer') ?>