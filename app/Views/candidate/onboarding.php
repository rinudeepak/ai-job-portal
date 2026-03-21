<?= view('Layouts/candidate_header', ['title' => 'Complete Your Profile']) ?>

<?php
$stepLabels = [
    'personal' => 'Personal Details',
    'resume' => 'Resume Upload',
    'skills' => 'Skills',
    'education' => 'Education',
    'experience' => 'Experience',
    'preferences' => 'Preferences',
    'review' => 'Review',
];
$stepDescriptions = [
    'personal' => 'Add the key identity and contact details recruiters need first.',
    'resume' => 'Upload your resume so the portal can use it for jobs and matching.',
    'skills' => 'List your strongest skills in a recruiter-friendly format.',
    'education' => 'Add at least one education record to complete your academic background.',
    'experience' => 'Add work experience, or mark yourself as a fresher.',
    'preferences' => 'Capture work preferences that improve job recommendations.',
    'review' => 'Confirm everything before entering the portal.',
];
$currentStepTitle = $stepLabels[$activeStep] ?? 'Onboarding';
?>

<div class="onboarding-jobboard">
<div class="container">
    <div class="page-board-header page-board-header-tight">
        <div class="page-board-copy">
            <span class="page-board-kicker"><i class="fas fa-user-plus"></i> Onboarding</span>
            <h1 class="page-board-title">Complete Your Candidate Profile</h1>
            <p class="page-board-subtitle">Follow the step-by-step setup flow to finish your profile and enter the portal fully prepared.</p>
            <div class="company-profile-meta">
                <span class="meta-chip"><strong><?= (int) $progressPercent ?>%</strong> Complete</span>
                <span class="meta-chip"><strong><?= esc($currentStepTitle) ?></strong> Current step</span>
            </div>
        </div>
        <div class="page-board-actions">
            <?php if (!empty($user['resume_path'])): ?>
                <a href="<?= base_url('candidate/download-resume') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-download mr-1"></i> Download Resume
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="onboarding-jobboard">
<section class="site-section pt-0 content-wrap">
    <div class="container pt-4">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>
        <?php if (session('validation')): ?>
            <div class="alert alert-danger">
                <?php foreach (session('validation')->getErrors() as $error): ?>
                    <div><?= esc($error) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="onboarding-wrap">
            <aside class="onboarding-side">
                <div class="text-uppercase text-muted small font-weight-bold">Profile Setup</div>
                <div class="onboarding-progress-bar">
                    <div class="onboarding-progress-fill" style="width: <?= (int) $progressPercent ?>%;"></div>
                </div>
                <div class="font-weight-bold mb-3"><?= (int) $progressPercent ?>% complete</div>

                <div class="onboarding-step-list">
                    <?php foreach ($steps as $index => $step): ?>
                        <?php
                        $classes = ['onboarding-step-item'];
                        if ($step === $activeStep) {
                            $classes[] = 'is-active';
                        } elseif (!empty($completionMap[$step])) {
                            $classes[] = 'is-done';
                        }
                        ?>
                        <div class="<?= esc(implode(' ', $classes)) ?>">
                            <div class="onboarding-step-index">Step <?= $index + 1 ?></div>
                            <div class="font-weight-bold"><?= esc($stepLabels[$step] ?? ucfirst($step)) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>

            <div class="onboarding-content">
                <div class="onboarding-header">
                    <h2><?= esc($currentStepTitle) ?></h2>
                    <p><?= esc($stepDescriptions[$activeStep] ?? '') ?></p>
                </div>

                <?php if ($activeStep === 'personal'): ?>
                    <form method="post" action="<?= base_url('candidate/onboarding/personal') ?>" data-onboarding-form>
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Full Name</label>
                                <input type="text" name="name" class="form-control" value="<?= esc(old('name', $user['name'] ?? '')) ?>" minlength="3" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="<?= esc(old('email', $user['email'] ?? '')) ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" value="<?= esc(old('phone', $user['phone'] ?? '')) ?>" minlength="10" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Current Location</label>
                                <input type="text" name="location" class="form-control" value="<?= esc(old('location', $user['location'] ?? '')) ?>" minlength="2" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Gender</label>
                                <?php $gender = (string) old('gender', $user['gender'] ?? ''); ?>
                                <select name="gender" class="form-control" required>
                                    <option value="">Select gender</option>
                                    <option value="Male" <?= $gender === 'Male' ? 'selected' : '' ?>>Male</option>
                                    <option value="Female" <?= $gender === 'Female' ? 'selected' : '' ?>>Female</option>
                                    <option value="Other" <?= $gender === 'Other' ? 'selected' : '' ?>>Other</option>
                                    <option value="Prefer not to say" <?= $gender === 'Prefer not to say' ? 'selected' : '' ?>>Prefer not to say</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Date of Birth</label>
                                <input type="date" name="date_of_birth" class="form-control" value="<?= esc(old('date_of_birth', $user['date_of_birth'] ?? '')) ?>" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label>Professional Summary</label>
                                <textarea name="bio" class="form-control" rows="5" minlength="20" required><?= esc(old('bio', $user['bio'] ?? '')) ?></textarea>
                            </div>
                        </div>
                        <div class="onboarding-actions">
                            <button type="submit" class="btn btn-primary" data-onboarding-submit disabled>Save and Continue</button>
                        </div>
                    </form>
                <?php elseif ($activeStep === 'resume'): ?>
                    <form method="post" action="<?= base_url('candidate/onboarding/resume') ?>" enctype="multipart/form-data" data-onboarding-form>
                        <?= csrf_field() ?>
                        <div class="onboarding-card">
                            <div class="mb-3">
                                <label>Upload Resume</label>
                                <input type="file" name="resume" class="form-control-file" accept=".pdf,.doc,.docx" required>
                            </div>
                            <?php if (!empty($user['resume_path'])): ?>
                                <p class="text-muted mb-0">Current resume: <?= esc(basename((string) $user['resume_path'])) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="onboarding-actions">
                            <button type="submit" class="btn btn-primary" data-onboarding-submit disabled>Save and Continue</button>
                        </div>
                    </form>
                <?php elseif ($activeStep === 'skills'): ?>
                    <form method="post" action="<?= base_url('candidate/onboarding/skills') ?>" data-onboarding-form>
                        <?= csrf_field() ?>
                        <div class="onboarding-card">
                            <label>Skills</label>
                            <textarea name="skills" class="form-control" rows="4" placeholder="PHP, MySQL, JavaScript, Laravel" minlength="2" required><?= esc(old('skills', $skillsValue)) ?></textarea>
                            <small class="text-muted">Use comma-separated skills so matching works properly.</small>
                        </div>
                        <div class="onboarding-actions">
                            <button type="submit" class="btn btn-primary" data-onboarding-submit disabled>Save and Continue</button>
                        </div>
                    </form>
                <?php elseif ($activeStep === 'education'): ?>
                    <form method="post" action="<?= base_url('candidate/onboarding/education') ?>" data-onboarding-form>
                        <?= csrf_field() ?>
                        <?php $educationItems = !empty($educationRows) ? $educationRows : [[]]; ?>
                        <div class="repeatable-list" id="educationList">
                            <?php foreach ($educationItems as $index => $educationRow): ?>
                                <div class="repeatable-item education-item">
                                    <div class="repeatable-item-title">Education <?= $index + 1 ?></div>
                                    <?php if ($index > 0): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger repeatable-remove" data-remove-item>Remove</button>
                                    <?php endif; ?>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Degree</label>
                                            <input type="text" name="degree[]" class="form-control" value="<?= esc($educationRow['degree'] ?? '') ?>" minlength="2" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Field of Study</label>
                                            <input type="text" name="field_of_study[]" class="form-control" value="<?= esc($educationRow['field_of_study'] ?? '') ?>" minlength="2" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Institution</label>
                                            <input type="text" name="institution[]" class="form-control" value="<?= esc($educationRow['institution'] ?? '') ?>" minlength="2" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label>Start Year</label>
                                            <input type="number" name="start_year[]" class="form-control" value="<?= esc($educationRow['start_year'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <label>End Year</label>
                                            <input type="number" name="end_year[]" class="form-control" value="<?= esc($educationRow['end_year'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Grade / CGPA</label>
                                            <input type="text" name="grade[]" class="form-control" value="<?= esc($educationRow['grade'] ?? '') ?>">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="onboarding-actions">
                            <button type="button" class="btn btn-outline-secondary" id="addEducationItem">+ Add Education</button>
                            <button type="submit" class="btn btn-primary" data-onboarding-submit disabled>Save and Continue</button>
                        </div>
                    </form>
                <?php elseif ($activeStep === 'experience'): ?>
                    <form method="post" action="<?= base_url('candidate/onboarding/experience') ?>" data-onboarding-form>
                        <?= csrf_field() ?>
                        <div class="onboarding-card mb-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_fresher_candidate" name="is_fresher_candidate" value="1" <?= (int) old('is_fresher_candidate', $user['is_fresher_candidate'] ?? 0) === 1 ? 'checked' : '' ?>>
                                <label class="custom-control-label" for="is_fresher_candidate">I am a fresher / I do not have work experience yet</label>
                            </div>
                        </div>
                        <?php $experienceItems = !empty($experienceRows) ? $experienceRows : [[]]; ?>
                        <div class="repeatable-list" id="experienceFields">
                            <?php foreach ($experienceItems as $index => $experienceRow): ?>
                                <div class="repeatable-item experience-item">
                                    <div class="repeatable-item-title">Experience <?= $index + 1 ?></div>
                                    <?php if ($index > 0): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger repeatable-remove" data-remove-item>Remove</button>
                                    <?php endif; ?>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label>Job Title</label>
                                            <input type="text" name="job_title[]" class="form-control" value="<?= esc($experienceRow['job_title'] ?? '') ?>" minlength="2">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Company Name</label>
                                            <input type="text" name="company_name[]" class="form-control" value="<?= esc($experienceRow['company_name'] ?? '') ?>" minlength="2">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Employment Type</label>
                                            <?php $employmentType = (string) ($experienceRow['employment_type'] ?? 'Full-time'); ?>
                                            <select name="employment_type[]" class="form-control">
                                                <?php foreach (['Full-time', 'Part-time', 'Contract', 'Internship', 'Freelance'] as $type): ?>
                                                    <option value="<?= esc($type) ?>" <?= $employmentType === $type ? 'selected' : '' ?>><?= esc($type) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Location</label>
                                            <input type="text" name="location[]" class="form-control" value="<?= esc($experienceRow['location'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>Start Date</label>
                                            <input type="date" name="start_date[]" class="form-control" value="<?= esc($experienceRow['start_date'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label>End Date</label>
                                            <input type="date" name="end_date[]" class="form-control" value="<?= esc($experienceRow['end_date'] ?? '') ?>">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="is_current_<?= $index ?>" name="is_current[<?= $index ?>]" value="1" <?= !empty($experienceRow['is_current']) ? 'checked' : '' ?>>
                                                <label class="custom-control-label" for="is_current_<?= $index ?>">I currently work here</label>
                                            </div>
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label>Work Summary</label>
                                            <textarea name="description[]" class="form-control" rows="4"><?= esc($experienceRow['description'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="onboarding-actions">
                            <button type="button" class="btn btn-outline-secondary" id="addExperienceItem">+ Add Experience</button>
                            <button type="submit" class="btn btn-primary" data-onboarding-submit disabled>Save and Continue</button>
                        </div>
                    </form>
                <?php elseif ($activeStep === 'preferences'): ?>
                    <form method="post" action="<?= base_url('candidate/onboarding/preferences') ?>" data-onboarding-form>
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label>Resume Headline</label>
                                <input type="text" name="resume_headline" class="form-control" value="<?= esc(old('resume_headline', $user['resume_headline'] ?? '')) ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Preferred Job Titles</label>
                                <input type="text" name="preferred_job_titles" class="form-control" value="<?= esc(old('preferred_job_titles', $user['preferred_job_titles'] ?? '')) ?>" minlength="2" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Preferred Locations</label>
                                <input type="text" name="preferred_locations" class="form-control" value="<?= esc(old('preferred_locations', $user['preferred_locations'] ?? '')) ?>" minlength="2" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Preferred Employment Type</label>
                                <?php $preferredEmploymentType = (string) old('preferred_employment_type', $user['preferred_employment_type'] ?? ''); ?>
                                <select name="preferred_employment_type" class="form-control" required>
                                    <option value="">Select employment type</option>
                                    <?php foreach (['Full-time', 'Part-time', 'Contract', 'Internship', 'Freelance'] as $employmentTypeOption): ?>
                                        <option value="<?= esc($employmentTypeOption) ?>" <?= $preferredEmploymentType === $employmentTypeOption ? 'selected' : '' ?>><?= esc($employmentTypeOption) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Notice Period</label>
                                <?php $noticePeriod = (string) old('notice_period', $user['notice_period'] ?? ''); ?>
                                <select name="notice_period" class="form-control" required>
                                    <option value="">Select notice period</option>
                                    <?php foreach (['Immediate', '1 Month', '2 Months', '3 Months', 'More than 3 Months'] as $period): ?>
                                        <option value="<?= esc($period) ?>" <?= $noticePeriod === $period ? 'selected' : '' ?>><?= esc($period) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Expected Salary (LPA)</label>
                                <input type="number" step="0.01" min="0" name="expected_salary" class="form-control" value="<?= esc(old('expected_salary', $user['expected_salary'] ?? '')) ?>">
                            </div>
                            <div class="col-12">
                                <small class="text-muted">These preferences are reused for job alerts and preference-based job recommendations.</small>
                            </div>
                        </div>
                        <div class="onboarding-actions">
                            <button type="submit" class="btn btn-primary" data-onboarding-submit disabled>Save and Continue</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="onboarding-summary-grid">
                        <?php foreach ($completionMap as $step => $done): ?>
                            <div class="onboarding-summary-card">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong><?= esc($stepLabels[$step] ?? ucfirst($step)) ?></strong>
                                    <span class="badge badge-<?= !empty($done) ? 'success' : 'secondary' ?>"><?= !empty($done) ? 'Done' : 'Pending' ?></span>
                                </div>
                                <div class="text-muted small"><?= esc($stepDescriptions[$step] ?? '') ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <form method="post" action="<?= base_url('candidate/onboarding/review') ?>" data-onboarding-form>
                        <?= csrf_field() ?>
                        <div class="onboarding-actions">
                            <button type="submit" class="btn btn-primary" data-onboarding-submit>Finish and Go to Dashboard</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
</div>
</div>

<?= view('Layouts/candidate_footer') ?>
