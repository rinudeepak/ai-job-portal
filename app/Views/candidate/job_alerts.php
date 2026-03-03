<?= view('Layouts/candidate_header', ['title' => 'Job Alerts']) ?>

<div class="job-alerts-jobboard">
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
                    <h5 class="mb-0">Create Alert</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('candidate/job-alerts/create') ?>">
                        <?= csrf_field() ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Role Keywords</label>
                                <input type="text" name="role_keywords" class="form-control" placeholder="PHP Developer, QA Engineer">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Location Keywords</label>
                                <input type="text" name="location_keywords" class="form-control" placeholder="Bangalore, Remote">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Skills Keywords</label>
                                <input type="text" name="skills_keywords" class="form-control" placeholder="PHP, MySQL, React">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Salary Min</label>
                                <input type="number" name="salary_min" class="form-control" min="0" placeholder="300000">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Salary Max</label>
                                <input type="number" name="salary_max" class="form-control" min="0" placeholder="1200000">
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="notify_in_app" id="notify_in_app" value="1" checked>
                                    <label class="form-check-label" for="notify_in_app">In-App Notification</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="notify_email" id="notify_email" value="1" checked>
                                    <label class="form-check-label" for="notify_email">Email Notification</label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create Alert
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
                                                <a href="<?= base_url('candidate/job-alerts/toggle/' . (int) $alert['id']) ?>" class="btn btn-sm btn-outline-warning">
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
