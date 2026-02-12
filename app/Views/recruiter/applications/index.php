<?= view('Layouts/recruiter_header', ['title' => 'Job Applications']) ?>

<div class="container-fluid py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-briefcase"></i> My Jobs & Applications</h2>
            <p class="text-muted">View applications for each job posting</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <div class="row">
        <?php if (!empty($jobs)): ?>
            <?php foreach ($jobs as $job): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card shadow h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?= esc($job['title']) ?></h5>
                            <p class="text-muted mb-3">
                                <i class="fas fa-map-marker-alt"></i> <?= esc($job['location']) ?>
                            </p>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge badge-<?= $job['status'] == 'active' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($job['status']) ?>
                                </span>
                                <span class="text-muted">
                                    <i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($job['created_at'])) ?>
                                </span>
                            </div>
                            <div class="alert alert-light mb-3">
                                <h3 class="mb-0"><?= $job['application_count'] ?></h3>
                                <small class="text-muted">Total Applications</small>
                            </div>
                            <a href="<?= base_url('recruiter/applications/job/' . $job['id']) ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-eye"></i> View Applications
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h5>No jobs posted yet</h5>
                    <p>Start by posting a job to receive applications</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= view('Layouts/recruiter_footer') ?>
