<?= view('Layouts/candidate_header', ['title' => 'Job Details']) ?>
<?php
$companyRefId = (int) ($job['company_id'] ?? 0);
if ($companyRefId <= 0) {
    $companyRefId = (int) ($job['recruiter_id'] ?? 0);
}
$companyProfileUrl = $companyRefId > 0 ? base_url('company/' . $companyRefId) : '#';
$isSaved = (bool) ($isSaved ?? false);
?>
<style>
.job-details-jobboard .ai-policy-card {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 12px;
    border-radius: 10px;
    border: 1px solid #dfe6f5;
    background: #f8fbff;
    margin-bottom: 12px;
}
.job-details-jobboard .ai-policy-card .ai-policy-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    flex: 0 0 28px;
}
.job-details-jobboard .ai-policy-card .ai-policy-title {
    font-size: 13px;
    font-weight: 700;
    line-height: 1.2;
    color: #1f2f57;
    margin-bottom: 2px;
}
.job-details-jobboard .ai-policy-card .ai-policy-desc {
    font-size: 12px;
    color: #607295;
    line-height: 1.3;
    margin: 0;
}
.job-details-jobboard .ai-policy-off { background: #f7f9fc; border-color: #dde3ed; }
.job-details-jobboard .ai-policy-off .ai-policy-icon { background: #e8edf4; color: #4b5f80; }
.job-details-jobboard .ai-policy-optional { background: #f2f9ff; border-color: #cfe5ff; }
.job-details-jobboard .ai-policy-optional .ai-policy-icon { background: #dceeff; color: #2f78c6; }
.job-details-jobboard .ai-policy-soft { background: #fffaf0; border-color: #f4dfb8; }
.job-details-jobboard .ai-policy-soft .ai-policy-icon { background: #ffedcc; color: #b87b1b; }
.job-details-jobboard .ai-policy-hard { background: #fff4f4; border-color: #f2c7c7; }
.job-details-jobboard .ai-policy-hard .ai-policy-icon { background: #f8dada; color: #b53d3d; }
</style>

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
                            <a href="<?= esc($companyProfileUrl) ?>" title="View company profile">
                                <div class="job-details-logo">
                                    <?= strtoupper(substr($job['company'] ?? 'J', 0, 1)) ?>
                                </div>
                            </a>
                        </div>
                        <div>
                            <h2 class="mb-1"><?= esc($job['title']) ?></h2>
                            <div>
                                <span class="ml-0 mr-2 mb-2 d-inline-block">
                                    <span class="icon-briefcase mr-2"></span>
                                    <a href="<?= esc($companyProfileUrl) ?>"><?= esc($job['company']) ?></a>
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
                    <?php
                    $policyRaw = strtoupper($job['ai_interview_policy'] ?? 'REQUIRED_HARD');
                    $policyMap = [
                        'OFF' => [
                            'title' => 'AI Interview: Not Required',
                            'desc' => 'You can apply directly. No AI round is needed for this job.',
                            'class' => 'ai-policy-off',
                            'icon' => 'fas fa-check-circle'
                        ],
                        'OPTIONAL' => [
                            'title' => 'AI Interview: Optional',
                            'desc' => 'Optional AI round is available and may improve your visibility.',
                            'class' => 'ai-policy-optional',
                            'icon' => 'fas fa-lightbulb'
                        ],
                        'REQUIRED_SOFT' => [
                            'title' => 'AI Interview: Required + Recruiter Review',
                            'desc' => 'AI round is required, and recruiter can still make the final decision.',
                            'class' => 'ai-policy-soft',
                            'icon' => 'fas fa-user-check'
                        ],
                        'REQUIRED_HARD' => [
                            'title' => 'AI Interview: Mandatory Screening',
                            'desc' => 'AI interview is mandatory and works as the primary screening gate.',
                            'class' => 'ai-policy-hard',
                            'icon' => 'fas fa-shield-alt'
                        ],
                    ];
                    $policy = $policyMap[$policyRaw] ?? $policyMap['REQUIRED_HARD'];
                    ?>
                    <div class="ai-policy-card <?= esc($policy['class']) ?>">
                        <span class="ai-policy-icon"><i class="<?= esc($policy['icon']) ?>"></i></span>
                        <div>
                            <div class="ai-policy-title"><?= esc($policy['title']) ?></div>
                            <p class="ai-policy-desc"><?= esc($policy['desc']) ?></p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <a href="<?= base_url($isSaved ? 'job/unsave/' . $job['id'] : 'job/save/' . $job['id']) ?>" class="btn btn-block btn-outline-secondary btn-md mb-2">
                                <span class="<?= $isSaved ? 'fas' : 'far' ?> fa-bookmark mr-2"></span><?= $isSaved ? 'Saved' : 'Save Job' ?>
                            </a>
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
                            <li class="mb-2"><strong class="text-black">Company:</strong> <a href="<?= esc($companyProfileUrl) ?>"><?= esc($job['company']) ?></a></li>
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
