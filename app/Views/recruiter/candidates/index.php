<?= view('Layouts/recruiter_header', ['title' => 'Candidate Database']) ?>

<div class="recruiter-candidates-jobboard">
<div class="container-fluid py-5">
    <?php
    $selectedJobTitle = (string) ($selectedJob['title'] ?? '');
    $candidateCount = count($candidates ?? []);
    $aiSuggestionCount = count($aiSuggestions ?? []);
    ?>

    <div class="page-board-header page-board-header-tight recruiter-page-board-header">
        <div class="page-board-copy">
            <span class="page-board-kicker"><i class="fas fa-database"></i> Recruiter candidate search</span>
            <h1 class="page-board-title">Candidate Database</h1>
            <p class="page-board-subtitle">Search and discover candidates beyond direct applicants. Compare profiles and jump into the candidate workspace.</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success recruiter-alert"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger recruiter-alert"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card shadow-sm recruiter-filter-card mb-4">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-3">
                <div>
                    <h6 class="m-0 font-weight-bold text-primary">Search Filters</h6>
                    <p class="text-muted mb-0">Narrow down the candidate database by skill, experience, job fit, and resume availability.</p>
                </div>
            </div>

            <form method="get" action="<?= base_url('recruiter/candidates') ?>" class="recruiter-candidate-filter-form">
                <div class="row">
                    <div class="col-md-3">
                        <label class="small text-muted mb-1">Keyword</label>
                        <input type="text" name="keyword" class="form-control" value="<?= esc($filters['keyword'] ?? '') ?>" placeholder="Name / Email / Skill">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">Skills</label>
                        <input type="text" name="skills" class="form-control" value="<?= esc($filters['skills'] ?? '') ?>" placeholder="e.g. PHP">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">Location</label>
                        <input type="text" name="location" class="form-control" value="<?= esc($filters['location'] ?? '') ?>" placeholder="City / State">
                    </div>
                    <div class="col-md-1">
                        <label class="small text-muted mb-1">Exp Min (Years)</label>
                        <input type="number" step="0.5" min="0" name="exp_min" class="form-control" value="<?= esc($filters['exp_min'] ?? '') ?>">
                    </div>
                    <div class="col-md-1">
                        <label class="small text-muted mb-1">Exp Max (Years)</label>
                        <input type="number" step="0.5" min="0" name="exp_max" class="form-control" value="<?= esc($filters['exp_max'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">Job Role</label>
                        <select name="job_id" class="form-control">
                            <option value="">Select Job</option>
                            <?php foreach (($recruiterJobs ?? []) as $job): ?>
                                <option value="<?= (int) $job['id'] ?>" <?= (int) ($filters['job_id'] ?? 0) === (int) $job['id'] ? 'selected' : '' ?>>
                                    <?= esc($job['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">Resume</label>
                        <select name="resume" class="form-control">
                            <option value="" <?= ($filters['resume'] ?? '') === '' ? 'selected' : '' ?>>All</option>
                            <option value="yes" <?= ($filters['resume'] ?? '') === 'yes' ? 'selected' : '' ?>>With Resume</option>
                            <option value="no" <?= ($filters['resume'] ?? '') === 'no' ? 'selected' : '' ?>>Without Resume</option>
                        </select>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="mt-3 recruiter-filter-actions">
                    <a href="<?= base_url('recruiter/candidates') ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($selectedJob)): ?>
        <div class="card shadow-sm recruiter-ai-suggestions-card mb-4">
            <div class="card-header py-3 bg-gradient-primary text-white">
                <h6 class="m-0 font-weight-bold"><i class="fas fa-robot"></i> AI Candidate Suggestions for <?= esc($selectedJob['title'] ?? 'Selected Job') ?></h6>
            </div>
            <div class="card-body">
                <?php if (empty($aiSuggestions)): ?>
                    <p class="text-muted mb-0">No suitable candidates found for this role.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover recruiter-candidates-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Candidate</th>
                                    <th>Score</th>
                                    <th>Experience</th>
                                    <th>Skills</th>
                                    <th>AI Reason</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($aiSuggestions as $candidate): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($candidate['name'] ?? '-') ?></strong><br>
                                            <small class="text-muted"><?= esc($candidate['email'] ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-success"><?= esc((string) ($candidate['match_score'] ?? 0)) ?>%</span>
                                        </td>
                                        <td><?= esc($candidate['experience_display'] ?? '-') ?></td>
                                        <td><small><?= esc($candidate['skill_name'] ?? '-') ?></small></td>
                                        <td><small><?= esc($candidate['match_reason'] ?? '-') ?></small></td>
                                        <td>
                                            <a href="<?= base_url('recruiter/candidate/' . $candidate['id']) ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-user"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php $aiModeForJob = !empty($selectedJob); ?>
    <?php if (!$aiModeForJob): ?>
        <div class="card shadow-sm recruiter-table-card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-users"></i> Candidates (<?= count($candidates ?? []) ?> on this page)</h6>
            </div>
            <div class="card-body">
                <?php if (empty($candidates)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                        <h5>No candidates found</h5>
                        <p class="text-muted mb-0">Try adjusting your filters.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover recruiter-candidates-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>Candidate</th>
                                    <th>Location</th>
                                    <th>Experience</th>
                                    <th>Skills</th>
                                    <th>Resume</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($candidates as $candidate): ?>
                                    <tr>
                                        <td>
                                            <strong><?= esc($candidate['name'] ?? '-') ?></strong><br>
                                            <small class="text-muted"><?= esc($candidate['email'] ?? '-') ?></small>
                                        </td>
                                        <td><?= esc($candidate['location'] ?? '-') ?></td>
                                        <td><?= esc($candidate['experience_display'] ?? '-') ?></td>
                                        <td>
                                            <?php if (!empty($candidate['skill_name'])): ?>
                                                <small><?= esc($candidate['skill_name']) ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($candidate['resume_path'])): ?>
                                                <span class="badge badge-success">Available</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Not Uploaded</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= !empty($candidate['created_at']) ? date('M d, Y', strtotime($candidate['created_at'])) : '-' ?></td>
                                        <td>
                                            <div class="application-actions-wrap">
                                                <a href="<?= base_url('recruiter/candidate/' . $candidate['id']) ?>" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-user"></i> View Profile
                                                </a>
                                                <a href="<?= base_url('recruiter/candidate/' . $candidate['id'] . '/view-contact') ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-address-card"></i> View Contact
                                                </a>
                                                <?php if (!empty($candidate['resume_path'])): ?>
                                                    <a href="<?= base_url('recruiter/candidate/' . $candidate['id'] . '/download-resume') ?>" class="btn btn-sm btn-success">
                                                        <i class="fas fa-download"></i> Resume
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if (isset($pager) && is_object($pager) && method_exists($pager, 'links')): ?>
                        <div class="mt-3">
                            <?= $pager->links() ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
</div>

<?= view('Layouts/recruiter_footer') ?>
