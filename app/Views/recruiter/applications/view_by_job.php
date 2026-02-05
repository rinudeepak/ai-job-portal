<?= view('layouts/recruiter_header', ['title' => 'Applications - ' . $job['title']]) ?>

<div class="container-fluid py-5">
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

<?= view('layouts/recruiter_footer') ?>
