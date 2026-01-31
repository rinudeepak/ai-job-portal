<?= view('Layouts/candidate_header', ['title' => 'Dashboard']) ?>



<!-- Online CV Area Start -->
<!-- <div class="online-cv cv-bg section-overly pt-90 pb-120" data-background="assets/img/gallery/cv_bg.jpg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="cv-caption text-center">
                    <p class="pera2">Welcome, <?= session('user_name') ?></p>
                </div>
            </div>
        </div>
    </div>
</div> -->
<!-- Online CV Area End-->
 <!-- Notification Alerts Section -->



<!-- Online CV Area Start -->
<div class="online-cv cv-bg section-overly pt-90 pb-120" data-background="assets/img/gallery/cv_bg.jpg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="cv-caption text-center">
                    <h2 class="text-white mb-3">Welcome Back, <?= session('user_name') ?></h2>
                    <p class="pera2">Track your application progress and complete pending actions</p>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Online CV Area End-->

<!-- Dashboard Content Start -->
<div class="featured-job-area feature-padding">
    <div class="container">
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
                <div class="section-tittle mb-3">
                    <h3>My Applications</h3>
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
                <?php foreach ($applications as $application): ?>
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="card application-card shadow h-100">
                        <div class="card-header bg-white border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <a href="<?= base_url('jobs/view/' . $application['job_id']) ?>" class="text-dark">
                                        <?= esc($application['job_title']) ?>
                                    </a>
                                </h5>
                                <span class="badge badge-<?= getStatusBadgeColor($application['status']) ?>">
                                    <?= ucwords(str_replace('_', ' ', $application['status'])) ?>
                                </span>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-building"></i> <?= esc($application['company_name'] ?? 'Company') ?> | 
                                <i class="fas fa-clock"></i> Applied <?= timeAgo($application['applied_at']) ?>
                            </small>
                        </div>

                        <div class="card-body">
                            <!-- Current Stage Progress -->
                            <div class="stage-progress mb-3">
                                <h6 class="mb-2"><i class="fas fa-route"></i> Application Progress</h6>
                                <div class="progress-steps">
                                    <?php
                                    $stages = [
                                        'pending' => 'Screening',
                                        'ai_interview_scheduled' => 'AI Interview',
                                        'ai_interview_completed' => 'Reviewed',
                                        'shortlisted' => 'Shortlisted',
                                        'hr_interview_scheduled' => 'HR Interview',
                                        'selected' => 'Selected'
                                    ];
                                    
                                    $currentStageIndex = array_search($application['status'], array_keys($stages));
                                    ?>
                                    
                                    <div class="step-container">
                                        <?php foreach ($stages as $stageKey => $stageName): 
                                            $stageIndex = array_search($stageKey, array_keys($stages));
                                            $isCompleted = $stageIndex <= $currentStageIndex;
                                            $isCurrent = $stageKey === $application['status'];
                                        ?>
                                            <div class="step <?= $isCompleted ? 'completed' : '' ?> <?= $isCurrent ? 'current' : '' ?>">
                                                <div class="step-icon">
                                                    <?php if ($isCompleted): ?>
                                                        <i class="fas fa-check"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-circle"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="step-label"><?= $stageName ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- AI Interview Scores -->
                            <?php if (!empty($application['ai_interview_completed'])): ?>
                            <div class="ai-scores mb-3">
                                <h6 class="mb-2"><i class="fas fa-chart-bar"></i> AI Interview Performance</h6>
                                <div class="score-grid">
                                    <div class="score-item">
                                        <label>Technical</label>
                                        <div class="score-bar">
                                            <div class="score-fill" style="width: <?= $application['technical_score'] ?? 0 ?>%">
                                                <?= number_format($application['technical_score'] ?? 0, 1) ?>%
                                            </div>
                                        </div>
                                    </div>
                                    <div class="score-item">
                                        <label>Communication</label>
                                        <div class="score-bar">
                                            <div class="score-fill" style="width: <?= $application['communication_score'] ?? 0 ?>%">
                                                <?= number_format($application['communication_score'] ?? 0, 1) ?>%
                                            </div>
                                        </div>
                                    </div>
                                    <div class="score-item">
                                        <label>Overall Rating</label>
                                        <div class="overall-score">
                                            <span class="score-badge badge-<?= getScoreBadgeColor($application['overall_rating'] ?? 0) ?>">
                                                <?= number_format($application['overall_rating'] ?? 0, 1) ?>%
                                            </span>
                                            <div class="stars">
                                                <?php 
                                                $stars = round(($application['overall_rating'] ?? 0) / 20);
                                                for ($i = 1; $i <= 5; $i++): 
                                                ?>
                                                    <i class="fas fa-star <?= $i <= $stars ? 'text-warning' : 'text-muted' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Next Actions -->
                            <?php if (!empty($application['next_action'])): ?>
                            <div class="next-action alert alert-info">
                                <strong><i class="fas fa-info-circle"></i> Next Step:</strong> 
                                <?= $application['next_action'] ?>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-footer bg-white border-top">
                            <div class="d-flex justify-content-between">
                                <a href="<?= base_url('candidate/applications/view/' . $application['id']) ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View Details
                                </a>
                                
                                <?php if ($application['status'] === 'ai_interview_scheduled'): ?>
                                    <a href="<?= base_url('candidate/ai-interview/' . $application['id']) ?>" class="btn btn-sm btn-success">
                                        <i class="fas fa-video"></i> Start AI Interview
                                    </a>
                                <?php elseif ($application['status'] === 'hr_interview_scheduled'): ?>
                                    <a href="<?= base_url('candidate/my-bookings') ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-calendar"></i> View Interview
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
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
            
        </div>
    </div>
