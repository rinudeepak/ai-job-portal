<?= view('Layouts/candidate_header', ['title' => 'Job Details']) ?>
<?php
$companyRefId = (int) ($job['company_id'] ?? 0);
if ($companyRefId <= 0) {
    $companyRefId = (int) ($job['recruiter_id'] ?? 0);
}
$companyProfileUrl = $companyRefId > 0 ? base_url('company/' . $companyRefId) : '#';
$isSaved = (bool) ($isSaved ?? false);
$company = is_array($company ?? null) ? $company : [];
$brandingPhotos = [];
$brandingPhotosRaw = $company['workplace_photos'] ?? '';
if (is_string($brandingPhotosRaw) && trim($brandingPhotosRaw) !== '') {
    $decodedBrandingPhotos = json_decode($brandingPhotosRaw, true);
    if (is_array($decodedBrandingPhotos)) {
        $brandingPhotos = array_values(array_filter(array_map('strval', $decodedBrandingPhotos)));
    }
}
$resolveAssetUrl = static function (string $path): string {
    $path = trim($path);
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path) || str_starts_with($path, '//')) {
        return $path;
    }
    return base_url(ltrim($path, '/'));
};
$benefitsRaw = trim((string) ($company['employee_benefits'] ?? ''));
$benefits = [];
if ($benefitsRaw !== '') {
    $benefits = preg_split('/[\r\n,]+/', $benefitsRaw) ?: [];
    $benefits = array_values(array_filter(array_map('trim', $benefits)));
}
$cultureSummary = trim((string) ($company['culture_summary'] ?? ''));
$resumeCoach = is_array($resumeCoach ?? null) ? $resumeCoach : [];
$successFlash = session()->getFlashdata('success');
$errorFlash = session()->getFlashdata('error');
$careerSuggestion = session()->getFlashdata('career_suggestion');
$skills = array_filter(array_map('trim', explode(',', (string) ($job['required_skills'] ?? ''))));
$jobTypeLabel = ucwords(str_replace('-', ' ', (string) ($job['employment_type'] ?? 'Full Time')));
$jobLocation = (string) ($job['location'] ?? 'Not specified');
$jobCompany = (string) ($job['company'] ?? 'Company');
$jobCategory = trim((string) ($job['category'] ?? ''));
$jobExperience = (string) ($job['experience_level'] ?? 'Not specified');
$jobSalary = trim((string) ($job['salary_range'] ?? ''));
$jobDeadline = trim((string) ($job['application_deadline'] ?? ''));
$jobOpenings = trim((string) ($job['openings'] ?? ''));
$jobPublished = !empty($job['created_at']) ? date('d M Y', strtotime((string) $job['created_at'])) : null;
$companyInitial = strtoupper(substr($jobCompany, 0, 1) ?: 'C');

$summaryRows = [
    ['Published on', $jobPublished ?: 'Not specified'],
    ['Company', $jobCompany],
    ['Employment', $jobTypeLabel],
    ['Experience', $jobExperience],
    ['Location', $jobLocation],
];
if ($jobCategory !== '') {
    $summaryRows[] = ['Category', $jobCategory];
}
if ($jobSalary !== '') {
    $summaryRows[] = ['Salary Range', $jobSalary];
}
if ($jobDeadline !== '') {
    $summaryRows[] = ['Deadline', date('d M Y', strtotime($jobDeadline))];
}
if ($jobOpenings !== '') {
    $summaryRows[] = ['Openings', $jobOpenings];
}

$policyRaw = strtoupper($job['ai_interview_policy'] ?? 'REQUIRED_HARD');
$policyMap = [
    'OFF' => [
        'title' => 'AI Interview: Not Required',
        'desc' => 'You can apply directly. No AI round is needed for this job.',
        'class' => 'ai-policy-off',
        'icon' => 'fas fa-check-circle',
    ],
    'OPTIONAL' => [
        'title' => 'AI Interview: Optional',
        'desc' => 'Optional AI round is available and may improve your visibility.',
        'class' => 'ai-policy-optional',
        'icon' => 'fas fa-lightbulb',
    ],
    'REQUIRED_SOFT' => [
        'title' => 'AI Interview: Required + Recruiter Review',
        'desc' => 'AI round is required, and recruiter can still make the final decision.',
        'class' => 'ai-policy-soft',
        'icon' => 'fas fa-user-check',
    ],
    'REQUIRED_HARD' => [
        'title' => 'AI Interview: Mandatory Screening',
        'desc' => 'AI interview is mandatory and works as the primary screening gate.',
        'class' => 'ai-policy-hard',
        'icon' => 'fas fa-shield-alt',
    ],
];
$policy = $policyMap[$policyRaw] ?? $policyMap['REQUIRED_HARD'];
?>

