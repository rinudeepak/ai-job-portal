<?= view('Layouts/candidate_header', ['title' => 'Career Transition AI']) ?>

<div class="career-transition-jobboard">
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <h1 class="text-white font-weight-bold">Career Transition AI</h1>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong>Career Transition AI</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section pt-0 content-wrap">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap transition-header-row">
                <h3 class="mb-0">Your Career Transition</h3>
                <a href="<?= base_url('career-transition/history') ?>" class="btn btn-primary">
                    <i class="fas fa-history"></i> View History
                </a>
            </div>

            <?php if (!$transition): ?>
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                <?php endif; ?>

                <div class="card transition-panel">
                    <div class="card-body p-4 p-md-5">
                        <h5 class="mb-2">Start Your Career Transition Journey</h5>
                        <p class="text-muted mb-3">We analyze your current skill set and generate a focused roadmap for your target role.</p>

                        <div class="alert alert-info mb-4">
                            <i class="fas fa-lightbulb"></i>
                            <strong> Smart Tip:</strong> If you already explored this path before, your course can be restored instantly.
                        </div>

                        <form action="<?= base_url('career-transition/create') ?>" method="post" id="transitionForm">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label>Current Role</label>
                                <input type="text" name="current_role" class="form-control" value="<?= esc($currentRole ?? '') ?>" placeholder="e.g., PHP Developer" required>
                                <small class="text-muted">Auto-detected from your profile</small>
                            </div>
                            <div class="mb-3">
                                <label>Target Role (Job you want to transition to)</label>
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
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span id="btnText"><i class="fas fa-rocket"></i> Generate Personalized Roadmap</span>
                                <span id="btnLoading" class="transition-btn-loading">
                                    <span class="spinner-border spinner-border-sm" role="status"></span>
                                    Generating AI course... (60-90 seconds)
                                </span>
                            </button>
                        </form>
                    </div>
                </div>

            <?php else: ?>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card transition-panel">
                            <div class="card-body text-center">
                                <h6>Career Path</h6>
                                <h5><?= esc($transition['current_role']) ?></h5>
                                <div class="transition-arrow my-2"><i class="fas fa-arrow-down"></i></div>
                                <h5 class="text-success"><?= esc($transition['target_role']) ?></h5>

                                <?php if (isset($transition['reactivation_count']) && $transition['reactivation_count'] > 0): ?>
                                    <div class="badge badge-info mt-2">
                                        <i class="fas fa-redo"></i> Reused <?= (int)$transition['reactivation_count'] ?> time(s)
                                    </div>
                                <?php endif; ?>

                                <a href="<?= base_url('career-transition/course') ?>" class="btn btn-success btn-sm mt-3 d-block">
                                    <i class="fas fa-book-open"></i> View Full Course
                                </a>
                                <a href="<?= base_url('career-transition/download-pdf') ?>" class="btn btn-info btn-sm mt-2 d-block">
                                    <i class="fas fa-file-pdf"></i> Download PDF
                                </a>
                                <button class="btn btn-warning btn-sm mt-2 d-block" onclick="if(confirm('Save current path to history and start a new one? Your progress will be preserved.')) window.location.href='<?= base_url('career-transition/reset') ?>'">
                                    <i class="fas fa-sync"></i> Change Career Path
                                </button>
                            </div>
                        </div>

                        <div class="card transition-panel mt-3">
                            <div class="card-body">
                                <h6>Skill Gaps</h6>
                                <ul class="mb-0 pl-3">
                                    <?php
                                    $skillGaps = json_decode($transition['skill_gaps'], true);
                                    if ($skillGaps && count($skillGaps) > 0):
                                        foreach ($skillGaps as $skill): ?>
                                            <li><?= esc($skill) ?></li>
                                        <?php endforeach;
                                    else: ?>
                                        <li class="text-muted">Analyzing skills...</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap transition-task-head">
                            <h5 class="mb-0">Daily Learning Tasks</h5>
                            <small class="text-muted">5-10 minutes each</small>
                        </div>
                        <p class="text-muted">Each task maps directly to a module and lesson in your generated course.</p>

                        <?php if (!empty($tasks) && count($tasks) > 0): ?>
                            <?php foreach ($tasks as $task): ?>
                                <div class="card transition-task-card mb-3 <?= $task['is_completed'] ? 'task-completed' : '' ?>">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between transition-task-row">
                                            <div class="flex-grow-1">
                                                <h6>Day <?= (int)$task['day_number'] ?>: <?= esc($task['task_title']) ?></h6>
                                                <p class="mb-1"><?= esc($task['task_description']) ?></p>
                                                <small class="text-muted"><i class="far fa-clock"></i> <?= (int)$task['duration_minutes'] ?> minutes</small>
                                                <?php if (!empty($task['module_number'])): ?>
                                                    <span class="badge badge-info ml-2">
                                                        Module <?= (int)$task['module_number'] ?>
                                                        <?= !empty($task['lesson_number']) ? ' - Lesson ' . (int)$task['lesson_number'] : '' ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <?php if (!$task['is_completed']): ?>
                                                    <button class="btn btn-sm btn-success" onclick="completeTask(<?= (int)$task['id'] ?>)">
                                                        <i class="fas fa-check"></i> Complete
                                                    </button>
                                                <?php else: ?>
                                                    <span class="badge badge-success"><i class="fas fa-check"></i> Done</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-info" data-auto-refresh="1" data-refresh-delay="5000">
                                <p class="mb-0"><i class="fas fa-sync-alt"></i> Tasks are being generated. This page will refresh shortly.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