</div>
<!-- Dashboard Content End -->

<style>
/* Stats Cards */
.stats-card {
    border: none;
    border-radius: 0.5rem;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

/* Application Cards */
.application-card {
    border: none;
    border-radius: 0.5rem;
    transition: transform 0.3s, box-shadow 0.3s;
}

.application-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175) !important;
}

/* Progress Steps */
.step-container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    position: relative;
    padding: 20px 0;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    flex: 1;
    position: relative;
}

.step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 15px;
    left: 50%;
    width: 100%;
    height: 2px;
    background: #e0e0e0;
    z-index: -1;
}

.step.completed:not(:last-child)::after {
    background: #1cc88a;
}

.step-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #e0e0e0;
    color: #999;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 8px;
    font-size: 14px;
}

.step.completed .step-icon {
    background: #1cc88a;
    color: white;
}

.step.current .step-icon {
    background: #4e73df;
    color: white;
    box-shadow: 0 0 0 4px rgba(78, 115, 223, 0.2);
}

.step-label {
    font-size: 11px;
    text-align: center;
    color: #666;
    font-weight: 500;
}

.step.current .step-label {
    color: #4e73df;
    font-weight: bold;
}

/* AI Scores */
.score-grid {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.score-item label {
    font-size: 12px;
    font-weight: 600;
    color: #666;
    margin-bottom: 4px;
    display: block;
}

.score-bar {
    height: 24px;
    background: #e9ecef;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
}

.score-fill {
    height: 100%;
    background: linear-gradient(to right, #4e73df, #224abe);
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding-right: 8px;
    color: white;
    font-size: 11px;
    font-weight: bold;
    transition: width 0.5s ease;
}

.overall-score {
    display: flex;
    align-items: center;
    gap: 10px;
}

.score-badge {
    font-size: 18px;
    padding: 6px 12px;
}

.badge-success { background: #1cc88a; }
.badge-warning { background: #f6c23e; color: #333; }
.badge-danger { background: #e74a3b; }
.badge-info { background: #36b9cc; }

.stars {
    display: flex;
    gap: 2px;
}

/* Quick Links */
.quick-link-card {
    display: block;
    padding: 30px 20px;
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
    text-align: center;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s;
}

.quick-link-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    text-decoration: none;
}

.border-left-warning {
    border-left: 4px solid #f6c23e;
}

/* Responsive */
@media (max-width: 768px) {
    .step-label {
        font-size: 9px;
    }
    
    .step-icon {
        width: 24px;
        height: 24px;
        font-size: 12px;
    }
}
</style>

<?php
// Helper function for status badge colors
function getStatusBadgeColor($status) {
    $colors = [
        'pending' => 'warning',
        'ai_interview_scheduled' => 'info',
        'ai_interview_completed' => 'primary',
        'shortlisted' => 'success',
        'hr_interview_scheduled' => 'warning',
        'hr_interview_completed' => 'primary',
        'selected' => 'success',
        'rejected' => 'danger'
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



<?= view('layouts/candidate_footer') ?>