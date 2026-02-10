<?= view('Layouts/candidate_header', ['title' => 'Dashboard']) ?>

<!-- Dashboard Content Start -->
<div class="featured-job-area feature-padding pt-5">
    <div class="container">
        <!-- Career Transition Suggestions -->
        <?php 
        $suggestions = session()->get('career_suggestions') ?? [];
        $activeSuggestions = array_filter($suggestions, function($s) {
            return isset($s['expires_at']) && time() < $s['expires_at'];
        });
        
        if (!empty($activeSuggestions)): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <h5><i class="fas fa-lightbulb"></i> Career Growth Suggestions</h5>
            <p>We noticed you're exploring roles that may require additional skills. Consider these career paths:</p>
            
            <div class="row">
            <?php foreach ($activeSuggestions as $suggestion): ?>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="text-primary"><i class="fas fa-arrow-right"></i> <?= esc($suggestion['job_title']) ?></h6>
                            <p class="small text-muted mb-2">Skills may not match your current profile</p>
                            <a href="<?= base_url('career-transition') ?>?target=<?= urlencode($suggestion['job_title']) ?>" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-route"></i> Explore Learning Path
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
            
            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="dismissAllSuggestions()">
                <i class="fas fa-times"></i> Dismiss All
            </button>
        </div>
        <?php endif; ?>
        
        <!-- Notification Alerts Section -->
       

        <!-- Pending Actions Section -->
        <?php if (!empty($pendingActions) && count($pendingActions) > 0): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-warning border-left-warning shadow">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                        <div>
                            <h5 class="mb-1">
                                <i class="fas fa-tasks"></i> You have <?= count($pendingActions) ?> pending action(s)
                            </h5>
                            <ul class="mb-0 pl-3">
                                <?php foreach ($pendingActions as $action): ?>
                                    <li>
                                        <strong><?= esc($action['title']) ?>:</strong> 
                                        <?= esc($action['description']) ?>
                                        <?php if (!empty($action['link'])): ?>
                                            <a href="<?= $action['link'] ?>" class="btn btn-sm btn-primary ml-2">
                                                <?= $action['button_text'] ?? 'Take Action' ?>
                                            </a>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-left-primary shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Total Applications
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">
                                    <?= $stats['total_applications'] ?? 0 ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-left-success shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Active Applications
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">
                                    <?= $stats['active_applications'] ?? 0 ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-left-warning shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                    Interviews Scheduled
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">
                                    <?= $stats['interviews_scheduled'] ?? 0 ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card stats-card border-left-info shadow h-100">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Average AI Score
                                </div>
                                <div class="h4 mb-0 font-weight-bold text-gray-800">
                                    <?php 
                                    $avgScore = $stats['average_ai_score'] ?? 0;
                                    echo $avgScore > 0 ? number_format($avgScore, 1) : 'N/A';
                                    ?>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-robot fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Applications Overview -->
        <div class="row">
            <div class="col-12">
                <div class="section-tittle mb-3 d-flex justify-content-between align-items-center">
                    <h3>Recent Applications</h3>
                    <a href="<?= base_url('candidate/applications') ?>" class="btn btn-primary">
                        <i class="fas fa-list"></i> View All Applications
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <?php if (empty($applications)): ?>
                <div class="col-12">
                    <div class="card shadow text-center py-5">
                        <div class="card-body">
                            <i class="fas fa-inbox fa-5x text-muted mb-4"></i>
                            <h4 class="text-muted">No Applications Yet</h4>
                            <p class="text-muted mb-4">You haven't applied to any jobs yet. Start exploring opportunities!</p>
                            <a href="<?= base_url('jobs') ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-search"></i> Browse Jobs
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="col-12">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Job Title</th>
                                            <th>Applied Date</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $recentApps = array_slice($applications, 0, 5);
                                        foreach ($recentApps as $application): 
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($application['job_title']) ?></strong>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($application['applied_at'])) ?></td>
                                            <td>
                                                <span class="badge badge-<?= getStatusBadgeColor($application['status']) ?>">
                                                    <?= ucwords(str_replace('_', ' ', $application['status'])) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('job/'.$application['job_id']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="fas fa-eye"></i> View Job
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if (count($applications) > 5): ?>
                                <div class="text-center mt-3">
                                    <a href="<?= base_url('candidate/applications') ?>" class="btn btn-outline-primary">
                                        View All <?= count($applications) ?> Applications
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Links -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="section-tittle mb-3">
                    <h3>Quick Actions</h3>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <a href="<?= base_url('jobs') ?>" class="quick-link-card">
                    <i class="fas fa-search fa-3x mb-3 text-primary"></i>
                    <h5>Browse Jobs</h5>
                    <p class="text-muted">Find new opportunities</p>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="<?= base_url('candidate/my-bookings') ?>" class="quick-link-card">
                    <i class="fas fa-calendar-alt fa-3x mb-3 text-success"></i>
                    <h5>My Interviews</h5>
                    <p class="text-muted">Manage interview schedules</p>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="<?= base_url('candidate/profile') ?>" class="quick-link-card">
                    <i class="fas fa-user fa-3x mb-3 text-info"></i>
                    <h5>My Profile</h5>
                    <p class="text-muted">Update your information</p>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="<?= base_url('career-transition') ?>" class="quick-link-card">
                    <i class="fas fa-rocket fa-3x mb-3 text-warning"></i>
                    <h5>Career Transition AI</h5>
                    <p class="text-muted">Plan your career growth</p>
                </a>
            </div>
            
        </div>
    </div>
</div>
<!-- Dashboard Content End -->



<?php
// Helper function for status badge colors
function getStatusBadgeColor($status) {
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

// Helper function for score badge colors
function getScoreBadgeColor($score) {
    if ($score >= 80) return 'success';
    if ($score >= 60) return 'warning';
    return 'danger';
}

// Helper function for time ago
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) return 'just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    
    return date('M d, Y', $timestamp);
}
?>

<script>
function dismissAllSuggestions() {
    fetch('<?= base_url('career-transition/dismiss-suggestion') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(() => {
        location.reload();
    });
}
</script>

<?= view('layouts/candidate_footer') ?>