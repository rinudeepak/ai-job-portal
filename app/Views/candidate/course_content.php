<?= view('Layouts/candidate_header', ['title' => esc($module['title'] ?? 'Course Module')]) ?>

<div class="course-content-jobboard">
    <div class="offline-badge online" id="offlineStatus">Online</div>

    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1 class="text-white font-weight-bold"><?= esc($module['title']) ?></h1>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                        <span class="mx-2 slash">/</span>
                        <a href="<?= base_url('career-transition') ?>">Career Transition AI</a>
                        <span class="mx-2 slash">/</span>
                        <a href="<?= base_url('career-transition/course') ?>">Modules</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong>Module <?= (int) $module['module_number'] ?></strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section pt-0 content-wrap">
        <div class="container">
            <div class="card transition-panel course-header-panel mb-4">
                <div class="card-body d-flex justify-content-between align-items-start flex-wrap transition-header-row">
                    <div>
                        <span class="badge badge-light mb-2">Module <?= (int) $module['module_number'] ?></span>
                        <h4 class="mb-1"><?= esc($module['title']) ?></h4>
                        <p class="text-muted mb-0"><?= esc($module['description']) ?></p>
                        <small class="text-muted d-block mt-2"><i class="far fa-clock"></i> <?= (int) $module['duration_weeks'] ?> week(s)</small>
                    </div>
                    <div class="mt-2 mt-md-0">
                        <a href="<?= base_url('career-transition/course') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> All Modules
                        </a>
                    </div>
                </div>
            </div>

            <?php if (empty($lessons)): ?>
                <div class="alert alert-warning">No lessons available for this module.</div>
            <?php else: ?>
                <?php foreach ($lessons as $lesson): ?>
                    <div class="card course-lesson-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <span class="course-lesson-number"><?= (int) $lesson['lesson_number'] ?></span>
                                <h5 class="mb-0"><?= esc($lesson['title']) ?></h5>
                            </div>

                            <div class="course-lesson-content">
                                <?= nl2br(esc($lesson['content'])) ?>
                            </div>

                            <div class="mt-4">
                                <h6 class="course-section-title"><i class="fas fa-book"></i> Learning Resources</h6>
                                <div>
                                    <?php
                                    $resources = is_string($lesson['resources']) ? json_decode($lesson['resources'], true) : $lesson['resources'];
                                    if (!empty($resources) && is_array($resources)):
                                        foreach ($resources as $resource):
                                            if (filter_var($resource, FILTER_VALIDATE_URL)): ?>
                                                <a href="<?= esc($resource) ?>" target="_blank" class="course-resource-link">
                                                    <i class="fas fa-link"></i> <?= esc(parse_url($resource, PHP_URL_HOST)) ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="course-resource-link muted"><?= esc($resource) ?></span>
                                            <?php endif;
                                        endforeach;
                                    else: ?>
                                        <p class="text-muted mb-0">No additional resources for this lesson.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mt-4">
                                <h6 class="course-section-title"><i class="fas fa-pen"></i> Practice Exercises</h6>
                                <?php
                                $exercises = is_string($lesson['exercises']) ? json_decode($lesson['exercises'], true) : $lesson['exercises'];
                                if (!empty($exercises) && is_array($exercises)):
                                    foreach ($exercises as $index => $exercise): ?>
                                        <div class="course-exercise-item">
                                            <strong>Exercise <?= (int) $index + 1 ?>:</strong> <?= esc($exercise) ?>
                                        </div>
                                    <?php endforeach;
                                else: ?>
                                    <p class="text-muted mb-0">No exercises for this lesson.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="text-center mt-4">
                    <a href="<?= base_url('career-transition/course') ?>" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-arrow-left"></i> Back to All Modules
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
