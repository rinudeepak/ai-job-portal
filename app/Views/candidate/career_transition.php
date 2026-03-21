<?= view('Layouts/candidate_header', ['title' => 'Career Transition AI']) ?>

<?php
$skillGaps = [];
if (!empty($transition['skill_gaps'])) {
    $decodedGaps = json_decode((string) $transition['skill_gaps'], true);
    if (is_array($decodedGaps)) {
        $skillGaps = $decodedGaps;
    }
}
$hasTransition = !empty($transition);
$taskCount = count($tasks ?? []);
$reactivationCount = (int) ($transition['reactivation_count'] ?? 0);
?>

<div class="career-transition-jobboard">
    <section class="career-transition-content">
        <div class="container">
            <div class="page-board-header page-board-header-tight">
                <div class="page-board-copy">
                    <span class="page-board-kicker"><i class="fas fa-route"></i> Career learning path</span>
                    <h1 class="page-board-title">Career Transition AI</h1>
                    <p class="page-board-subtitle">Get a personalized roadmap, identify skill gaps, and follow a day-by-day learning plan toward your target role.</p>
                </div>
                <div class="page-board-actions">
                    <a href="<?= base_url('career-transition/history') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-history mr-1"></i> View History
                    </a>
                </div>
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (!$hasTransition): ?>
                <div class="row g-4 align-items-start">
                    <div class="col-lg-7">
                        <div class="career-transition-card dashboard-panel" id="transition-form">
                            <div class="panel-header">
                                <div class="ai-badge">
                                    <i class="fas fa-lightbulb"></i>
                                    Personalized Roadmap
                                </div>
                                <h2 class="section-title mb-2">Start Your Career Transition Journey</h2>
                                <p class="section-subtitle">
                                    We analyze your current skill set and generate a focused roadmap for your target role.
                                </p>
                            </div>
                            <div class="panel-body">
                                <div class="dashboard-cta-banner mb-4">
                                    <h3 class="section-title mb-2">Smart Tip</h3>
                                    <p class="section-subtitle mb-0">
                                        If you already explored this path before, your course can be restored instantly.
                                    </p>
                                </div>

                                <form action="<?= base_url('career-transition/create') ?>" method="post" id="transitionForm">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Current Role</label>
                                        <input type="text" name="current_role" class="form-control" value="<?= esc($currentRole ?? '') ?>" placeholder="e.g., PHP Developer" required>
                                        <small class="text-muted">Auto-detected from your profile</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Target Role</label>
                                        <input list="role-suggestions" type="text" name="target_role" class="form-control" value="<?= esc($targetRole ?? '') ?>" placeholder="e.g., Next.js Developer" required>
                                        <datalist id="role-suggestions">
                                            <option value="Next.js Developer">
                                            <option value="React Developer">
                                            <option value="DevOps Engineer">
                                            <option value="DevOps Developer">
                                            <option value="Data Scientist">
                                            <option value="Data Analyst">
                                            <option value="Full Stack Developer">
                                            <option value="Frontend Developer">
                                            <option value="Backend Developer">
                                            <option value="Python Developer">
                                            <option value="Java Developer">
                                            <option value="Node.js Developer">
                                            <option value="Cloud Engineer">
                                            <option value="Machine Learning Engineer">
                                        </datalist>
                                        <small class="text-muted">Choose from suggestions or type your own role</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary career-transition-submit-btn" id="submitBtn">
                                        <span id="btnText"><i class="fas fa-rocket"></i> Generate Personalized Roadmap</span>
                                        <span id="btnLoading" class="transition-btn-loading">
                                            <span class="spinner-border spinner-border-sm" role="status"></span>
                                            Generating AI course... (60-90 seconds)
                                        </span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="dashboard-panel mb-4">
                            <div class="panel-header">
                                <h3 class="section-title mb-2">What You Get</h3>
                                <p class="section-subtitle mb-0">A practical transition plan, not just a static list.</p>
                            </div>
                            <div class="panel-body">
                                <div class="d-grid gap-3">
                                    <div class="quick-action-card">
                                        <div class="quick-action-title">Learning Path</div>
                                        <div class="quick-action-text">A structured course with daily tasks and milestones.</div>
                                    </div>
                                    <div class="quick-action-card">
                                        <div class="quick-action-title">Skill Gap Analysis</div>
                                        <div class="quick-action-text">Clear focus areas based on your current profile.</div>
                                    </div>
                                    <div class="quick-action-card">
                                        <div class="quick-action-title">Job Direction</div>
                                        <div class="quick-action-text">Target roles aligned to your new career goal.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="dashboard-panel">
                            <div class="panel-header">
                                <h3 class="section-title mb-2">Need to Revisit?</h3>
                                <p class="section-subtitle mb-0">Keep your existing path and build a new one when you’re ready.</p>
                            </div>
                            <div class="panel-body">
                                <a href="<?= base_url('career-transition/history') ?>" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="fas fa-history"></i> View History
                                </a>
                                <p class="text-muted mb-0">Previous transitions remain available for reference and reuse.</p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4 align-items-start">
                    <div class="col-lg-4">
                        <div class="dashboard-panel mb-4">
                            <div class="panel-header text-center">
                                <div class="ai-badge mb-3">
                                    <i class="fas fa-route"></i>
                                    Current Path
                                </div>
                                <h3 class="section-title career-transition-role-title career-transition-current-role mb-2"><?= esc($transition['current_role']) ?></h3>
                                <div class="transition-arrow my-3" style="font-size: 2rem; color: var(--primary);">
                                    <i class="fas fa-arrow-down"></i>
                                </div>
                                <h3 class="section-title career-transition-role-title career-transition-target-role mb-0"><?= esc($transition['target_role']) ?></h3>

                                <?php if ($reactivationCount > 0): ?>
                                    <div class="badge badge-info mt-3">
                                        <i class="fas fa-redo"></i> Reused <?= $reactivationCount ?> time(s)
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="panel-body">
                                <a href="<?= base_url('career-transition/course') ?>" class="btn btn-primary w-100 mb-2">
                                    <i class="fas fa-book-open"></i> View Full Course
                                </a>
                                <a href="<?= base_url('career-transition/download-pdf') ?>" class="btn btn-info w-100 mb-2">
                                    <i class="fas fa-file-pdf"></i> Download PDF
                                </a>
                                <button type="button" class="btn btn-warning w-100" onclick="if(confirm('Save current path to history and start a new one? Your progress will be preserved.')) window.location.href='<?= base_url('career-transition/reset') ?>'">
                                    <i class="fas fa-sync"></i> Change Career Path
                                </button>
                            </div>
                        </div>

                        <div class="dashboard-panel">
                            <div class="panel-header">
                                <h3 class="section-title mb-2">Skill Gaps</h3>
                                <p class="section-subtitle mb-0">Skills to prioritize next</p>
                            </div>
                            <div class="panel-body">
                                <?php if (!empty($skillGaps)): ?>
                                    <div class="job-card-tags">
                                        <?php foreach ($skillGaps as $skill): ?>
                                            <span class="badge badge-secondary"><?= esc($skill) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted mb-0">Analyzing skills...</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-8">
                        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                            <div>
                                <div class="ai-badge">
                                    <i class="fas fa-calendar-day"></i>
                                    Daily Plan
                                </div>
                                <h2 class="section-title">Daily Learning Tasks</h2>
                                <p class="section-subtitle mb-0">Each task maps directly to a module and lesson in your generated course.</p>
                            </div>
                            <div class="badge badge-primary" style="padding: 0.6rem 0.9rem;">
                                <?= $taskCount ?> Tasks
                            </div>
                        </div>

                        <?php if (!empty($tasks) && count($tasks) > 0): ?>
                            <div class="d-grid gap-3">
                                <?php foreach ($tasks as $task): ?>
                                    <div class="dashboard-panel transition-task-card <?= !empty($task['is_completed']) ? 'task-completed' : '' ?>">
                                        <div class="panel-body">
                                            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                                                <div class="flex-grow-1">
                                                    <h4 class="mb-2">Day <?= (int) $task['day_number'] ?>: <?= esc($task['task_title']) ?></h4>
                                                    <p class="mb-2 text-muted"><?= esc($task['task_description']) ?></p>
                                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                                        <span class="badge badge-info">
                                                            <i class="far fa-clock"></i> <?= (int) $task['duration_minutes'] ?> minutes
                                                        </span>
                                                        <?php if (!empty($task['module_number'])): ?>
                                                            <span class="badge badge-secondary">
                                                                Module <?= (int) $task['module_number'] ?>
                                                                <?= !empty($task['lesson_number']) ? ' - Lesson ' . (int) $task['lesson_number'] : '' ?>
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div>
                                                    <?php if (empty($task['is_completed'])): ?>
                                                        <button class="btn btn-sm btn-primary" onclick="completeTask(<?= (int) $task['id'] ?>)">
                                                            <i class="fas fa-check"></i> Complete
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="badge badge-primary"><i class="fas fa-check"></i> Done</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="dashboard-panel">
                                <div class="panel-body text-center py-5">
                                    <i class="fas fa-sync-alt fa-3x text-muted mb-3"></i>
                                    <h4 class="mb-2">Tasks are being generated</h4>
                                    <p class="text-muted mb-0" data-auto-refresh="1" data-refresh-delay="5000">
                                        This page will refresh shortly.
                                    </p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
