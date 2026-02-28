<?= view('Layouts/recruiter_header', ['title' => 'Applications - ' . $job['title']]) ?>

<div class="container-fluid py-5">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="mb-4">
        <a href="<?= base_url('recruiter/jobs') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Jobs
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary"><?= esc($job['title']) ?></h5>
            <small class="text-muted">
                <i class="fas fa-map-marker-alt"></i> <?= esc($job['location']) ?> | 
                <i class="fas fa-calendar"></i> Posted on <?= date('M d, Y', strtotime($job['created_at'])) ?>
            </small>
            <?php $policy = strtoupper($job['ai_interview_policy'] ?? 'REQUIRED_HARD'); ?>
            <?php
            $policyMap = [
                'OFF' => ['label' => 'Not Required', 'hint' => 'Candidates can apply directly', 'class' => 'ai-policy-chip-off'],
                'OPTIONAL' => ['label' => 'Optional', 'hint' => 'AI can improve candidate ranking', 'class' => 'ai-policy-chip-optional'],
                'REQUIRED_SOFT' => ['label' => 'Required + Recruiter Review', 'hint' => 'Recruiter can still decide after AI', 'class' => 'ai-policy-chip-soft'],
                'REQUIRED_HARD' => ['label' => 'Mandatory Screening', 'hint' => 'AI result is strict gate', 'class' => 'ai-policy-chip-hard'],
            ];
            $policyMeta = $policyMap[$policy] ?? $policyMap['REQUIRED_HARD'];
            ?>
            <div class="mt-2">
                <div class="ai-policy-chip <?= esc($policyMeta['class']) ?>">
                    <strong>AI Interview: <?= esc($policyMeta['label']) ?></strong>
                    <small><?= esc($policyMeta['hint']) ?></small>
                </div>
                <?php if (!empty($isAiCompulsory)): ?>
                    <small class="text-muted ml-2">Recruiter decision enabled only after AI interview completion.</small>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users"></i> Applications (<?= count($applications) ?>)
            </h6>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('recruiter/jobs/' . $job['id'] . '/applications') ?>" class="mb-4">
                <div class="row g-2">
                    <div class="col-md-3">
                        <label class="small text-muted mb-1">Skills</label>
                        <input type="text" name="skills" class="form-control" value="<?= esc($filters['skills'] ?? '') ?>" placeholder="e.g. PHP, Laravel">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">Experience</label>
                        <input type="text" name="experience" class="form-control" value="<?= esc($filters['experience'] ?? '') ?>" placeholder="e.g. 3 years">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">Location</label>
                        <input type="text" name="location" class="form-control" value="<?= esc($filters['location'] ?? '') ?>" placeholder="City / State">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">Score Min</label>
                        <input type="number" step="0.1" min="0" max="10" name="score_min" class="form-control" value="<?= esc($filters['score_min'] ?? '') ?>" placeholder="0">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">Score Max</label>
                        <input type="number" step="0.1" min="0" max="10" name="score_max" class="form-control" value="<?= esc($filters['score_max'] ?? '') ?>" placeholder="10">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">ATS Min</label>
                        <input type="number" min="0" max="100" name="ats_min" class="form-control" value="<?= esc($filters['ats_min'] ?? '') ?>" placeholder="0">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">ATS Max</label>
                        <input type="number" min="0" max="100" name="ats_max" class="form-control" value="<?= esc($filters['ats_max'] ?? '') ?>" placeholder="100">
                    </div>
                    <div class="col-md-2">
                        <label class="small text-muted mb-1">Sort By</label>
                        <select name="sort" class="form-control">
                            <option value="applied_desc" <?= ($filters['sort'] ?? '') === 'applied_desc' ? 'selected' : '' ?>>Newest Applied</option>
                            <option value="ats_desc" <?= ($filters['sort'] ?? '') === 'ats_desc' ? 'selected' : '' ?>>ATS High to Low</option>
                            <option value="ats_asc" <?= ($filters['sort'] ?? '') === 'ats_asc' ? 'selected' : '' ?>>ATS Low to High</option>
                            <option value="ai_desc" <?= ($filters['sort'] ?? '') === 'ai_desc' ? 'selected' : '' ?>>AI Rating High to Low</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="small text-muted mb-1">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All</option>
                            <?php foreach (($statusOptions ?? []) as $status): ?>
                                <option value="<?= esc($status) ?>" <?= ($filters['status'] ?? '') === $status ? 'selected' : '' ?>>
                                    <?= esc(ucwords(str_replace('_', ' ', $status))) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="<?= base_url('recruiter/jobs/' . $job['id'] . '/applications') ?>" class="btn btn-outline-secondary btn-sm ml-2">
                        Clear
                    </a>
                </div>
            </form>

            <?php if (!empty($applications)): ?>
                <form method="post" action="<?= base_url('recruiter/jobs/' . $job['id'] . '/applications/bulk') ?>" id="bulkActionForm" class="mb-3">
                    <?= csrf_field() ?>
                    <div class="d-flex flex-wrap align-items-center" style="gap: 10px;">
                        <select name="bulk_action" id="bulkActionSelect" class="form-control form-control-sm" style="max-width: 240px;">
                            <option value="">Bulk Action</option>
                            <option value="shortlist">Shortlist Selected</option>
                            <option value="reject">Reject Selected</option>
                            <option value="message">Message Selected</option>
                        </select>
                        <input type="text" name="bulk_message" id="bulkMessageInput" class="form-control form-control-sm" style="min-width: 280px; max-width: 480px;" placeholder="Message for selected candidates (required only for Message action)">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-bolt"></i> Apply
                        </button>
                        <small class="text-muted">Select candidates using the first column.</small>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="selectAllApplications" title="Select all">
                                </th>
                                <th>ID</th>
                                <th>Candidate</th>
                                <th>Email</th>
                                <th>Experience</th>
                                <th>Skills</th>
                                <th>Tags</th>
                                <th>Notes</th>
                                <th>Status</th>
                                <th>ATS Score</th>
                                <th>AI Rating</th>
                                <th>Applied Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="application-checkbox" value="<?= (int) $app['id'] ?>">
                                    </td>
                                    <td>#<?= $app['id'] ?></td>
                                    <td><strong><?= esc($app['name']) ?></strong></td>
                                    <td><?= esc($app['email']) ?></td>
                                    <td><?= esc($app['experience_display'] ?? '-') ?></td>
                                    <td>
                                        <?php if (!empty($app['skill_name'])): ?>
                                            <small><?= esc($app['skill_name']) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($app['recruiter_tags'])): ?>
                                            <?php foreach (explode(',', (string) $app['recruiter_tags']) as $tag): ?>
                                                <?php $trimmedTag = trim($tag); ?>
                                                <?php if ($trimmedTag !== ''): ?>
                                                    <span class="badge badge-light border mb-1"><?= esc($trimmedTag) ?></span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($app['recruiter_notes'])): ?>
                                            <?php
                                            $fullNote = trim((string) $app['recruiter_notes']);
                                            $shortNote = mb_strlen($fullNote) > 80 ? mb_substr($fullNote, 0, 80) . '...' : $fullNote;
                                            ?>
                                            <small title="<?= esc($fullNote) ?>"><?= esc($shortNote) ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'applied' => 'warning',
                                            'ai_interview_started' => 'info',
                                            'ai_interview_completed' => 'primary',
                                            'shortlisted' => 'success',
                                            'interview_slot_booked' => 'success',
                                            'selected' => 'success',
                                            'rejected' => 'danger'
                                        ];
                                        $color = $statusColors[$app['status']] ?? 'secondary';
                                        $statusLabels = [
                                            'pending' => 'Applied',
                                            'applied' => 'Applied',
                                            'ai_interview_started' => 'AI Interview In Progress',
                                            'ai_interview_completed' => 'AI Interviewed',
                                            'shortlisted' => 'Shortlisted',
                                            'interview_slot_booked' => 'Interview Booked',
                                            'selected' => 'Selected',
                                            'rejected' => 'Rejected',
                                        ];
                                        $label = $statusLabels[$app['status']] ?? ucwords(str_replace('_', ' ', $app['status']));
                                        ?>
                                        <span class="badge badge-<?= $color ?>">
                                            <?= esc($label) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $ats = (int) ($app['ats_score'] ?? 0);
                                        $atsBadge = 'danger';
                                        if ($ats >= 80) {
                                            $atsBadge = 'success';
                                        } elseif ($ats >= 60) {
                                            $atsBadge = 'warning';
                                        } elseif ($ats >= 40) {
                                            $atsBadge = 'info';
                                        }
                                        ?>
                                        <span class="badge badge-<?= $atsBadge ?>"><?= $ats ?>%</span>
                                    </td>
                                    <td>
                                        <?php if ($app['overall_rating']): ?>
                                            <span class="badge badge-info"><?= $app['overall_rating'] ?>/10</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($app['applied_at'])) ?></td>
                                    <td>
                                        <div class="application-actions-wrap">
                                            <a href="<?= base_url('recruiter/candidate/' . $app['candidate_id'] . '?application_id=' . $app['id'] . '&job_id=' . $job['id']) ?>" class="btn btn-sm btn-primary" target="_blank">
                                                <i class="fas fa-user"></i> View Profile
                                            </a>
                                            <?php if (!empty($app['can_manual_decision'])): ?>
                                                <form method="post" action="<?= base_url('recruiter/applications/shortlist/' . $app['id']) ?>" class="application-action-form">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-success">
                                                        <i class="fas fa-check"></i> Shortlist
                                                    </button>
                                                </form>
                                                <form method="post" action="<?= base_url('recruiter/applications/reject/' . $app['id']) ?>" class="application-action-form">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                </form>
                                            <?php elseif (($app['status'] ?? '') !== 'interview_slot_booked' && ($app['status'] ?? '') !== 'selected'): ?>
                                                <small class="text-muted d-block mt-1">Awaiting AI interview completion</small>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5>No applications yet</h5>
                    <p class="text-muted">Applications will appear here once candidates apply</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var selectAll = document.getElementById('selectAllApplications');
    var checkboxes = Array.prototype.slice.call(document.querySelectorAll('.application-checkbox'));
    var bulkForm = document.getElementById('bulkActionForm');
    var bulkAction = document.getElementById('bulkActionSelect');
    var bulkMessage = document.getElementById('bulkMessageInput');

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(function (cb) {
                cb.checked = selectAll.checked;
            });
        });
    }

    checkboxes.forEach(function (cb) {
        cb.addEventListener('change', function () {
            if (!selectAll) {
                return;
            }
            var allChecked = checkboxes.length > 0 && checkboxes.every(function (item) { return item.checked; });
            selectAll.checked = allChecked;
        });
    });

    if (bulkForm) {
        bulkForm.addEventListener('submit', function (event) {
            var selected = checkboxes.filter(function (cb) { return cb.checked; });
            if (!bulkAction || !bulkAction.value) {
                event.preventDefault();
                alert('Please choose a bulk action.');
                return;
            }
            if (selected.length === 0) {
                event.preventDefault();
                alert('Please select at least one candidate.');
                return;
            }
            if (bulkAction.value === 'message' && (!bulkMessage || bulkMessage.value.trim() === '')) {
                event.preventDefault();
                alert('Please enter a message for selected candidates.');
                return;
            }

            bulkForm.querySelectorAll('input[name=\"application_ids[]\"]').forEach(function (input) {
                input.remove();
            });

            selected.forEach(function (cb) {
                var hidden = document.createElement('input');
                hidden.type = 'hidden';
                hidden.name = 'application_ids[]';
                hidden.value = cb.value;
                bulkForm.appendChild(hidden);
            });
        });
    }
});
</script>

<?= view('Layouts/recruiter_footer') ?>