<div class="job-details-jobboard">
    <div class="container">
        <div class="job-details-page-header">
            <div class="page-board-copy">
                <span class="page-board-kicker"><i class="fas fa-briefcase"></i> Job details</span>
                <h1 class="page-board-title"><?= esc($job['title']) ?></h1>
                <p class="page-board-subtitle">Review the role, AI screening policy, and company summary before applying.</p>
                <div class="job-details-header-meta">
                    <span class="meta-chip"><i class="fas fa-building"></i> <?= esc($jobCompany) ?></span>
                    <span class="meta-chip"><i class="fas fa-map-pin"></i> <?= esc($jobLocation) ?></span>
                    <span class="meta-chip"><i class="fas fa-clock"></i> <?= esc($jobTypeLabel) ?></span>
                    <?php if ($jobSalary !== ''): ?>
                        <span class="meta-chip"><i class="fas fa-rupee-sign"></i> <?= esc($jobSalary) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="job-details-header-actions">
                <a href="<?= esc($companyProfileUrl) ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-store mr-1"></i> Company Profile
                </a>
                <a href="<?= base_url($isSaved ? 'job/unsave/' . $job['id'] : 'job/save/' . $job['id']) ?>"
                   class="btn btn-outline-secondary js-save-job-toggle"
                   data-save-url="<?= base_url($isSaved ? 'job/unsave/' . $job['id'] : 'job/save/' . $job['id']) ?>"
                   data-job-id="<?= (int) $job['id'] ?>"
                   data-saved="<?= $isSaved ? '1' : '0' ?>"
                   data-save-label-save="Save Job"
                   data-save-label-saved="Saved">
                    <span class="js-save-icon <?= $isSaved ? 'fas' : 'far' ?> fa-bookmark mr-1"></span><span class="js-save-label"><?= $isSaved ? 'Saved' : 'Save Job' ?></span>
                </a>
                <div id="jobDetailsTopAction">
                    <?php if ($alreadyApplied): ?>
                        <button class="btn job-details-applied-btn js-job-details-top-applied" disabled>
                            <i class="fas fa-check mr-1"></i> Already Applied
                        </button>
                    <?php else: ?>
                        <a href="#apply-job" class="btn btn-primary js-job-details-apply-link">
                            <i class="fas fa-paper-plane mr-1"></i> Apply Now
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <section class="site-section pt-0 content-wrap">
        <div class="container">
            <div id="candidateApplicationAjaxAlert"></div>
            <?php if ($successFlash): ?>
                <div class="alert alert-success">
                    <?= esc($successFlash) ?>
                </div>
            <?php endif; ?>

            <?php if ($errorFlash): ?>
                <div class="alert alert-danger">
                    <?= esc($errorFlash) ?>
                </div>
            <?php endif; ?>

            <div class="job-details-layout">
                <div class="job-details-main">
                    <?php if (!$alreadyApplied && !empty($resumeCoach)): ?>
                        <div class="resume-coach-card">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                                <div>
                                    <h3 class="h5 mb-1 text-primary"><i class="fas fa-magic mr-2"></i>Job-specific Resume Coach</h3>
                                    <p class="text-muted mb-0">See how well your current resume/profile aligns with this job before you apply.</p>
                                </div>
                                <div class="text-right">
                                    <div class="resume-coach-score"><?= (int) ($resumeCoach['score'] ?? 0) ?></div>
                                    <small class="text-muted">ATS readiness</small>
                                </div>
                            </div>

                            <?php if (!empty($resumeCoach['resume_version']['title'])): ?>
                                <div class="alert alert-light border mb-3">
                                    Using resume version: <strong><?= esc($resumeCoach['resume_version']['title']) ?></strong>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($resumeCoach['matched_skills'])): ?>
                                <div class="mb-3">
                                    <div class="small text-uppercase font-weight-bold text-muted mb-2">Matched Skills</div>
                                    <div class="resume-coach-chip-list">
                                        <?php foreach (array_slice((array) $resumeCoach['matched_skills'], 0, 6) as $skill): ?>
                                            <span class="resume-coach-chip match"><?= esc($skill) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($resumeCoach['missing_skills'])): ?>
                                <div class="mb-3">
                                    <div class="small text-uppercase font-weight-bold text-muted mb-2">Missing or Weak Keywords</div>
                                    <div class="resume-coach-chip-list">
                                        <?php foreach (array_slice((array) $resumeCoach['missing_skills'], 0, 6) as $skill): ?>
                                            <span class="resume-coach-chip missing"><?= esc($skill) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <div class="small text-uppercase font-weight-bold text-muted mb-2">Suggested Summary Direction</div>
                                <p class="mb-0"><?= esc((string) ($resumeCoach['summary_suggestion'] ?? 'Tailor your resume summary to this role.')) ?></p>
                            </div>

                            <?php if (!empty($resumeCoach['suggestions'])): ?>
                                <div class="mb-3">
                                    <div class="small text-uppercase font-weight-bold text-muted mb-2">What to Improve</div>
                                    <ul class="resume-coach-suggestion-list">
                                        <?php foreach ((array) $resumeCoach['suggestions'] as $suggestion): ?>
                                            <li><?= esc($suggestion) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <a href="<?= esc((string) ($resumeCoach['resume_studio_url'] ?? base_url('candidate/resume-studio'))) ?>" class="btn btn-primary">
                                <i class="fas fa-file-alt mr-1"></i> Improve Resume for This Job
                            </a>
                        </div>
                    <?php elseif ($alreadyApplied): ?>
                        <div class="resume-coach-card">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                                <div>
                                    <h3 class="h5 mb-1 text-primary"><i class="fas fa-check-circle mr-2"></i>Application Already Submitted</h3>
                                    <p class="text-muted mb-0">Your focus should now move from resume tailoring to interview readiness and application follow-up.</p>
                                </div>
                                <span class="badge badge-light border px-3 py-2">Post-application guidance</span>
                            </div>

                            <div class="mb-3">
                                <div class="small text-uppercase font-weight-bold text-muted mb-2">What to Do Next</div>
                                <ul class="resume-coach-suggestion-list">
                                    <li>Review your submitted application status and recruiter activity from the applications page.</li>
                                    <li>Use the mock interview coach to practice role-specific questions for this job.</li>
                                    <li>Keep your project stories and examples aligned with the resume version you used for this application.</li>
                                </ul>
                            </div>

                            <div class="d-flex flex-wrap" style="gap: 10px;">
                                <a href="<?= base_url('candidate/applications') ?>" class="btn btn-primary">
                                    <i class="fas fa-briefcase mr-1"></i> View My Application
                                </a>
                                <?php if (!empty($application['id'])): ?>
                                    <a href="<?= base_url('candidate/applications/' . (int) $application['id'] . '/mock-interview') ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-comments mr-1"></i> Open Mock Interview
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="detail-card">
                        <div class="detail-card-title">
                            <span class="detail-card-icon"><i class="fas fa-align-left"></i></span>
                            <span>Job Description</span>
                        </div>
                        <div class="job-details-section-text">
                            <p><?= nl2br(esc($job['description'])) ?></p>
                        </div>
                    </div>

                    <div class="detail-card">
                        <div class="detail-card-title">
                            <span class="detail-card-icon"><i class="fas fa-rocket"></i></span>
                            <span>Required Knowledge, Skills, and Abilities</span>
                        </div>
                        <?php if (!empty($skills)): ?>
                            <ul class="job-details-skill-list">
                                <?php foreach ($skills as $skill): ?>
                                    <li><i class="fas fa-check-circle"></i><span><?= esc($skill) ?></span></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="job-details-section-text mb-0">Skills not specified.</p>
                        <?php endif; ?>
                    </div>

                    <div class="detail-card">
                        <div class="detail-card-title">
                            <span class="detail-card-icon"><i class="fas fa-book"></i></span>
                            <span>Education + Experience</span>
                        </div>
                        <p class="job-details-section-text mb-0"><?= esc($jobExperience) ?></p>
                    </div>

                    <?php if ($cultureSummary !== '' || !empty($benefits) || !empty($brandingPhotos)): ?>
                        <div class="detail-card">
                            <div class="detail-card-title">
                                <span class="detail-card-icon"><i class="fas fa-building"></i></span>
                                <span>Company Snapshot</span>
                            </div>
                            <?php if ($cultureSummary !== ''): ?>
                                <p class="job-details-section-text"><?= esc($cultureSummary) ?></p>
                            <?php endif; ?>
                            <?php if (!empty($benefits)): ?>
                                <div class="job-details-chip-list">
                                    <?php foreach (array_slice($benefits, 0, 6) as $benefit): ?>
                                        <span class="summary-chip"><?= esc($benefit) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($brandingPhotos)): ?>
                                <div class="job-details-gallery">
                                    <?php foreach (array_slice($brandingPhotos, 0, 3) as $photo): ?>
                                        <img src="<?= esc($resolveAssetUrl($photo)) ?>" alt="<?= esc($jobCompany) ?>" loading="lazy">
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="detail-card" id="apply-job">
                        <div class="detail-card-title">
                            <span class="detail-card-icon"><i class="fas fa-paper-plane"></i></span>
                            <span>Apply</span>
                        </div>
                        <?php if ($alreadyApplied): ?>
                            <button class="btn btn-block btn-outline-primary btn-md job-details-applied-btn" disabled data-application-state="applied">
                                <span class="icon-check mr-2"></span>Already Applied
                            </button>
                        <?php else: ?>
                            <form method="post" action="<?= base_url('job/apply/' . $job['id']) ?>" class="js-apply-job-form" data-job-id="<?= (int) $job['id'] ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-block btn-primary btn-md">Apply Now</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>

                <aside class="job-details-side">
                    <div class="summary-card policy-card">
                        <div class="ai-policy-card <?= esc($policy['class']) ?>">
                            <span class="ai-policy-icon"><i class="<?= esc($policy['icon']) ?>"></i></span>
                            <div>
                                <div class="ai-policy-title"><?= esc($policy['title']) ?></div>
                                <p class="ai-policy-desc"><?= esc($policy['desc']) ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="summary-card">
                        <div class="detail-card-title mb-3">
                            <span class="detail-card-icon"><i class="fas fa-list"></i></span>
                            <span>Job Summary</span>
                        </div>
                        <ul class="job-details-summary-list">
                            <?php foreach ($summaryRows as [$label, $value]): ?>
                                <li>
                                    <strong><?= esc($label) ?></strong>
                                    <span><?= esc($value) ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="summary-card">
                        <div class="detail-card-title mb-3">
                            <span class="detail-card-icon"><i class="fas fa-store"></i></span>
                            <span>Company</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="job-details-logo mr-3"><?= esc($companyInitial) ?></div>
                            <div>
                                <div class="font-weight-bold"><?= esc($jobCompany) ?></div>
                                <a href="<?= esc($companyProfileUrl) ?>" class="small">View company profile</a>
                            </div>
                        </div>
                        <?php if (!empty($benefits)): ?>
                            <div class="job-details-chip-list">
                                <?php foreach (array_slice($benefits, 0, 4) as $benefit): ?>
                                    <span class="summary-chip"><?= esc($benefit) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ($careerSuggestion): ?>
                        <div class="summary-card career-card">
                            <h3 class="text-primary h5 mb-2"><i class="fas fa-rocket mr-2"></i>Career Transition Opportunity</h3>
                            <p class="small mb-3"><?= esc($careerSuggestion['message']) ?></p>
                            <a href="<?= base_url('career-transition') ?>" class="btn btn-sm btn-primary btn-block">
                                <i class="fas fa-graduation-cap mr-1"></i> Get Learning Roadmap
                            </a>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
