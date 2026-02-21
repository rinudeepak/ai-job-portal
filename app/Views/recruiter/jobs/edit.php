<?= view('Layouts/recruiter_header', ['title' => 'Edit Job']) ?>

<div class="container-fluid py-5">
    <div class="mb-4">
        <a href="<?= base_url('recruiter/jobs') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Jobs
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">Edit Job</h5>
        </div>
        <div class="card-body">
            <form action="<?= base_url('recruiter/jobs/update/' . $job['id']) ?>" method="post">
                <div class="form-group">
                    <label>Job Title *</label>
                    <input type="text" name="title" class="form-control" value="<?= esc($job['title']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Description *</label>
                    <textarea name="description" class="form-control" rows="5" required><?= esc($job['description']) ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Location *</label>
                            <input type="text" name="location" class="form-control" value="<?= esc($job['location']) ?>" required>
                        </div>
                    </div>
                    
                </div>

                <div class="form-group">
                    <label>Required Skills *</label>
                    <input type="text" name="required_skills" class="form-control" value="<?= esc($job['required_skills']) ?>" required>
                    <small class="text-muted">Comma separated (e.g., PHP, MySQL, JavaScript)</small>
                </div>

                <div class="form-group">
                    <label>Number of Openings *</label>
                    <input type="number" name="openings" class="form-control" value="<?= esc($job['openings']) ?>" required min="1">
                </div>

                <div class="form-group">
                    <?php $policy = strtoupper($job['ai_interview_policy'] ?? 'REQUIRED_HARD'); ?>
                    <label>AI Interview Policy *</label>
                    <select name="ai_interview_policy" class="form-control">
                        <option value="REQUIRED_HARD" <?= $policy === 'REQUIRED_HARD' ? 'selected' : '' ?>>Required Hard (strict)</option>
                        <option value="REQUIRED_SOFT" <?= $policy === 'REQUIRED_SOFT' ? 'selected' : '' ?>>Required Soft (recruiter override)</option>
                        <option value="OPTIONAL" <?= $policy === 'OPTIONAL' ? 'selected' : '' ?>>Optional</option>
                        <option value="OFF" <?= $policy === 'OFF' ? 'selected' : '' ?>>Off</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Job
                </button>
            </form>
        </div>
    </div>
</div>

<?= view('Layouts/recruiter_footer') ?>
