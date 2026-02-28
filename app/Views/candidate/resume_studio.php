<?= view('Layouts/candidate_header', ['title' => 'AI Resume Studio']) ?>

<div class="profile-jobboard">
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1 class="text-white font-weight-bold">AI Resume Studio</h1>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                        <span class="mx-2 slash">/</span>
                        <a href="<?= base_url('candidate/profile') ?>">My Profile</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong>AI Resume Studio</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section pt-0 content-wrap">
        <div class="container">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success mt-4"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger mt-4"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <style>
                .resume-studio-panel {
                    background: linear-gradient(135deg, #f8fffb 0%, #eef6ff 100%);
                    border: 1px solid #dbeafe;
                    border-radius: 16px;
                }
                .resume-version-card {
                    border: 1px solid #e5e7eb;
                    border-radius: 14px;
                    padding: 16px;
                    background: #fff;
                    height: 100%;
                }
                .resume-version-content {
                    max-height: 260px;
                    overflow: auto;
                    background: #f8fafc;
                    border-radius: 10px;
                    padding: 14px;
                    font-size: 0.92rem;
                }
                .resume-version-content .resume-template-shell {
                    box-shadow: none !important;
                    border-radius: 16px;
                    padding: 18px;
                    border: 1px solid #e2e8f0;
                }
                .btn-loading-state {
                    display: none;
                    align-items: center;
                    gap: 8px;
                }
                .btn-submit-text.is-hidden {
                    display: none;
                }
                .btn.is-loading {
                    pointer-events: none;
                    opacity: .85;
                }
                .template-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                    gap: 18px;
                }
                .template-option {
                    position: relative;
                }
                .template-option input {
                    position: absolute;
                    opacity: 0;
                    pointer-events: none;
                }
                .template-card {
                    display: block;
                    border: 1px solid #dbe3ee;
                    border-radius: 18px;
                    padding: 18px;
                    background: linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
                    cursor: pointer;
                    transition: .18s ease;
                    min-height: 220px;
                }
                .template-card:hover {
                    border-color: #93c5fd;
                    box-shadow: 0 14px 28px rgba(59, 130, 246, .12);
                    transform: translateY(-2px);
                }
                .template-option input:checked + .template-card {
                    border-color: #2563eb;
                    box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
                    background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
                }
                .template-preview {
                    display: block;
                    height: 118px;
                    border-radius: 14px;
                    border: 1px solid #d9e2ec;
                    margin-bottom: 14px;
                    background: linear-gradient(180deg, #eef5fb 0%, #f8fbff 100%);
                    overflow: hidden;
                    position: relative;
                    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .8);
                }
                .template-preview:before {
                    content: "";
                    position: absolute;
                    inset: 12px;
                    border-radius: 10px;
                    background: #ffffff;
                    box-shadow: 0 10px 20px rgba(15, 23, 42, .08);
                }
                .template-preview.modern:after {
                    content: "";
                    position: absolute;
                    inset: 24px 24px 24px 24px;
                    background:
                        linear-gradient(90deg, #0f172a 0 42%, transparent 42%) 0 0/100% 10px no-repeat,
                        linear-gradient(90deg, #2563eb 0 52%, transparent 52%) 0 18px/100% 6px no-repeat,
                        linear-gradient(90deg, #cbd5e1 0 100%, transparent 100%) 0 38px/100% 4px no-repeat,
                        linear-gradient(90deg, #cbd5e1 0 82%, transparent 82%) 0 50px/100% 4px no-repeat,
                        linear-gradient(90deg, #e2e8f0 0 100%, transparent 100%) 0 68px/100% 2px no-repeat;
                }
                .template-preview.sidebar {
                    background: linear-gradient(180deg, #eef5fb 0%, #f8fbff 100%);
                }
                .template-preview.sidebar:before {
                    content: "";
                    position: absolute;
                    inset: 12px;
                    border-radius: 10px;
                    background: linear-gradient(90deg, #172554 0 34%, #ffffff 34% 100%);
                    box-shadow: 0 10px 20px rgba(15, 23, 42, .08);
                }
                .template-preview.sidebar:after {
                    content: "";
                    position: absolute;
                    inset: 24px 22px 22px 42%;
                    background:
                        linear-gradient(90deg, #0f172a 0 52%, transparent 52%) 0 0/100% 9px no-repeat,
                        linear-gradient(90deg, #cbd5e1 0 100%, transparent 100%) 0 18px/100% 4px no-repeat,
                        linear-gradient(90deg, #cbd5e1 0 78%, transparent 78%) 0 30px/100% 4px no-repeat,
                        linear-gradient(90deg, #0f766e 0 38%, transparent 38%) 0 48px/100% 7px no-repeat,
                        linear-gradient(90deg, #e2e8f0 0 100%, transparent 100%) 0 66px/100% 2px no-repeat;
                }
                .template-preview.timeline:after {
                    content: "";
                    position: absolute;
                    inset: 24px 22px 22px 24px;
                    background:
                        linear-gradient(#bfdbfe, #bfdbfe) 6px 0/2px 100% no-repeat,
                        radial-gradient(circle, #0f766e 0 5px, transparent 6px) 0 0/14px 20px no-repeat,
                        radial-gradient(circle, #0f766e 0 5px, transparent 6px) 0 28px/14px 20px no-repeat,
                        radial-gradient(circle, #0f766e 0 5px, transparent 6px) 0 56px/14px 20px no-repeat,
                        linear-gradient(90deg, #0f172a 0 42%, transparent 42%) 20px 0/100% 8px no-repeat,
                        linear-gradient(90deg, #cbd5e1 0 90%, transparent 90%) 20px 16px/100% 4px no-repeat,
                        linear-gradient(90deg, #cbd5e1 0 74%, transparent 74%) 20px 28px/100% 4px no-repeat,
                        linear-gradient(90deg, #cbd5e1 0 86%, transparent 86%) 20px 54px/100% 4px no-repeat;
                }
                .template-card strong {
                    font-size: 1rem;
                    color: #0f172a;
                }
                .template-card small.text-muted {
                    line-height: 1.55;
                    font-size: .9rem;
                }
            </style>

            <div class="card shadow-sm mt-4">
                <div class="card-body resume-studio-panel p-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
                        <div>
                            <h5 class="mb-1"><i class="fas fa-magic"></i> Create Resume Versions</h5>
                            <p class="text-muted mb-0">Generate polished role-based resumes, create job-specific versions, and keep a transition-ready primary resume.</p>
                        </div>
                        <?php if (!empty($activeTransition)): ?>
                            <form method="post" action="<?= base_url('candidate/resume/sync-transition') ?>" data-loading-form>
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline-success btn-sm" data-loading-button>
                                    <span class="btn-submit-text"><i class="fas fa-arrows-rotate"></i> One-click update from transition</span>
                                    <span class="btn-loading-state" aria-hidden="true"><i class="fas fa-spinner fa-spin"></i> Updating...</span>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($activeTransition)): ?>
                        <div class="alert alert-info">
                            <strong>Active transition:</strong>
                            <?= esc($activeTransition['current_role'] ?? 'Current role') ?>
                            <i class="fas fa-arrow-right mx-1"></i>
                            <?= esc($activeTransition['target_role'] ?? 'Target role') ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('candidate/resume/generate') ?>" data-loading-form>
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Target Role</label>
                                <input type="text" name="target_role" class="form-control" placeholder="e.g. Product Designer, PHP Developer">
                                <small class="text-muted">Required unless you pick a job below.</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Specific Job Version</label>
                                <select name="job_id" class="form-control">
                                    <option value="">No specific job</option>
                                    <?php foreach (($resumeTargets ?? []) as $target): ?>
                                        <option value="<?= (int) $target['job_id'] ?>"><?= esc($target['title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label d-block">Choose Template</label>
                                <div class="template-grid">
                                    <?php foreach (($resumeTemplates ?? []) as $templateKey => $template): ?>
                                        <?php
                                        $previewClass = 'modern';
                                        if ($templateKey === 'executive_sidebar') {
                                            $previewClass = 'sidebar';
                                        } elseif ($templateKey === 'minimal_timeline') {
                                            $previewClass = 'timeline';
                                        }
                                        ?>
                                        <label class="template-option">
                                            <input type="radio" name="template_key" value="<?= esc($templateKey) ?>" <?= $templateKey === 'modern_professional' ? 'checked' : '' ?>>
                                            <span class="template-card">
                                                <span class="template-preview <?= esc($previewClass) ?>"></span>
                                                <strong class="d-block mb-1"><?= esc($template['label']) ?></strong>
                                                <small class="text-muted d-block"><?= esc($template['description']) ?></small>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3 d-flex align-items-end">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" value="1" id="makePrimaryResumeVersion" name="make_primary">
                                    <label class="form-check-label" for="makePrimaryResumeVersion">Set primary</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-dark" data-loading-button>
                            <span class="btn-submit-text"><i class="fas fa-magic"></i> Generate AI Resume</span>
                            <span class="btn-loading-state" aria-hidden="true"><i class="fas fa-spinner fa-spin"></i> Generating...</span>
                        </button>
                    </form>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 mb-3 flex-wrap">
                <div>
                    <h5 class="mb-1">Saved Resume Versions</h5>
                    <p class="text-muted mb-0">We keep one saved version per role and one per job-specific application target to avoid duplicate clutter.</p>
                </div>
                <a href="<?= base_url('candidate/profile') ?>" class="btn btn-outline-secondary btn-sm">Back to Profile</a>
            </div>

            <?php if (!empty($resumeVersions)): ?>
                <div class="row">
                    <?php foreach ($resumeVersions as $version): ?>
                        <div class="col-lg-6 mb-4">
                            <div class="resume-version-card">
                                <div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            <?= esc($version['title'] ?? 'Resume Version') ?>
                                            <?php if ((int) ($version['is_primary'] ?? 0) === 1): ?>
                                                <span class="badge badge-success ml-2">Primary</span>
                                            <?php endif; ?>
                                        </h6>
                                        <div class="text-muted small">
                                            Target role: <?= esc($version['target_role'] ?? '-') ?>
                                            <?php if (!empty($version['job_title'])): ?>
                                                | Job: <?= esc($version['job_title']) ?>
                                            <?php endif; ?>
                                            <?php if (!empty($version['template_label'])): ?>
                                                | Template: <?= esc($version['template_label']) ?>
                                            <?php endif; ?>
                                            | Source: <?= esc(ucwords(str_replace('_', ' ', (string) ($version['generation_source'] ?? 'role_based')))) ?>
                                        </div>
                                    </div>
                                    <div class="d-flex" style="gap:8px;">
                                        <a href="<?= base_url('candidate/resume-version/' . (int) $version['id'] . '/download') ?>" class="btn btn-outline-secondary btn-sm">Download PDF</a>
                                        <form method="post" action="<?= base_url('candidate/resume-version/' . (int) $version['id'] . '/delete') ?>" onsubmit="return confirm('Delete this saved resume version?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                                        </form>
                                        <?php if ((int) ($version['is_primary'] ?? 0) !== 1): ?>
                                            <form method="post" action="<?= base_url('candidate/resume-version/' . (int) $version['id'] . '/primary') ?>">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-outline-primary btn-sm">Set as primary</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if (!empty($version['summary'])): ?>
                                    <p class="mb-2"><?= esc($version['summary']) ?></p>
                                <?php endif; ?>

                                <div class="resume-version-content"><?= $version['rendered_preview'] ?? '' ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                        <h5>No AI resume versions yet</h5>
                        <p class="text-muted mb-0">Generate your first role-based or job-specific resume version above.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var loadingForms = document.querySelectorAll('form[data-loading-form]');

    loadingForms.forEach(function (form) {
        form.addEventListener('submit', function () {
            var button = form.querySelector('[data-loading-button]');
            if (!button || button.disabled) {
                return;
            }

            button.disabled = true;
            button.classList.add('is-loading');

            var submitText = button.querySelector('.btn-submit-text');
            var loadingState = button.querySelector('.btn-loading-state');

            if (submitText) {
                submitText.classList.add('is-hidden');
            }

            if (loadingState) {
                loadingState.style.display = 'inline-flex';
            }
        });
    });
});
</script>
