<?= view('layouts/recruiter_header', ['title' => 'Interview Slots']) ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-calendar-alt"></i> Interview Slots Management</h2>
                <a href="<?= base_url('recruiter/slots/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Slots
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Slots</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_slots'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Available Slots</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['available_slots'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Fully Booked</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['fully_booked'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Total Bookings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_bookings'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('recruiter/slots') ?>">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Job</label>
                            <select name="job_id" class="form-control">
                                <option value="">All Jobs</option>
                                <?php foreach ($jobs as $job): ?>
                                    <option value="<?= $job['id'] ?>" <?= ($filters['job_id'] ?? '') == $job['id'] ? 'selected' : '' ?>>
                                        <?= esc($job['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="date" class="form-control" value="<?= esc($filters['date'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="available" <?= ($filters['status'] ?? '') === 'available' ? 'selected' : '' ?>>Available</option>
                                <option value="full" <?= ($filters['status'] ?? '') === 'full' ? 'selected' : '' ?>>Fully Booked</option>
                                <option value="past" <?= ($filters['status'] ?? '') === 'past' ? 'selected' : '' ?>>Past Slots</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="text-right">
                <a href="<?= base_url('recruiter/slots/bookings') ?>" class="btn btn-info">
                    <i class="fas fa-list"></i> View All Bookings
                </a>
            </div>
        </div>
    </div>

    <!-- Slots Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Interview Slots</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Job</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Capacity</th>
                            <th>Booked</th>
                            <th>Status</th>
                            <th>Created By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($slots)): ?>
                            <tr>
                                <td colspan="9" class="text-center">No slots found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($slots as $slot): ?>
                                <?php
                                $isPast = strtotime($slot['slot_datetime']) < time();
                                $isAvailable = $slot['is_available'] && !$isPast;
                                $isFull = $slot['booked_count'] >= $slot['capacity'];
                                ?>
                                <tr class="<?= $isPast ? 'table-secondary' : ($isFull ? 'table-warning' : '') ?>">
                                    <td><?= $slot['id'] ?></td>
                                    <td><?= esc($slot['job_title']) ?></td>
                                    <td><?= date('M d, Y', strtotime($slot['slot_date'])) ?></td>
                                    <td><strong><?= date('h:i A', strtotime($slot['slot_time'])) ?></strong></td>
                                    <td><?= $slot['capacity'] ?></td>
                                    <td>
                                        <span class="badge badge-<?= $slot['booked_count'] > 0 ? 'primary' : 'secondary' ?>">
                                            <?= $slot['booked_count'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($isPast): ?>
                                            <span class="badge badge-secondary">Past</span>
                                        <?php elseif ($isFull): ?>
                                            <span class="badge badge-danger">Full</span>
                                        <?php elseif ($isAvailable): ?>
                                            <span class="badge badge-success">Available</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Unavailable</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($slot['created_by_name']) ?></td>
                                    <td>
                                        <?php if ($slot['booked_count'] == 0): ?>
                                            <a href="<?= base_url('recruiter/slots/edit/' . $slot['id']) ?>" 
                                               class="btn btn-sm btn-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="<?= base_url('recruiter/slots/delete/' . $slot['id']) ?>" 
                                               class="btn btn-sm btn-danger" 
                                               onclick="return confirm('Delete this slot?')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">Has bookings</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?= $pager->links() ?>
        </div>
    </div>
</div>

<?= view('layouts/recruiter_footer') ?>