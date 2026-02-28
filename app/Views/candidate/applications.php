<?= view('Layouts/candidate_header', ['title' => 'Applications']) ?>

<?php
$totalApplications = count($applications ?? []);
$activeApplications = count(array_filter($applications ?? [], function ($application) {
    return !in_array($application['status'] ?? '', ['rejected', 'selected'], true);
}));
?>

<div class="applications-jobboard">
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <h1 class="text-white font-weight-bold">My Applications</h1>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong>Applications</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container content-wrap pb-5">
        <?php if (empty($applications)): ?>
            <div class="text-center bg-white rounded shadow-sm p-5 mb-4">
                <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">No Applications Yet</h4>
                <p class="text-muted mb-4">You have not applied to any jobs yet.</p>
                <a href="<?= base_url('jobs') ?>" class="btn btn-primary btn-lg">
                    <i class="fas fa-search"></i> Browse Jobs
                </a>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($applications as $application): ?>
                    <div class="col-lg-6">
                        <div class="application-card">
                            <div class="application-head">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="mb-1"><?= esc($application['job_title']) ?></h5>
                                        <small class="text-muted"><i class="far fa-clock"></i> Applied <?= date('M d, Y', strtotime($application['applied_at'])) ?></small>
                                        <?php if (!empty($application['resume_version_title'])): ?>
                                            <div class="small text-muted mt-1">
                                                <i class="fas fa-file-alt"></i>
                                                Resume used: <?= esc($application['resume_version_title']) ?>
                                                <?php if (!empty($application['resume_version_target_role'])): ?>
                                                    (<?= esc($application['resume_version_target_role']) ?>)
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="badge status-badge badge-<?= getStatusBadgeColor($application['status']) ?>">
                                        <?= ucwords(str_replace('_', ' ', $application['status'])) ?>
                                    </span>
                                </div>
                            </div>

                            <div class="application-body">
                                <?php
                                $stages = [
                                    'applied' => 'Applied',
                                    'ai_interview_completed' => 'AI Interviewed',
                                    'shortlisted' => 'Shortlisted',
                                    'interview_slot_booked' => 'Interview Booked',
                                    'selected' => 'Selected'
                                ];
                                $currentIndex = array_search($application['status'], array_keys($stages), true);
                                $isRejected = $application['status'] === 'rejected';
                                ?>

                                <h6 class="mb-2"><i class="fas fa-route"></i> Progress</h6>
                                <?php if ($isRejected): ?>
                                    <div class="alert alert-danger mb-3">
                                        <i class="fas fa-times-circle"></i> Application Rejected
                                    </div>
                                <?php else: ?>
                                    <?php $progress = ($currentIndex !== false) ? (($currentIndex + 1) / count($stages)) * 100 : 20; ?>
                                    <div class="progress mb-3" style="height: 24px;">
                                        <div class="progress-bar bg-success" style="width: <?= $progress ?>%">
                                            <?= esc($stages[$application['status']] ?? 'In Progress') ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($application['overall_rating'])): ?>
                                    <h6 class="mb-2"><i class="fas fa-chart-bar"></i> AI Interview Score</h6>
                                    <div class="score-grid">
                                        <div class="score-item">
                                            <small class="text-muted d-block">Technical</small>
                                            <strong><?= number_format($application['technical_score'] ?? 0, 1) ?>%</strong>
                                        </div>
                                        <div class="score-item">
                                            <small class="text-muted d-block">Communication</small>
                                            <strong><?= number_format($application['communication_score'] ?? 0, 1) ?>%</strong>
                                        </div>
                                        <div class="score-item">
                                            <small class="text-muted d-block">Overall</small>
                                            <strong><?= number_format($application['overall_rating'] ?? 0, 1) ?>%</strong>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <?php
                                $recruiterActivity = $application['recruiter_activity'] ?? [];
                                $profileViewed = (int) (($recruiterActivity['profile_unique_recruiters'] ?? 0) ?: ($recruiterActivity['profile_viewed_count'] ?? 0));
                                $contactViewed = (int) (($recruiterActivity['contact_unique_recruiters'] ?? 0) ?: ($recruiterActivity['contact_viewed_count'] ?? 0));
                                $resumeDownloaded = (int) (($recruiterActivity['resume_unique_recruiters'] ?? 0) ?: ($recruiterActivity['resume_downloaded_count'] ?? 0));
                                $lastActivityAt = $recruiterActivity['last_recruiter_activity_at'] ?? null;
                                ?>
                                <h6 class="mt-3 mb-2"><i class="fas fa-user-check"></i> Recruiter Activity</h6>
                                <div class="score-grid">
                                    <div class="score-item">
                                        <small class="text-muted d-block">Profile Viewed</small>
                                        <strong><?= $profileViewed ?></strong>
                                    </div>
                                    <div class="score-item">
                                        <small class="text-muted d-block">Contact Viewed</small>
                                        <strong><?= $contactViewed ?></strong>
                                    </div>
                                    <div class="score-item">
                                        <small class="text-muted d-block">Resume Downloaded</small>
                                        <strong><?= $resumeDownloaded ?></strong>
                                    </div>
                                </div>
                                <?php if (!empty($lastActivityAt)): ?>
                                    <small class="text-muted d-block mt-2">
                                        Last recruiter activity: <?= date('M d, Y h:i A', strtotime($lastActivityAt)) ?>
                                    </small>
                                <?php endif; ?>
                            </div>

                            <div class="application-footer">
                                <div class="d-flex flex-wrap" style="gap: 8px;">
                                    <a href="<?= base_url('job/' . $application['job_id']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye"></i> View Job
                                    </a>

                                    <?php if ($application['status'] === 'applied'): ?>
                                        <?php $policy = strtoupper($application['ai_interview_policy'] ?? 'REQUIRED_HARD'); ?>
                                        <?php if ($policy === 'OFF'): ?>
                                            <span class="badge badge-info p-2">AI interview disabled for this job</span>
                                        <?php else: ?>
                                            <a href="<?= base_url('interview/start/' . $application['id']) ?>" class="btn btn-success btn-sm">
                                                <i class="fas fa-video"></i> <?= $policy === 'OPTIONAL' ? 'Start AI Interview (Optional)' : 'Start AI Interview' ?>
                                            </a>
                                            <?php if ($policy === 'OPTIONAL'): ?>
                                                <a href="<?= base_url('candidate/book-slot/' . $application['id']) ?>" class="btn btn-outline-warning btn-sm">
                                                    <i class="fas fa-calendar-plus"></i> Book Slot Without AI
                                                </a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    <?php elseif ($application['status'] === 'ai_interview_completed' && strtoupper($application['ai_interview_policy'] ?? 'REQUIRED_HARD') === 'REQUIRED_SOFT'): ?>
                                        <span class="badge badge-info p-2">AI done. Recruiter decision pending.</span>
                                    <?php elseif ($application['status'] === 'shortlisted'): ?>
                                        <a href="<?= base_url('candidate/book-slot/' . $application['id']) ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-calendar-plus"></i> Book Interview Slot
                                        </a>
                                    <?php elseif ($application['status'] === 'interview_slot_booked'): ?>
                                        <a href="<?= base_url('candidate/my-bookings') ?>" class="btn btn-info btn-sm text-white">
                                            <i class="fas fa-calendar-check"></i> View Interview Schedule
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
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
