<?= view('Layouts/candidate_header', ['title' => 'My Applications']) ?>

<div class="featured-job-area feature-padding pt-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h2><i class="fas fa-file-alt"></i> My Applications</h2>
                <p class="text-muted">Track all your job applications and their status</p>
            </div>
        </div>

        <div class="row">
            <?php if (empty($applications)): ?>
                <div class="col-12">
                    <div class="card shadow text-center py-5">
                        <div class="card-body">
                            <i class="fas fa-inbox fa-5x text-muted mb-4"></i>
                            <h4 class="text-muted">No Applications Yet</h4>
                            <p class="text-muted mb-4">You haven't applied to any jobs yet.</p>
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
                                    <?= esc($application['job_title']) ?>
                                </h5>
                                <span class="badge badge-<?= getStatusBadgeColor($application['status']) ?>">
                                    <?= ucwords(str_replace('_', ' ', $application['status'])) ?>
                                </span>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> Applied <?= date('M d, Y', strtotime($application['applied_at'])) ?>
                            </small>
                        </div>

                        <div class="card-body">
                            <!-- Progress Steps -->
                            <div class="mb-3">
                                <h6><i class="fas fa-route"></i> Progress</h6>
                                <?php
                                $stages = [
                                    'applied' => 'Applied',
                                    'ai_interview_completed' => 'AI Interviewed',
                                    'shortlisted' => 'Shortlisted',
                                    'interview_slot_booked' => 'Interview Booked',
                                    'selected' => 'Selected'
                                ];
                                $currentIndex = array_search($application['status'], array_keys($stages));
                                $isRejected = $application['status'] === 'rejected';
                                ?>
                                <?php if ($isRejected): ?>
                                    <div class="alert alert-danger mb-0">
                                        <i class="fas fa-times-circle"></i> Application Rejected
                                    </div>
                                <?php else: ?>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-success" style="width: <?= (($currentIndex + 1) / count($stages)) * 100 ?>%">
                                            <?= $stages[$application['status']] ?? 'In Progress' ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- AI Scores -->
                            <?php if (!empty($application['overall_rating'])): ?>
                            <div class="mb-3">
                                <h6><i class="fas fa-chart-bar"></i> AI Interview Score</h6>
                                <div class="d-flex justify-content-between">
                                    <span>Technical: <strong><?= number_format($application['technical_score'] ?? 0, 1) ?>%</strong></span>
                                    <span>Communication: <strong><?= number_format($application['communication_score'] ?? 0, 1) ?>%</strong></span>
                                    <span>Overall: <strong><?= number_format($application['overall_rating'] ?? 0, 1) ?>%</strong></span>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-footer bg-white">
                            <?php if ($application['status'] === 'applied'): ?>
                                <a href="<?= base_url('interview/start/' . $application['id']) ?>" class="btn btn-success btn-block">
                                    <i class="fas fa-video"></i> Start AI Interview
                                </a>
                            <?php elseif ($application['status'] === 'shortlisted'): ?>
                                <a href="<?= base_url('candidate/book-slot/' . $application['id']) ?>" class="btn btn-warning btn-block">
                                    <i class="fas fa-calendar-plus"></i> Book Interview Slot
                                </a>
                            <?php elseif ($application['status'] === 'interview_slot_booked'): ?>
                                <a href="<?= base_url('candidate/my-bookings') ?>" class="btn btn-info btn-block">
                                    <i class="fas fa-calendar-check"></i> View Interview Schedule
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
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
?>

<?= view('layouts/candidate_footer') ?>
