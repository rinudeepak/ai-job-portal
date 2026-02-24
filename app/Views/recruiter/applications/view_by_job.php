<?= view('Layouts/recruiter_header', ['title' => 'Applications - ' . $job['title']]) ?>

<div class="container-fluid py-5">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="mb-4">
        <a href="<?= base_url('recruiter/applications') ?>" class="btn btn-outline-secondary">
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
            <div class="mt-2">
                <span class="badge badge-info">AI Policy: <?= esc(str_replace('_', ' ', $policy)) ?></span>
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
            <?php if (!empty($applications)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th>ID</th>
                                <th>Candidate</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>AI Rating</th>
                                <th>Applied Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($applications as $app): ?>
                                <tr>
                                    <td>#<?= $app['id'] ?></td>
                                    <td><strong><?= esc($app['name']) ?></strong></td>
                                    <td><?= esc($app['email']) ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'ai_interview_started' => 'info',
                                            'ai_interview_completed' => 'primary',
                                            'shortlisted' => 'success',
                                            'interview_slot_booked' => 'success',
                                            'rejected' => 'danger'
                                        ];
                                        $color = $statusColors[$app['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?= $color ?>">
                                            <?= ucwords(str_replace('_', ' ', $app['status'])) ?>
                                        </span>
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
                                        <a href="<?= base_url('recruiter/candidate/' . $app['candidate_id'] . '?application_id=' . $app['id'] . '&job_id=' . $job['id']) ?>" class="btn btn-sm btn-primary" target="_blank">
                                            <i class="fas fa-user"></i> View Profile
                                        </a>
                                        <?php if ($app['status'] !== 'interview_slot_booked'): ?>
                                            <form method="post" action="<?= base_url('recruiter/applications/shortlist/' . $app['id']) ?>" class="d-inline-block">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i> Shortlist
                                                </button>
                                            </form>
                                            <form method="post" action="<?= base_url('recruiter/applications/reject/' . $app['id']) ?>" class="d-inline-block">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        <?php endif; ?>
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

<?= view('Layouts/recruiter_footer') ?>
