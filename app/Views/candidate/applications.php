<?= view('Layouts/candidate_header', ['title' => 'Applications']) ?>

<?php
$totalApplications = count($applications ?? []);
$activeApplications = count(array_filter($applications ?? [], function ($application) {
    return !in_array($application['status'] ?? '', ['rejected', 'selected', 'withdrawn', 'hired'], true);
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
        <style>
            .applications-shell {
                display: grid;
                grid-template-columns: 340px minmax(0, 1fr);
                gap: 24px;
                align-items: start;
            }
            .applications-sidebar,
            .application-detail-panel {
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 18px;
                box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            }
            .applications-sidebar {
                position: sticky;
                top: 110px;
                overflow: hidden;
            }
            .applications-sidebar-header {
                padding: 20px 22px 14px;
                border-bottom: 1px solid #eef2f7;
            }
            .applications-sidebar-list {
                max-height: 70vh;
                overflow-y: auto;
            }
            .application-list-item {
                width: 100%;
                border: 0;
                border-bottom: 1px solid #eef2f7;
                background: #fff;
                text-align: left;
                padding: 16px 20px;
                transition: background-color .18s ease;
                cursor: pointer;
            }
            .application-list-item:focus,
            .application-list-item:focus-visible {
                outline: none;
                box-shadow: none;
            }
            .application-list-item:last-child {
                border-bottom: 0;
            }
            .application-list-item.is-active {
                background: #f8fbff;
                box-shadow: inset 3px 0 0 #78b300;
            }
            .application-list-title {
                font-size: 1rem;
                font-weight: 700;
                color: #1f2937;
                margin-bottom: 6px;
            }
            .application-list-meta {
                font-size: .88rem;
                color: #6b7280;
                margin-bottom: 10px;
            }
            .application-detail-panel {
                padding: 24px;
            }
            .application-detail-card {
                display: none;
            }
            .application-detail-card.is-active {
                display: block;
            }
            .application-detail-head {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 16px;
                padding-bottom: 18px;
                margin-bottom: 20px;
                border-bottom: 1px solid #eef2f7;
            }
            .application-detail-title {
                font-size: 1.8rem;
                line-height: 1.2;
                margin-bottom: 8px;
            }
            .application-detail-title-link {
                color: #111827;
                text-decoration: none;
            }
            .application-detail-title-link:hover {
                color: #1d4ed8;
                text-decoration: underline;
            }
            .application-detail-submeta {
                color: #6b7280;
                font-size: .95rem;
            }
            .application-section-title {
                font-size: 1.05rem;
                font-weight: 700;
                color: #374151;
                margin-bottom: 12px;
            }
            .detail-progress {
                height: 26px;
                border-radius: 999px;
                overflow: hidden;
                background: #e5e7eb;
            }
            .detail-progress .progress-bar {
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
            }
            .detail-score-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 14px;
            }
            .detail-score-item {
                border: 1px solid #dbe5f0;
                border-radius: 14px;
                padding: 16px;
                background: #f8fbff;
                text-align: center;
            }
            .detail-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                margin-top: 22px;
            }
            .prep-coach-card {
                border: 1px solid #dbeafe;
                border-radius: 16px;
                background: linear-gradient(135deg, #f8fbff 0%, #f4fbf6 100%);
                padding: 18px;
                margin-top: 22px;
            }
            .prep-coach-chip-list {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
            }
            .prep-coach-chip {
                display: inline-flex;
                align-items: center;
                padding: 6px 10px;
                border-radius: 999px;
                border: 1px solid #bfdbfe;
                background: #eff6ff;
                color: #1d4ed8;
                font-size: 12px;
                font-weight: 700;
            }
            .prep-coach-list {
                margin: 0;
                padding-left: 18px;
            }
            .prep-coach-list li {
                margin-bottom: 8px;
                color: #475569;
            }
            .prep-coach-summary {
                color: #475569;
                margin-bottom: 14px;
                max-width: 760px;
            }
            .prep-coach-meta {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
                margin-bottom: 12px;
            }
            .prep-coach-actions {
                display: flex;
                flex-wrap: wrap;
                gap: 10px;
                align-items: center;
            }
            .prep-coach-source {
                display: inline-flex;
                align-items: center;
                padding: 4px 10px;
                border-radius: 999px;
                font-size: 11px;
                font-weight: 700;
                letter-spacing: .04em;
                text-transform: uppercase;
            }
            .prep-coach-source.is-ai {
                background: #dcfce7;
                color: #166534;
                border: 1px solid #bbf7d0;
            }
            .prep-coach-source.is-fallback {
                background: #eff6ff;
                color: #1d4ed8;
                border: 1px solid #bfdbfe;
            }
            @media (max-width: 991.98px) {
                .applications-shell {
                    grid-template-columns: 1fr;
                }
                .applications-sidebar {
                    position: static;
                }
                .applications-sidebar-list {
                    max-height: none;
                }
                .application-detail-title {
                    font-size: 1.45rem;
                }
            }
            @media (max-width: 575.98px) {
                .application-detail-head {
                    flex-direction: column;
                }
                .detail-score-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
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
            <div class="applications-shell">
                <aside class="applications-sidebar">
                    <div class="applications-sidebar-header">
                        <h5 class="mb-1">All Applications</h5>
                        <p class="text-muted mb-0 small"><?= $totalApplications ?> total, <?= $activeApplications ?> active</p>
                    </div>
                    <div class="applications-sidebar-list">
                        <?php foreach ($applications as $index => $application): ?>
                            <?php
                            $listActivity = $application['recruiter_activity'] ?? [];
                            $activityCount = (int) ($listActivity['profile_viewed_count'] ?? 0)
                                + (int) ($listActivity['contact_viewed_count'] ?? 0)
                                + (int) ($listActivity['resume_downloaded_count'] ?? 0);
                            ?>
                            <button type="button" class="application-list-item<?= $index === 0 ? ' is-active' : '' ?>" data-application-target="application-<?= (int) $application['id'] ?>">
                                <div class="application-list-title"><?= esc($application['job_title']) ?></div>
                                <div class="application-list-meta">
                                    Applied <?= date('M d, Y', strtotime($application['applied_at'])) ?>
                                    <?php if (!empty($application['company'])): ?>
                                        <span class="d-block"><?= esc($application['company']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge status-badge badge-<?= getStatusBadgeColor($application['status']) ?>">
                                        <?= ucwords(str_replace('_', ' ', $application['status'])) ?>
                                    </span>
                                    <small class="text-muted"><?= $activityCount ?> activity</small>
                                </div>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </aside>

                <section class="application-detail-panel">
                    <?php foreach ($applications as $index => $application): ?>
                        <?php
                        $stages = [
                            'applied' => 'Applied',
                            'shortlisted' => 'Shortlisted',
                            'interview_slot_booked' => 'Interview Booked',
                            'selected' => 'Selected',
                            'hired' => 'Hired',
                        ];
                        $currentIndex = array_search($application['status'], array_keys($stages), true);
                        $isRejected = $application['status'] === 'rejected';
                        $isWithdrawn = $application['status'] === 'withdrawn';
                        $isFinalPositive = in_array($application['status'], ['selected', 'hired'], true);
                        $progress = ($currentIndex !== false) ? (($currentIndex + 1) / count($stages)) * 100 : 20;
                        $recruiterActivity = $application['recruiter_activity'] ?? [];
                        $profileViewed = (int) (($recruiterActivity['profile_unique_recruiters'] ?? 0) ?: ($recruiterActivity['profile_viewed_count'] ?? 0));
                        $contactViewed = (int) (($recruiterActivity['contact_unique_recruiters'] ?? 0) ?: ($recruiterActivity['contact_viewed_count'] ?? 0));
                        $resumeDownloaded = (int) (($recruiterActivity['resume_unique_recruiters'] ?? 0) ?: ($recruiterActivity['resume_downloaded_count'] ?? 0));
                        $lastActivityAt = $recruiterActivity['last_recruiter_activity_at'] ?? null;
                        ?>
                        <article id="application-<?= (int) $application['id'] ?>" class="application-detail-card<?= $index === 0 ? ' is-active' : '' ?>">
                            <div class="application-detail-head">
                                <div>
                                    <h2 class="application-detail-title">
                                        <a href="<?= base_url('job/' . $application['job_id']) ?>" target="_blank" class="application-detail-title-link">
                                            <?= esc($application['job_title']) ?>
                                        </a>
                                    </h2>
                                    <div class="application-detail-submeta">
                                        <i class="far fa-clock"></i> Applied <?= date('M d, Y', strtotime($application['applied_at'])) ?>
                                        <?php if (!empty($application['company'])): ?>
                                            <span class="mx-2">&bull;</span><?= esc($application['company']) ?>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($application['resume_version_title'])): ?>
                                        <div class="small text-muted mt-2">
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

                            <div class="mb-4">
                                <div class="application-section-title">Progress</div>
                                <?php if ($isRejected): ?>
                                    <div class="alert alert-danger mb-0"><i class="fas fa-times-circle"></i> Application Rejected</div>
                                <?php elseif ($isWithdrawn): ?>
                                    <div class="alert alert-secondary mb-0"><i class="fas fa-ban"></i> Application Withdrawn</div>
                                <?php elseif ($isFinalPositive): ?>
                                    <div class="alert alert-success mb-0"><i class="fas fa-check-circle"></i> <?= $application['status'] === 'hired' ? 'You are hired for this role.' : 'You have been selected for this role.' ?></div>
                                <?php else: ?>
                                    <div class="progress detail-progress">
                                        <div class="progress-bar bg-success" style="width: <?= $progress ?>%">
                                            <?= esc($stages[$application['status']] ?? 'In Progress') ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($application['overall_rating'])): ?>
                                <div class="mb-4">
                                    <div class="application-section-title">AI Interview Score</div>
                                    <div class="detail-score-grid">
                                        <div class="detail-score-item"><small class="text-muted d-block">Technical</small><strong><?= number_format($application['technical_score'] ?? 0, 1) ?>%</strong></div>
                                        <div class="detail-score-item"><small class="text-muted d-block">Communication</small><strong><?= number_format($application['communication_score'] ?? 0, 1) ?>%</strong></div>
                                        <div class="detail-score-item"><small class="text-muted d-block">Overall</small><strong><?= number_format($application['overall_rating'] ?? 0, 1) ?>%</strong></div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <div class="application-section-title">Recruiter Activity</div>
                                <div class="detail-score-grid">
                                    <div class="detail-score-item"><small class="text-muted d-block">Profile Viewed</small><strong><?= $profileViewed ?></strong></div>
                                    <div class="detail-score-item"><small class="text-muted d-block">Contact Viewed</small><strong><?= $contactViewed ?></strong></div>
                                    <div class="detail-score-item"><small class="text-muted d-block">Resume Downloaded</small><strong><?= $resumeDownloaded ?></strong></div>
                                </div>
                                <?php if (!empty($lastActivityAt)): ?>
                                    <small class="text-muted d-block mt-3">Last recruiter activity: <?= date('M d, Y h:i A', strtotime($lastActivityAt)) ?></small>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($application['interview_prep']) && !in_array($application['status'], ['rejected', 'withdrawn', 'selected', 'hired'], true)): ?>
                                <?php $prep = $application['interview_prep']; ?>
                                <div class="prep-coach-card">
                                    <h4 class="application-section-title mb-2"><?= esc($prep['title'] ?? 'Pre-interview Preparation Coach') ?></h4>
                                    <div class="prep-coach-meta">
                                        <p class="text-muted mb-0">Open the detailed mock interview page to practice role-specific questions, answer structure, and evaluation points.</p>
                                        <?php $prepSource = (string) ($prep['source'] ?? 'fallback'); ?>
                                        <span class="prep-coach-source <?= $prepSource === 'ai' ? 'is-ai' : 'is-fallback' ?>">
                                            <?= $prepSource === 'ai' ? 'AI-generated' : 'Structured fallback' ?>
                                        </span>
                                    </div>

                                    <?php if (!empty($prep['focus_skills'])): ?>
                                        <div class="mb-3">
                                            <div class="small text-uppercase font-weight-bold text-muted mb-2">Focus Skills</div>
                                            <div class="prep-coach-chip-list">
                                                <?php foreach ((array) $prep['focus_skills'] as $skill): ?>
                                                    <span class="prep-coach-chip"><?= esc($skill) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <div class="prep-coach-summary">
                                        Includes tailored interview rounds, deeper mock questions, answer guidance, and a final rehearsal checklist for this application.
                                    </div>
                                    <div class="prep-coach-actions">
                                        <a href="<?= base_url('candidate/applications/' . (int) $application['id'] . '/mock-interview') ?>" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-comments"></i> Open Mock Interview
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="detail-actions">
                                <?php if ($application['status'] === 'applied'): ?>
                                    <?php $policy = strtoupper($application['ai_interview_policy'] ?? 'REQUIRED_HARD'); ?>
                                    <?php if ($policy === 'OFF'): ?>
                                        <span class="badge badge-info p-2">AI interview disabled for this job</span>
                                    <?php else: ?>
                                        <a href="<?= base_url('interview/start/' . $application['id']) ?>" class="btn btn-success btn-sm"><i class="fas fa-video"></i> <?= $policy === 'OPTIONAL' ? 'Start AI Interview (Optional)' : 'Start AI Interview' ?></a>
                                        <?php if ($policy === 'OPTIONAL'): ?>
                                            <a href="<?= base_url('candidate/book-slot/' . $application['id']) ?>" class="btn btn-outline-warning btn-sm"><i class="fas fa-calendar-plus"></i> Book Slot Without AI</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                <?php elseif ($application['status'] === 'shortlisted'): ?>
                                    <a href="<?= base_url('candidate/book-slot/' . $application['id']) ?>" class="btn btn-warning btn-sm"><i class="fas fa-calendar-plus"></i> Book Interview Slot</a>
                                <?php elseif ($application['status'] === 'interview_slot_booked'): ?>
                                    <a href="<?= base_url('candidate/my-bookings') ?>" class="btn btn-info btn-sm text-white"><i class="fas fa-calendar-check"></i> View Interview Schedule</a>
                                <?php endif; ?>
                                <?php if (!in_array($application['status'], ['withdrawn', 'rejected', 'selected', 'hired', 'interview_slot_booked'], true)): ?>
                                    <form action="<?= base_url('candidate/applications/withdraw/' . $application['id']) ?>" method="post" onsubmit="return confirm('Withdraw this application?');" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-times-circle"></i> Withdraw</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </section>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var applicationButtons = document.querySelectorAll('.application-list-item');
    var applicationCards = document.querySelectorAll('.application-detail-card');

    applicationButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            var targetId = button.getAttribute('data-application-target');

            applicationButtons.forEach(function (item) {
                item.classList.remove('is-active');
            });

            applicationCards.forEach(function (card) {
                card.classList.remove('is-active');
            });

            button.classList.add('is-active');

            var activeCard = document.getElementById(targetId);
            if (activeCard) {
                activeCard.classList.add('is-active');
            }
        });
    });
});
</script>

<?php
function getStatusBadgeColor($status)
{
    $colors = [
        'applied' => 'warning',
        'shortlisted' => 'success',
        'rejected' => 'danger',
        'interview_slot_booked' => 'warning',
        'selected' => 'success',
        'withdrawn' => 'secondary',
        'hired' => 'success',
    ];

    return $colors[$status] ?? 'secondary';
}
?>

<?= view('Layouts/candidate_footer') ?>
