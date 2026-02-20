<?= view('Layouts/candidate_header', ['title' => 'Course Modules']) ?>

<div class="course-modules-jobboard">
    <div class="offline-badge online" id="offlineStatus">Online</div>

    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1 class="text-white font-weight-bold">Course Modules</h1>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                        <span class="mx-2 slash">/</span>
                        <a href="<?= base_url('career-transition') ?>">Career Transition AI</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong>Course Modules</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section pt-0 content-wrap">
        <div class="container">
            <div class="card transition-panel course-header-panel mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap transition-header-row">
                        <div class="flex-grow-1">
                            <h3 class="mb-1">Your Learning Journey</h3>
                            <h5 class="mb-2"><?= esc($transition['current_role']) ?> to <?= esc($transition['target_role']) ?></h5>
                            <p class="text-muted mb-0">Open any module to start learning. Course content is available for offline-ready usage.</p>
                        </div>
                        <div class="mt-2 mt-md-0">
                            <a href="<?= base_url('career-transition') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>

                    <div class="course-download-box mt-4">
                        <h6><i class="fas fa-file-pdf text-danger"></i> Download Offline PDF</h6>
                        <p class="text-muted mb-3">Get a complete PDF with modules, lessons, resources, and exercises.</p>
                        <button class="btn btn-primary" id="coursePdfBtn" data-download-url="<?= base_url('career-transition/download-pdf') ?>">
                            <span id="coursePdfBtnText"><i class="fas fa-download"></i> Download Complete Course PDF</span>
                            <span id="coursePdfBtnLoading" class="transition-btn-loading"><i class="fas fa-spinner fa-spin"></i> Generating PDF...</span>
                        </button>
                    </div>
                </div>
            </div>

            <?php if (empty($modules)): ?>
                <div class="alert alert-info">
                    <h5>No course content available yet.</h5>
                    <p class="mb-0">Generate your career transition roadmap first.</p>
                </div>
            <?php else: ?>
                <?php foreach ($modules as $module): ?>
                    <a href="<?= base_url('career-transition/module/' . (int) $module['id']) ?>" class="course-module-card">
                        <div class="course-module-number"><?= (int) $module['module_number'] ?></div>
                        <div class="course-module-content">
                            <h5 class="mb-1"><?= esc($module['title']) ?></h5>
                            <p class="text-muted mb-2"><?= esc($module['description']) ?></p>
                            <span class="course-module-duration"><i class="far fa-clock"></i> <?= (int) $module['duration_weeks'] ?> week(s)</span>
                        </div>
                        <span class="course-module-arrow"><i class="fas fa-arrow-right"></i></span>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
