<?= view('Layouts/candidate_header', ['title' => 'Job Alerts']) ?>

<div class="job-alerts-jobboard">
    <style>
        .switch-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 10px 0;
        }
        .switch-copy {
            flex: 1;
        }
        .switch-toggle {
            position: relative;
            width: 42px;
            height: 24px;
            margin: 0;
            flex-shrink: 0;
        }
        .switch-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }
        .switch-toggle-slider {
            position: absolute;
            inset: 0;
            cursor: pointer;
            background: #c7ced8;
            border-radius: 999px;
            transition: background .2s ease;
        }
        .switch-toggle-slider::before {
            content: '';
            position: absolute;
            height: 18px;
            width: 18px;
            left: 3px;
            top: 3px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 1px 4px rgba(15, 23, 42, .25);
            transition: transform .2s ease;
        }
        .switch-toggle input:checked + .switch-toggle-slider {
            background: #89ba16;
        }
        .switch-toggle input:checked + .switch-toggle-slider::before {
            transform: translateX(18px);
        }
        .switch-toggle input:disabled + .switch-toggle-slider {
            cursor: not-allowed;
            opacity: .65;
        }
    </style>
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                        <span class="mx-2 slash">/</span>
                        <a href="<?= base_url('jobs') ?>">Jobs</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong>Job Alerts</strong></span>
                    </div>
                    <div class="job-alerts-hero-title">
                        <span class="job-alerts-icon"><i class="fas fa-bell"></i></span>
                        <div>
                            <h1>Job Alerts</h1>
                            <p>Set your role, location, skills, and salary preferences once. We will notify you when matching jobs are posted.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="job-alerts-hero-panel">
                        <div class="job-alerts-hero-stat">
                            <strong>Instant updates</strong>
                            <span>In-app and email notifications for matching roles.</span>
                        </div>
                        <div class="job-alerts-hero-tags">
                            <span>Role-based</span>
                            <span>Location-aware</span>
                            <span>Salary filters</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section pt-0 content-wrap">
        <div class="container">

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
            <?php endif; ?>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Alert Status</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('candidate/job-alerts/settings') ?>">
                        <?= csrf_field() ?>
                        <div class="row align-items-end">
                            <div class="col-md-6 mb-3">
                                <div class="switch-row">
                                    <div class="switch-copy">
                                        <label for="job_alerts_enabled" class="mb-1 d-block">Job Alerts</label>
                                        <small class="text-muted">Turn job alerts on or off for your account without deleting your saved alert criteria.</small>
                                    </div>
                                    <input type="hidden" name="job_alerts_enabled" value="0">
                                    <label class="switch-toggle" for="job_alerts_enabled">
                                        <input type="checkbox" name="job_alerts_enabled" id="job_alerts_enabled" value="1" <?= !empty($jobAlertsEnabled) ? 'checked' : '' ?>>
                                        <span class="switch-toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Save Alert Status
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (empty($jobAlertsEnabled)): ?>
                <div class="alert alert-warning">
                    Job alerts are currently off. Turn them on above to create or activate alert rules.
                </div>
            <?php endif; ?>

            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Alert Preferences</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('candidate/job-alerts/create') ?>">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Role Keywords</label>
                                <input type="text" name="role_keywords" class="form-control" placeholder="PHP Developer, QA Engineer" value="<?= esc($jobAlertDefaults['role_keywords'] ?? '') ?>" <?= empty($jobAlertsEnabled) ? 'disabled' : '' ?>>
                                <small class="text-muted">Defaults from your profile headline or latest alert.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Location Keywords</label>
                                <input type="text" name="location_keywords" class="form-control" placeholder="Bangalore, Remote" value="<?= esc($jobAlertDefaults['location_keywords'] ?? '') ?>" <?= empty($jobAlertsEnabled) ? 'disabled' : '' ?>>
                                <small class="text-muted">Defaults from your preferred locations when available.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Employment Type</label>
                                <?php $defaultEmploymentType = (string) ($jobAlertDefaults['employment_type'] ?? ''); ?>
                                <select name="employment_type" class="form-control" <?= empty($jobAlertsEnabled) ? 'disabled' : '' ?>>
                                    <option value="">Any employment type</option>
                                    <?php foreach (['Full-time', 'Part-time', 'Contract', 'Internship', 'Freelance'] as $employmentTypeOption): ?>
                                        <option value="<?= esc($employmentTypeOption) ?>" <?= $defaultEmploymentType === $employmentTypeOption ? 'selected' : '' ?>><?= esc($employmentTypeOption) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">Defaults from your profile preferences when available.</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Skills Keywords</label>
                                <input type="text" name="skills_keywords" class="form-control" placeholder="PHP, MySQL, React" value="<?= esc($jobAlertDefaults['skills_keywords'] ?? '') ?>" <?= empty($jobAlertsEnabled) ? 'disabled' : '' ?>>
                                <small class="text-muted">Defaults from your key skills or latest alert.</small>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Salary Min</label>
                                <input type="number" name="salary_min" class="form-control" min="0" placeholder="300000" value="<?= esc($jobAlertDefaults['salary_min'] ?? '') ?>" <?= empty($jobAlertsEnabled) ? 'disabled' : '' ?>>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Salary Max</label>
                                <input type="number" name="salary_max" class="form-control" min="0" placeholder="1200000" value="<?= esc($jobAlertDefaults['salary_max'] ?? '') ?>" <?= empty($jobAlertsEnabled) ? 'disabled' : '' ?>>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="switch-row">
                                    <div class="switch-copy">
                                        <label for="notify_in_app" class="mb-1 d-block">In-App Notification</label>
                                        <small class="text-muted">Show alert matches inside the portal.</small>
                                    </div>
                                    <input type="hidden" name="notify_in_app" value="0">
                                    <label class="switch-toggle" for="notify_in_app">
                                        <input type="checkbox" name="notify_in_app" id="notify_in_app" value="1" <?= !empty($jobAlertDefaults['notify_in_app']) ? 'checked' : '' ?> <?= empty($jobAlertsEnabled) ? 'disabled' : '' ?>>
                                        <span class="switch-toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="switch-row">
                                    <div class="switch-copy">
                                        <label for="notify_email" class="mb-1 d-block">Email Notification</label>
                                        <small class="text-muted">Send matching jobs to your registered email address.</small>
                                    </div>
                                    <input type="hidden" name="notify_email" value="0">
                                    <label class="switch-toggle" for="notify_email">
                                        <input type="checkbox" name="notify_email" id="notify_email" value="1" <?= !empty($jobAlertDefaults['notify_email']) ? 'checked' : '' ?> <?= empty($jobAlertsEnabled) ? 'disabled' : '' ?>>
                                        <span class="switch-toggle-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" <?= empty($jobAlertsEnabled) ? 'disabled' : '' ?>>
                            <i class="fas fa-plus"></i> Save Alert Preferences
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">My Alerts</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($alerts)): ?>
                        <p class="text-muted mb-0">No alerts configured yet.</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Role</th>
                                        <th>Location</th>
                                        <th>Employment Type</th>
                                        <th>Skills</th>
                                        <th>Salary</th>
                                        <th>Channels</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($alerts as $alert): ?>
                                        <tr>
                                            <td><?= esc($alert['role_keywords'] ?? '-') ?></td>
                                            <td><?= esc($alert['location_keywords'] ?? '-') ?></td>
                                            <td><?= esc($alert['employment_type'] ?? '-') ?></td>
                                            <td><?= esc($alert['skills_keywords'] ?? '-') ?></td>
                                            <td>
                                                <?php
                                                $min = $alert['salary_min'];
                                                $max = $alert['salary_max'];
                                                if ($min === null && $max === null) {
                                                    echo '-';
                                                } elseif ($min !== null && $max !== null) {
                                                    echo esc((string) $min . ' - ' . (string) $max);
                                                } elseif ($min !== null) {
                                                    echo '>= ' . esc((string) $min);
                                                } else {
                                                    echo '<= ' . esc((string) $max);
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ((int) $alert['notify_in_app'] === 1): ?>
                                                    <span class="badge badge-info">In-App</span>
                                                <?php endif; ?>
                                                <?php if ((int) $alert['notify_email'] === 1): ?>
                                                    <span class="badge badge-primary">Email</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= (int) $alert['is_active'] === 1 ? 'success' : 'secondary' ?>">
                                                    <?= (int) $alert['is_active'] === 1 ? 'Active' : 'Paused' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('candidate/job-alerts/toggle/' . (int) $alert['id']) ?>" class="btn btn-sm btn-outline-warning <?= empty($jobAlertsEnabled) ? 'disabled' : '' ?>" <?= empty($jobAlertsEnabled) ? 'aria-disabled="true" onclick="return false;"' : '' ?>>
                                                    <?= (int) $alert['is_active'] === 1 ? 'Pause' : 'Activate' ?>
                                                </a>
                                                <a href="<?= base_url('candidate/job-alerts/delete/' . (int) $alert['id']) ?>"
                                                   class="btn btn-sm btn-outline-danger"
                                                   onclick="return confirm('Delete this alert?')">
                                                    Delete
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
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
