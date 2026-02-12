<?= view('Layouts/recruiter_header', ['title' => 'My Jobs']) ?>

<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-briefcase"></i> My Posted Jobs</h2>
            <p class="text-muted">Manage your job postings</p>
        </div>
        <a href="<?= base_url('recruiter/post_job') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Post New Job
        </a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>Job Title</th>
                            <th>Location</th>
                            <th>Applications</th>
                            <th>Status</th>
                            <th>Posted Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($jobs)): ?>
                            <?php foreach ($jobs as $job): ?>
                                <tr>
                                    <td><strong><?= esc($job['title']) ?></strong></td>
                                    <td><?= esc($job['location']) ?></td>
                                    <td>
                                        <a href="<?= base_url('recruiter/applications/job/' . $job['id']) ?>">
                                            <span class="badge badge-primary"><?= $job['application_count'] ?></span>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= $job['status'] == 'open' ? 'success' : 'secondary' ?>">
                                            <?= ucfirst($job['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('M d, Y', strtotime($job['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('recruiter/jobs/edit/' . $job['id']) ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <?php if ($job['status'] == 'open'): ?>
                                            <a href="<?= base_url('recruiter/jobs/close/' . $job['id']) ?>" 
                                               class="btn btn-sm btn-warning"
                                               onclick="return confirm('Are you sure you want to close this job?')">
                                                <i class="fas fa-times-circle"></i> Close
                                            </a>
                                        <?php else: ?>
                                            <a href="<?= base_url('recruiter/jobs/reopen/' . $job['id']) ?>" class="btn btn-sm btn-success">
                                                <i class="fas fa-check-circle"></i> Reopen
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                    <h5>No jobs posted yet</h5>
                                    <p class="text-muted">Start by posting your first job</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= view('Layouts/recruiter_footer') ?>
