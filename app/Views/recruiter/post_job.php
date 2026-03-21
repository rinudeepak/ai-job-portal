<?= view('Layouts/recruiter_header', ['title' => 'Post Job']) ?>

<div class="recruiter-post-jobboard">
<div class="container-fluid py-5">
    <div class="page-board-header page-board-header-tight recruiter-page-board-header">
        <div class="page-board-copy">
            <span class="page-board-kicker"><i class="fas fa-plus-circle"></i> Recruiter posting</span>
            <h1 class="page-board-title">Post a Job</h1>
            <p class="page-board-subtitle">Create a clear role listing and control how AI interview screening applies to applicants.</p>
        </div>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show recruiter-alert" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show recruiter-alert" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <div class="recruiter-form-layout">
        <div class="recruiter-form-main">
            <div class="card shadow-sm recruiter-form-card">
                <div class="card-body">
                    <form class="form-contact contact_form recruiter-job-form" method="post" action="<?= base_url('recruiter/post_job') ?>" id="jobForm">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Job Title *</label>
                                    <input class="form-control" name="title" id="title" type="text" value="<?= old('title') ?>" placeholder="Job Title" required>
                                    <small class="text-danger" id="title-error"></small>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Category *</label>
                                    <input class="form-control" name="category" id="category" type="text" value="<?= old('category') ?>" placeholder="Job Category (e.g., Software Development)" required>
                                    <small class="text-danger" id="category-error"></small>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label>Location *</label>
                                    <input class="form-control" name="location" id="location" type="text" value="<?= old('location') ?>" placeholder="Location" required>
                                    <small class="text-danger" id="location-error"></small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Description *</label>
                                    <textarea class="form-control w-100" name="description" id="description" cols="30" rows="9" placeholder="Job Description" required><?= old('description') ?></textarea>
                                    <small class="text-danger" id="description-error"></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Experience</label>
                                    <input class="form-control" name="experience_level" id="experience_level" type="text" value="<?= old('experience_level') ?>" placeholder="Experience (e.g., 2-3 years)">
                                    <small class="text-danger" id="experience_level-error"></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Employment Type</label>
                                    <select class="form-control" name="employment_type" id="employment_type">
                                        <option value="Full-time" <?= old('employment_type') === 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                                        <option value="Part-time" <?= old('employment_type') === 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                                        <option value="Contract" <?= old('employment_type') === 'Contract' ? 'selected' : '' ?>>Contract</option>
                                        <option value="Internship" <?= old('employment_type') === 'Internship' ? 'selected' : '' ?>>Internship</option>
                                    </select>
                                    <small class="text-danger" id="employment_type-error"></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Salary Range</label>
                                    <input class="form-control" name="salary_range" id="salary_range" type="text" value="<?= old('salary_range') ?>" placeholder="Salary Range (e.g., 5-8 LPA)">
                                    <small class="text-danger" id="salary_range-error"></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Application Deadline</label>
                                    <input class="form-control" name="application_deadline" id="application_deadline" type="date" value="<?= old('application_deadline') ?>" title="Application Deadline">
                                    <small class="text-muted">Application deadline (optional)</small>
                                    <small class="text-danger" id="application_deadline-error"></small>
                                </div>
                            </div>
                            <?php $selectedPolicy = old('ai_interview_policy', 'REQUIRED_HARD'); ?>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>AI Interview Policy</label>
                                    <select class="form-control" name="ai_interview_policy" id="ai_interview_policy">
                                        <option value="REQUIRED_HARD" <?= $selectedPolicy === 'REQUIRED_HARD' ? 'selected' : '' ?>>AI Interview: Mandatory (Strict)</option>
                                        <option value="REQUIRED_SOFT" <?= $selectedPolicy === 'REQUIRED_SOFT' ? 'selected' : '' ?>>AI Interview: Mandatory (Recruiter Can Override)</option>
                                        <option value="OPTIONAL" <?= $selectedPolicy === 'OPTIONAL' ? 'selected' : '' ?>>AI Interview: Optional</option>
                                        <option value="OFF" <?= $selectedPolicy === 'OFF' ? 'selected' : '' ?>>AI Interview: Not Required</option>
                                    </select>
                                    <small class="text-muted d-block mt-2">
                                        Choose how AI interview affects applications: strict reject, recruiter override, optional, or disabled.
                                    </small>
                                </div>
                            </div>
                            <div class="col-sm-6" id="minAiCutoffWrap">
                                <div class="form-group">
                                    <label>Minimum AI Cutoff Score</label>
                                    <input class="form-control" name="min_ai_cutoff_score" id="min_ai_cutoff_score" type="number" min="0" max="100" value="<?= old('min_ai_cutoff_score') ?>" placeholder="Minimum AI Cutoff Score">
                                    <small class="text-danger" id="min_ai_cutoff_score-error"></small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Number of Openings *</label>
                                    <input class="form-control" name="openings" id="openings" type="number" min="1" value="<?= old('openings', '1') ?>" placeholder="Number of Openings" required>
                                    <small class="text-danger" id="openings-error"></small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label>Required Skills</label>
                                    <input class="form-control" name="required_skills" id="required_skills" type="text" value="<?= old('required_skills') ?>" placeholder="Required Skills">
                                    <small class="text-danger" id="required_skills-error"></small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <button type="submit" class="button button-contactForm boxed-btn">Post Job</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="recruiter-form-side">
            <div class="card shadow-sm recruiter-form-card">
                <div class="card-body">
                    <h6 class="mb-3"><i class="fas fa-lightbulb"></i> Posting tips</h6>
                    <div class="recruiter-tip-list">
                        <div class="recruiter-tip-item">Use a precise title so matching works better.</div>
                        <div class="recruiter-tip-item">Add the core skills candidates should have on day one.</div>
                        <div class="recruiter-tip-item">Set the AI policy to match your screening process.</div>
                        <div class="recruiter-tip-item">Keep salary and deadline fields updated for trust.</div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm recruiter-form-card">
                <div class="card-body">
                    <h6 class="mb-3"><i class="fas fa-shield-alt"></i> AI interview policy</h6>
                    <p class="text-muted mb-0">Strict and soft modes keep screening automated. Optional lets recruiters review more manually. OFF disables AI screening for the role.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= view('Layouts/recruiter_footer') ?>
