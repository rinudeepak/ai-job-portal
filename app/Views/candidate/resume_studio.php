<?= view('Layouts/candidate_header', ['title' => 'AI Resume Studio']) ?>
<?php
$prefillGenerationMode = (string) (service('request')->getGet('generation_mode') ?? 'role');
if (!in_array($prefillGenerationMode, ['role', 'job'], true)) {
    $prefillGenerationMode = 'role';
}
$prefillJobId = (int) (service('request')->getGet('job_id') ?? 0);
$activeTransition = $activeTransition ?? null;
$resumeVersions = $resumeVersions ?? [];
$resumeTemplates = $resumeTemplates ?? [];
$blockedResumeTemplates = $blockedResumeTemplates ?? [];
$resumeTargets = $resumeTargets ?? [];
?>

<div class="job-details-jobboard resume-studio-jobboard">
    <div class="container">
        <div class="job-details-page-header">
            <div class="page-board-copy">
                <span class="page-board-kicker"><i class="fas fa-wand-magic-sparkles"></i> AI-powered resume tools</span>
                <h1 class="page-board-title">AI Resume Studio</h1>
                <p class="page-board-subtitle">Generate polished role-based resumes, create job-specific versions, and keep a transition-ready primary resume.</p>
                <div class="job-details-header-meta">
                    <span class="meta-chip"><i class="fas fa-file-lines"></i> Version Builder</span>
                    <span class="meta-chip"><i class="fas fa-bolt"></i> One-click sync</span>
                    <span class="meta-chip"><i class="fas fa-layer-group"></i> Multiple templates</span>
                </div>
            </div>
            <div class="job-details-header-actions">
                <a href="<?= base_url('candidate/profile') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-user mr-1"></i> My Profile
                </a>
                <?php if (!empty($activeTransition)): ?>
                    <form method="post" action="<?= base_url('candidate/resume/sync-transition') ?>" data-loading-form>
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline-primary" data-loading-button>
                            <span class="btn-submit-text"><i class="fas fa-arrows-rotate"></i> Sync Transition</span>
                            <span class="btn-loading-state" aria-hidden="true"><i class="fas fa-spinner fa-spin"></i> Updating...</span>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <section class="site-section pt-0 content-wrap">
        <div class="container">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div class="resume-studio-layout">
                <div class="resume-studio-main">
                    <?php if (!empty($activeTransition)): ?>
                        <div class="detail-card resume-transition-card">
                            <div class="detail-card-title">
                                <span class="detail-card-icon"><i class="fas fa-exchange-alt"></i></span>
                                <span>Active Transition</span>
                            </div>
                            <div class="resume-transition-flow">
                                <div class="resume-transition-pill"><?= esc($activeTransition['current_role'] ?? 'Current role') ?></div>
                                <i class="fas fa-arrow-right"></i>
                                <div class="resume-transition-pill is-target"><?= esc($activeTransition['target_role'] ?? 'Target role') ?></div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="detail-card resume-generator-card">
                        <div class="detail-card-title">
                            <span class="detail-card-icon"><i class="fas fa-magic"></i></span>
                            <span>Create Resume Versions</span>
                        </div>
                        <p class="job-details-section-text">Generate role-based resumes, tailor versions to specific jobs, and mark a primary version for quick reuse.</p>

                        <form method="post" action="<?= base_url('candidate/resume/generate') ?>" data-loading-form>
                            <?= csrf_field() ?>
                            <div class="generation-mode-grid">
                                <label class="generation-mode-option">
                                    <input type="radio" name="generation_mode" value="role" <?= $prefillGenerationMode === 'role' ? 'checked' : '' ?>>
                                    <span class="generation-mode-card">
                                        <strong>Generate By Role</strong>
                                        <small>Create a resume for a target role you enter manually.</small>
                                    </span>
                                </label>
                                <label class="generation-mode-option">
                                    <input type="radio" name="generation_mode" value="job" <?= $prefillGenerationMode === 'job' ? 'checked' : '' ?>>
                                    <span class="generation-mode-card">
                                        <strong>Generate For Specific Job</strong>
                                        <small>Create a version tailored to one selected job posting.</small>
                                    </span>
                                </label>
                            </div>

                            <div class="generation-role-field">
                                <label class="form-label">Target Role</label>
                                <input type="text" name="target_role" class="form-control" placeholder="e.g. Product Designer, PHP Developer">
                                <small class="text-muted">Used only for role-based generation.</small>
                            </div>

                            <div class="generation-panel <?= $prefillGenerationMode === 'job' ? 'is-active' : '' ?>" data-generation-panel="job">
                                <label class="form-label">Specific Job Version</label>
                                <select name="job_id" class="form-control resume-studio-select">
                                    <option value="">Select a job</option>
                                    <?php foreach (($resumeTargets ?? []) as $target): ?>
                                        <option value="<?= (int) $target['job_id'] ?>" <?= $prefillJobId === (int) $target['job_id'] ? 'selected' : '' ?>><?= esc($target['title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Used only for job-specific generation.</small>
                            </div>

                            <div class="generation-panel is-active">
                                <label class="form-label d-block">Choose Template</label>
                                <div class="template-grid">
                                    <?php foreach (($resumeTemplates ?? []) as $templateKey => $template): ?>
                                        <?php $disabledMessage = (string) ($blockedResumeTemplates[$templateKey] ?? ''); ?>
                                        <?php $isTemplateDisabled = $disabledMessage !== ''; ?>
                                        <?php $previewClass = (string) ($template['preview_class'] ?? 'modern'); ?>
                                        <label class="template-option <?= $isTemplateDisabled ? 'is-disabled' : '' ?>">
                                            <input type="radio" name="template_key" value="<?= esc($templateKey) ?>" <?= $templateKey === 'modern_professional' ? 'checked' : '' ?> <?= $isTemplateDisabled ? 'disabled' : '' ?>>
                                            <span class="template-card">
                                                <span class="template-preview <?= esc($previewClass) ?>"></span>
                                                <strong class="d-block mb-1"><?= esc($template['label']) ?></strong>
                                                <small class="text-muted d-block"><?= esc($template['description']) ?></small>
                                                <?php if ($isTemplateDisabled): ?>
                                                    <small class="template-disabled-note"><i class="fas fa-info-circle"></i> <?= esc($disabledMessage) ?></small>
                                                <?php endif; ?>
                                            </span>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="resume-studio-primary-row">
                                <div class="form-check mb-0">
                                    <input class="form-check-input" type="checkbox" value="1" id="makePrimaryResumeVersion" name="make_primary">
                                    <label class="form-check-label" for="makePrimaryResumeVersion">Set primary</label>
                                </div>
                                <button type="submit" class="btn btn-dark" data-loading-button>
                                    <span class="btn-submit-text"><i class="fas fa-magic"></i> Generate AI Resume</span>
                                    <span class="btn-loading-state" aria-hidden="true"><i class="fas fa-spinner fa-spin"></i> Generating...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <aside class="resume-studio-side">
                    <div class="summary-card">
                        <div class="detail-card-title mb-3">
                            <span class="detail-card-icon"><i class="fas fa-lightbulb"></i></span>
                            <span>How It Works</span>
                        </div>
                        <ul class="resume-studio-tip-list">
                            <li>Choose a generation mode to tailor the resume for a role or a specific job.</li>
                            <li>Select a template that matches the tone of the target role.</li>
                            <li>Mark a version as primary when you want it to be your default resume.</li>
                        </ul>
                    </div>

                    <div class="summary-card">
                        <div class="detail-card-title mb-3">
                            <span class="detail-card-icon"><i class="fas fa-folder-open"></i></span>
                            <span>Saved Versions</span>
                        </div>
                        <p class="job-details-section-text mb-0">We keep one saved version per role and one per job-specific application target to avoid duplicate clutter.</p>
                    </div>

                    <a href="#resume-versions" class="btn btn-outline-secondary btn-block">
                        <i class="fas fa-clipboard-list mr-1"></i> View Saved Versions
                    </a>
                </aside>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4 mb-3 flex-wrap" id="resume-versions">
                <div>
                    <h5 class="mb-1">Saved Resume Versions</h5>
                    <p class="text-muted mb-0">We keep one saved version per role and one per job-specific application target to avoid duplicate clutter.</p>
                </div>
                <a href="<?= base_url('candidate/profile') ?>" class="btn btn-outline-secondary btn-sm">Back to Profile</a>
            </div>

            <?php if (!empty($resumeVersions)): ?>
                <div class="resume-versions-grid">
                    <?php foreach ($resumeVersions as $version): ?>
                        <article class="detail-card resume-version-card">
                            <div class="d-flex justify-content-between align-items-start flex-wrap mb-2 gap-2">
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
                                <div class="resume-version-actions">
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
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-file-alt"></i>
                    <h5>No AI resume versions yet</h5>
                    <p>Generate your first role-based or job-specific resume version above.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
