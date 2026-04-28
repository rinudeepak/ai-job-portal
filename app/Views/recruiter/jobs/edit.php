<?= view('Layouts/recruiter_header', ['title' => 'Edit Job']) ?>
<?php
$existingQuestionnaire = [];
$questionnaireRows = old('questionnaire');
if (!is_array($questionnaireRows)) {
    $decodedQuestionnaire = json_decode((string) ($job['application_questionnaire'] ?? ''), true);
    if (is_array($decodedQuestionnaire)) {
        $existingQuestionnaire = array_values(array_filter($decodedQuestionnaire, static fn ($row) => is_array($row)));
    }
    $questionnaireRows = $existingQuestionnaire;
}
$jobCategoryOptions = [
    'Software Development',
    'Data Science',
    'DevOps',
    'Quality Assurance',
    'UI/UX Design',
    'Product Management',
    'Project Management',
    'Marketing',
    'Sales',
    'Human Resources',
    'Finance',
    'Operations',
    'Customer Support',
    'Business Analysis',
    'Cybersecurity',
];
$selectedCategory = (string) old('category', $job['category'] ?? '');
$hasCustomCategory = $selectedCategory !== '' && !in_array($selectedCategory, $jobCategoryOptions, true);
?>

<div class="recruiter-edit-jobboard">
<div class="container-fluid py-5">
    <div class="page-board-header page-board-header-tight recruiter-page-board-header">
        <div class="page-board-copy">
            <span class="page-board-kicker"><i class="fas fa-edit"></i> Recruiter job editor</span>
            <h1 class="page-board-title">Edit Job</h1>
            <p class="page-board-subtitle">Update the role description, screening policy, and hiring details without changing the workflow.</p>
        </div>
        <div class="page-board-actions">
            <a href="<?= base_url('recruiter/jobs') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Jobs
            </a>
        </div>
    </div>

    <div class="recruiter-form-layout recruiter-edit-layout">
        <div class="recruiter-form-main">
            <div class="card shadow-sm recruiter-form-card">
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= esc(session()->getFlashdata('error')) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('recruiter/jobs/update/' . $job['id']) ?>" method="post" id="editJobForm">
                        <?= csrf_field() ?>
                        <div class="form-group">
                            <label>Job Title *</label>
                            <input type="text" name="title" class="form-control" value="<?= esc(old('title', $job['title'])) ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Category *</label>
                            <select name="category" class="form-control" required>
                                <option value="">Select Job Category</option>
                                <?php if ($hasCustomCategory): ?>
                                    <option value="<?= esc($selectedCategory) ?>" selected>
                                        <?= esc($selectedCategory) ?> (Current)
                                    </option>
                                <?php endif; ?>
                                <?php foreach ($jobCategoryOptions as $categoryOption): ?>
                                    <option value="<?= esc($categoryOption) ?>" <?= $selectedCategory === $categoryOption ? 'selected' : '' ?>>
                                        <?= esc($categoryOption) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Description *</label>
                            <textarea name="description" class="form-control" rows="5" required><?= esc(old('description', $job['description'])) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Location *</label>
                                    <input type="text" name="location" class="form-control" value="<?= esc(old('location', $job['location'])) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Experience Level</label>
                                    <input type="text" name="experience_level" class="form-control" value="<?= esc(old('experience_level', $job['experience_level'] ?? '')) ?>" placeholder="e.g., 2-3 years">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Employment Type</label>
                                    <?php $employmentType = old('employment_type', $job['employment_type'] ?? 'Full-time'); ?>
                                    <select name="employment_type" class="form-control">
                                        <option value="Full-time" <?= $employmentType === 'Full-time' ? 'selected' : '' ?>>Full-time</option>
                                        <option value="Part-time" <?= $employmentType === 'Part-time' ? 'selected' : '' ?>>Part-time</option>
                                        <option value="Contract" <?= $employmentType === 'Contract' ? 'selected' : '' ?>>Contract</option>
                                        <option value="Internship" <?= $employmentType === 'Internship' ? 'selected' : '' ?>>Internship</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Salary Range</label>
                                    <input type="text" name="salary_range" class="form-control" value="<?= esc(old('salary_range', $job['salary_range'] ?? '')) ?>" placeholder="e.g., 5-8 LPA">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Application Deadline</label>
                            <input type="date" name="application_deadline" class="form-control" value="<?= esc(old('application_deadline', $job['application_deadline'] ?? '')) ?>">
                        </div>

                        <div class="form-group">
                            <label>Required Skills *</label>
                            <input type="text" name="required_skills" class="form-control" value="<?= esc(old('required_skills', $job['required_skills'])) ?>" required>
                            <small class="text-muted">Comma separated (e.g., PHP, MySQL, JavaScript)</small>
                        </div>

                        <div class="form-group">
                            <label>Number of Openings *</label>
                            <input type="number" name="openings" class="form-control" value="<?= esc(old('openings', $job['openings'])) ?>" required min="1">
                        </div>

                        <div class="form-group">
                            <div class="d-flex flex-wrap align-items-center justify-content-between mb-2" style="gap: 10px;">
                                <div>
                                    <label class="mb-0">Application Questionnaire</label>
                                    <small class="text-muted d-block">Recruiters can collect a cover letter, motivation note, availability, or other written screening answers.</small>
                                </div>
                                <div class="d-flex flex-wrap" style="gap: 8px;">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="addCoverLetterQuestion">
                                        <i class="fas fa-file-alt mr-1"></i> Add Cover Letter Prompt
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="addQuestionnaireRow">
                                        <i class="fas fa-plus mr-1"></i> Add Question
                                    </button>
                                </div>
                            </div>
                            <div id="questionnaireBuilder"
                                 data-next-index="<?= count($questionnaireRows) ?>"
                                 data-initial-items="<?= esc(json_encode(array_values($questionnaireRows)), 'attr') ?>"></div>
                        </div>

                        <div class="form-group">
                            <?php $policy = strtoupper(old('ai_interview_policy', $job['ai_interview_policy'] ?? 'REQUIRED_HARD')); ?>
                            <label>AI Interview Policy *</label>
                            <select name="ai_interview_policy" id="ai_interview_policy" class="form-control">
                                <option value="REQUIRED_HARD" <?= $policy === 'REQUIRED_HARD' ? 'selected' : '' ?>>Required Hard (strict)</option>
                                <option value="REQUIRED_SOFT" <?= $policy === 'REQUIRED_SOFT' ? 'selected' : '' ?>>Required Soft (recruiter override)</option>
                                <option value="OPTIONAL" <?= $policy === 'OPTIONAL' ? 'selected' : '' ?>>Optional</option>
                                <option value="OFF" <?= $policy === 'OFF' ? 'selected' : '' ?>>Off</option>
                            </select>
                        </div>

                        <div class="form-group" id="minAiCutoffWrap">
                            <label>Minimum AI Cutoff Score</label>
                            <input type="number" name="min_ai_cutoff_score" id="min_ai_cutoff_score" class="form-control" min="0" max="100" value="<?= esc(old('min_ai_cutoff_score', $job['min_ai_cutoff_score'] ?? '')) ?>" placeholder="0 to 100">
                            <small class="text-muted">Required if AI interview policy is not OFF.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Job
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="recruiter-form-side">
            <div class="card shadow-sm recruiter-form-card">
                <div class="card-body">
                    <h6 class="mb-3"><i class="fas fa-sliders-h"></i> Quick notes</h6>
                    <div class="recruiter-tip-list">
                        <div class="recruiter-tip-item">Keep the title and category aligned for search results.</div>
                        <div class="recruiter-tip-item">Use the policy selector to control AI screening behavior.</div>
                        <div class="recruiter-tip-item">Update the deadline and openings before reopening a role.</div>
                        <div class="recruiter-tip-item">Refine required skills to improve matching quality.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    const builder = document.getElementById('questionnaireBuilder');
    const addButton = document.getElementById('addQuestionnaireRow');
    const addCoverLetterButton = document.getElementById('addCoverLetterQuestion');

    if (!builder || !addButton || !addCoverLetterButton) {
        return;
    }

    let nextIndex = parseInt(builder.dataset.nextIndex || '0', 10) || 0;
    let initialItems = [];

    try {
        initialItems = JSON.parse(builder.dataset.initialItems || '[]');
    } catch (error) {
        initialItems = [];
    }

    function createRow(data) {
        const index = nextIndex++;
        const row = document.createElement('div');
        row.className = 'border rounded p-3 mb-3 questionnaire-row';
        row.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    <label class="small text-muted">Question Prompt</label>
                    <input type="text" class="form-control" name="questionnaire[${index}][label]" maxlength="150" placeholder="e.g. Why are you a fit for this role?" value="${escapeHtml(data.label || '')}">
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">Field Type</label>
                    <select class="form-control" name="questionnaire[${index}][type]">
                        <option value="textarea"${data.type === 'textarea' ? ' selected' : ''}>Long answer</option>
                        <option value="text"${data.type === 'text' ? ' selected' : ''}>Short answer</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small text-muted">Placeholder</label>
                    <input type="text" class="form-control" name="questionnaire[${index}][placeholder]" maxlength="200" placeholder="Optional helper text" value="${escapeHtml(data.placeholder || '')}">
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-outline-danger btn-block js-remove-question">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="col-12 mt-2">
                    <div class="custom-control custom-checkbox">
                        <input type="hidden" name="questionnaire[${index}][required]" value="0">
                        <input type="checkbox" class="custom-control-input" id="questionnaire_required_${index}" name="questionnaire[${index}][required]" value="1"${data.required ? ' checked' : ''}>
                        <label class="custom-control-label" for="questionnaire_required_${index}">Required question</label>
                    </div>
                </div>
            </div>
        `;
        builder.appendChild(row);
    }

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    addButton.addEventListener('click', function () {
        createRow({ type: 'textarea', required: false });
    });

    addCoverLetterButton.addEventListener('click', function () {
        createRow({
            label: 'Cover letter / Why are you a fit?',
            type: 'textarea',
            placeholder: 'Share why you are interested in this role and what makes you a strong fit.',
            required: true
        });
    });

    builder.addEventListener('click', function (event) {
        const button = event.target.closest('.js-remove-question');
        if (!button) {
            return;
        }

        const row = button.closest('.questionnaire-row');
        if (row) {
            row.remove();
        }
    });

    if (initialItems.length > 0) {
        initialItems.forEach(function (item) {
            createRow(item || {});
        });
    }
})();
</script>
<?= view('Layouts/recruiter_footer') ?>
