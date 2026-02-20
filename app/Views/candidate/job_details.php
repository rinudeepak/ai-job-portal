<?= view('Layouts/candidate_header', ['title' => 'Job Details']) ?>

<div class="job-details-jobboard">
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1 class="text-white font-weight-bold"><?= esc($job['title']) ?></h1>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                        <span class="mx-2 slash">/</span>
                        <a href="<?= base_url('jobs') ?>">Jobs</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong><?= esc($job['title']) ?></strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section content-wrap">
        <div class="container">
            <div class="row align-items-center mb-5">
                <div class="col-lg-8 mb-4 mb-lg-0">
                    <div class="d-flex align-items-center">
                        <div class="border p-2 d-inline-block mr-3 rounded">
                            <div class="job-details-logo">
                                <?= strtoupper(substr($job['company'] ?? 'J', 0, 1)) ?>
                            </div>
                        </div>
                        <div>
                            <h2 class="mb-1"><?= esc($job['title']) ?></h2>
                            <div>
                                <span class="ml-0 mr-2 mb-2 d-inline-block">
                                    <span class="icon-briefcase mr-2"></span><?= esc($job['company']) ?>
                                </span>
                                <span class="m-2 d-inline-block">
                                    <span class="icon-room mr-2"></span><?= esc($job['location']) ?>
                                </span>
                                <span class="m-2 d-inline-block">
                                    <span class="icon-clock-o mr-2"></span>
                                    <span class="text-primary"><?= esc(ucwords(str_replace('-', ' ', $job['employment_type'] ?? 'Full Time'))) ?></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-12">
                            <?php if ($alreadyApplied): ?>
                                <button class="btn btn-block btn-light btn-md" disabled>
                                    <span class="icon-check mr-2 text-success"></span>Already Applied
                                </button>
                            <?php else: ?>
                                <form method="post" action="<?= base_url('job/apply/' . $job['id']) ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-block btn-primary btn-md">Apply Now</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= esc(session()->getFlashdata('success')) ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="mb-5">
                        <h3 class="h5 d-flex align-items-center mb-4 text-primary">
                            <span class="icon-align-left mr-3"></span>Job Description
                        </h3>
                        <p><?= nl2br(esc($job['description'])) ?></p>
                    </div>

                    <div class="mb-5">
                        <h3 class="h5 d-flex align-items-center mb-4 text-primary">
                            <span class="icon-rocket mr-3"></span>Required Knowledge, Skills, and Abilities
                        </h3>
                        <?php
                        $skills = array_filter(array_map('trim', explode(',', $job['required_skills'] ?? '')));
                        ?>
                        <?php if (!empty($skills)): ?>
                            <ul class="list-unstyled m-0 p-0">
                                <?php foreach ($skills as $skill): ?>
                                    <li class="d-flex align-items-start mb-2">
                                        <span class="icon-check_circle mr-2 text-muted"></span>
                                        <span><?= esc($skill) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <ul class="list-unstyled m-0 p-0">
                                <li class="d-flex align-items-start mb-2">
                                    <span class="icon-check_circle mr-2 text-muted"></span>
                                    <span>Skills not specified.</span>
                                </li>
                            </ul>
                        <?php endif; ?>
                    </div>

                    <div class="mb-5">
                        <h3 class="h5 d-flex align-items-center mb-4 text-primary">
                            <span class="icon-book mr-3"></span>Education + Experience
                        </h3>
                        <ul class="list-unstyled m-0 p-0">
                            <li class="d-flex align-items-start mb-2">
                                <span class="icon-check_circle mr-2 text-muted"></span>
                                <span><?= esc($job['experience_level'] ?? 'Not specified') ?></span>
                            </li>
                        </ul>
                    </div>

                    <div class="row mb-5">
                        <div class="col-12">
                            <?php if ($alreadyApplied): ?>
                                <button class="btn btn-block btn-light btn-md" disabled>
                                    <span class="icon-check mr-2 text-success"></span>Already Applied
                                </button>
                            <?php else: ?>
                                <form method="post" action="<?= base_url('job/apply/' . $job['id']) ?>">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-block btn-primary btn-md">Apply Now</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <?php if (session()->getFlashdata('career_suggestion')):
                        $suggestion = session()->getFlashdata('career_suggestion'); ?>
                        <div class="bg-light p-3 border rounded mb-4">
                            <h3 class="text-primary mt-2 h5 mb-2"><i class="fas fa-rocket mr-2"></i>Career Transition Opportunity</h3>
                            <p class="small mb-2"><?= esc($suggestion['message']) ?></p>
                            <a href="<?= base_url('career-transition') ?>" class="btn btn-sm btn-primary btn-block">
                                <i class="fas fa-graduation-cap mr-1"></i> Get Learning Roadmap
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="bg-light p-3 border rounded mb-4">
                        <h3 class="text-primary mt-3 h5 pl-3 mb-3">Job Summary</h3>
                        <ul class="list-unstyled pl-3 mb-0">
                            <li class="mb-2"><strong class="text-black">Published on:</strong> <?= date('d M Y', strtotime($job['created_at'])) ?></li>
                            <li class="mb-2"><strong class="text-black">Company:</strong> <?= esc($job['company']) ?></li>
                            <li class="mb-2"><strong class="text-black">Employment Status:</strong> <?= esc(ucwords(str_replace('-', ' ', $job['employment_type'] ?? 'Full Time'))) ?></li>
                            <li class="mb-2"><strong class="text-black">Experience:</strong> <?= esc($job['experience_level'] ?? 'Not specified') ?></li>
                            <li class="mb-2"><strong class="text-black">Job Location:</strong> <?= esc($job['location']) ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
