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

<section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h1 class="text-white font-weight-bold">Complete Your Candidate Profile</h1>
                <div class="custom-breadcrumbs">
                    <span class="text-white"><strong>Step-by-step onboarding</strong></span>
                </div>
            </div>
        </div>
    </div>
</section>

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

        <style>
            .onboarding-wrap {
                display: grid;
                grid-template-columns: 300px minmax(0, 1fr);
                gap: 24px;
                align-items: start;
            }
            .onboarding-side,
            .onboarding-content {
                background: #fff;
                border: 1px solid #e8edf3;
                border-radius: 18px;
                box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
            }
            .onboarding-side {
                padding: 20px;
            }
            .onboarding-progress-bar {
                height: 10px;
                border-radius: 999px;
                background: #edf2f7;
                overflow: hidden;
                margin: 14px 0 18px;
            }
            .onboarding-progress-fill {
                height: 100%;
                background: #89ba16;
                border-radius: 999px;
            }
            .onboarding-step-list {
                display: flex;
                flex-direction: column;
                gap: 10px;
            }
            .onboarding-step-item {
                padding: 12px 14px;
                border-radius: 14px;
                background: #f8fafc;
                border: 1px solid #edf2f7;
            }
            .onboarding-step-item.is-active {
                background: #0f172a;
                color: #fff;
                border-color: #0f172a;
            }
            .onboarding-step-item.is-done {
                background: #f3fbdf;
                border-color: #d8ebb0;
            }
            .onboarding-step-index {
                font-size: 12px;
                font-weight: 700;
                letter-spacing: .08em;
                text-transform: uppercase;
                opacity: .75;
            }
            .onboarding-content {
                padding: 28px;
            }
            .onboarding-header h2 {
                font-size: 30px;
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 8px;
            }
            .onboarding-header p {
                color: #64748b;
                margin-bottom: 24px;
            }
            .onboarding-actions {
                display: flex;
                gap: 12px;
                margin-top: 24px;
                flex-wrap: wrap;
            }
            .onboarding-actions .btn[disabled] {
                opacity: .55;
                cursor: not-allowed;
                pointer-events: none;
            }
            .onboarding-card {
                border: 1px solid #edf2f7;
                border-radius: 16px;
                padding: 20px;
                background: #fbfdff;
            }
            .onboarding-summary-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 14px;
            }
            .repeatable-list {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }
            .repeatable-item {
                border: 1px solid #e5eaf1;
                border-radius: 16px;
                padding: 18px;
                background: #fff;
                position: relative;
            }
            .repeatable-item-title {
                font-weight: 700;
                color: #0f172a;
                margin-bottom: 14px;
            }
            .repeatable-remove {
                position: absolute;
                right: 16px;
                top: 16px;
            }
            .onboarding-summary-card {
                border: 1px solid #edf2f7;
                border-radius: 14px;
                padding: 16px;
                background: #fff;
            }
            @media (max-width: 991.98px) {
                .onboarding-wrap {
                    grid-template-columns: 1fr;
                }
                .onboarding-summary-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>

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

<?= view('Layouts/candidate_footer') ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var fresherCheckbox = document.getElementById('is_fresher_candidate');
    var experienceFields = document.getElementById('experienceFields');
    var educationList = document.getElementById('educationList');
    var addEducationItem = document.getElementById('addEducationItem');
    var addExperienceItem = document.getElementById('addExperienceItem');
    var onboardingForms = document.querySelectorAll('[data-onboarding-form]');

    function fieldHasValue(field) {
        if (!field || field.disabled) {
            return true;
        }

        if (field.type === 'file') {
            return field.files && field.files.length > 0;
        }

        if (field.type === 'checkbox' || field.type === 'radio') {
            return field.checked;
        }

        return String(field.value || '').trim() !== '';
    }

    function syncOnboardingSubmit(form) {
        var submit = form.querySelector('[data-onboarding-submit]');
        if (!submit) {
            return;
        }

        var requiredFields = Array.prototype.slice.call(form.querySelectorAll('[required]'));
        var allValid = requiredFields.every(function (field) {
            return fieldHasValue(field) && (!field.checkValidity || field.checkValidity());
        });

        if (form.action.indexOf('/candidate/onboarding/experience') !== -1) {
            if (fresherCheckbox && fresherCheckbox.checked) {
                allValid = true;
            } else {
                var experienceItems = Array.prototype.slice.call(form.querySelectorAll('.experience-item'));
                allValid = experienceItems.length > 0 && experienceItems.every(function (item) {
                    var jobTitle = item.querySelector('input[name="job_title[]"]');
                    var companyName = item.querySelector('input[name="company_name[]"]');
                    var startDate = item.querySelector('input[name="start_date[]"]');
                    if (jobTitle.disabled) {
                        return true;
                    }
                    return [jobTitle, companyName, startDate].every(function (field) {
                        return fieldHasValue(field) && (!field.checkValidity || field.checkValidity());
                    });
                });
            }
        }

        submit.disabled = !allValid;
    }

    onboardingForms.forEach(function (form) {
        form.addEventListener('input', function () {
            syncOnboardingSubmit(form);
        });
        form.addEventListener('change', function () {
            syncOnboardingSubmit(form);
        });
        syncOnboardingSubmit(form);
    });

    if (!fresherCheckbox || !experienceFields) {
        return;
    }

    function syncExperienceFields() {
        var disabled = fresherCheckbox.checked;
        experienceFields.querySelectorAll('input, select, textarea').forEach(function (field) {
            if (field.id === 'is_current') {
                return;
            }
            field.disabled = disabled;
        });

        onboardingForms.forEach(function (form) {
            syncOnboardingSubmit(form);
        });
    }

    fresherCheckbox.addEventListener('change', syncExperienceFields);
    syncExperienceFields();

    document.addEventListener('click', function (event) {
        if (!event.target.matches('[data-remove-item]')) {
            return;
        }

        event.preventDefault();
        var item = event.target.closest('.repeatable-item');
        if (!item) {
            return;
        }
        var container = item.parentElement;
        if (container && container.children.length > 1) {
            item.remove();
            onboardingForms.forEach(function (form) {
                syncOnboardingSubmit(form);
            });
        }
    });

    if (addEducationItem && educationList) {
        addEducationItem.addEventListener('click', function () {
            var itemCount = educationList.querySelectorAll('.education-item').length + 1;
            var wrapper = document.createElement('div');
            wrapper.className = 'repeatable-item education-item';
            wrapper.innerHTML = '<div class="repeatable-item-title">Education ' + itemCount + '</div>'
                + '<button type="button" class="btn btn-sm btn-outline-danger repeatable-remove" data-remove-item>Remove</button>'
                + '<div class="row">'
                + '<div class="col-md-6 mb-3"><label>Degree</label><input type="text" name="degree[]" class="form-control" minlength="2" required></div>'
                + '<div class="col-md-6 mb-3"><label>Field of Study</label><input type="text" name="field_of_study[]" class="form-control" minlength="2" required></div>'
                + '<div class="col-md-6 mb-3"><label>Institution</label><input type="text" name="institution[]" class="form-control" minlength="2" required></div>'
                + '<div class="col-md-3 mb-3"><label>Start Year</label><input type="number" name="start_year[]" class="form-control" required></div>'
                + '<div class="col-md-3 mb-3"><label>End Year</label><input type="number" name="end_year[]" class="form-control" required></div>'
                + '<div class="col-md-6 mb-3"><label>Grade / CGPA</label><input type="text" name="grade[]" class="form-control"></div>'
                + '</div>';
            educationList.appendChild(wrapper);
            onboardingForms.forEach(function (form) {
                syncOnboardingSubmit(form);
            });
        });
    }

    if (addExperienceItem && experienceFields) {
        addExperienceItem.addEventListener('click', function () {
            var index = experienceFields.querySelectorAll('.experience-item').length;
            var itemCount = index + 1;
            var wrapper = document.createElement('div');
            wrapper.className = 'repeatable-item experience-item';
            wrapper.innerHTML = '<div class="repeatable-item-title">Experience ' + itemCount + '</div>'
                + '<button type="button" class="btn btn-sm btn-outline-danger repeatable-remove" data-remove-item>Remove</button>'
                + '<div class="row">'
                + '<div class="col-md-6 mb-3"><label>Job Title</label><input type="text" name="job_title[]" class="form-control" minlength="2"></div>'
                + '<div class="col-md-6 mb-3"><label>Company Name</label><input type="text" name="company_name[]" class="form-control" minlength="2"></div>'
                + '<div class="col-md-6 mb-3"><label>Employment Type</label><select name="employment_type[]" class="form-control"><option>Full-time</option><option>Part-time</option><option>Contract</option><option>Internship</option><option>Freelance</option></select></div>'
                + '<div class="col-md-6 mb-3"><label>Location</label><input type="text" name="location[]" class="form-control"></div>'
                + '<div class="col-md-6 mb-3"><label>Start Date</label><input type="date" name="start_date[]" class="form-control"></div>'
                + '<div class="col-md-6 mb-3"><label>End Date</label><input type="date" name="end_date[]" class="form-control"></div>'
                + '<div class="col-12 mb-3"><div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="is_current_' + index + '" name="is_current[' + index + ']" value="1"><label class="custom-control-label" for="is_current_' + index + '">I currently work here</label></div></div>'
                + '<div class="col-12 mb-3"><label>Work Summary</label><textarea name="description[]" class="form-control" rows="4"></textarea></div>'
                + '</div>';
            experienceFields.appendChild(wrapper);
            syncExperienceFields();
        });
    }
});
</script>
