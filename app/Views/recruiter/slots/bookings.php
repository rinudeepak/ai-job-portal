<?= view('Layouts/recruiter_header', ['title' => 'Interview Bookings']) ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-calendar-check"></i> Interview Bookings</h2>
                <a href="<?= base_url('recruiter/slots') ?>" class="btn btn-secondary">
                    <i class="fas fa-calendar-alt"></i> Back to Slots
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
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Bookings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['total_bookings'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Upcoming</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['upcoming'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Completed</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['completed'] ?></div>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Rescheduled</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $stats['rescheduled'] ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-sync fa-2x text-gray-300"></i>
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
            <form method="get" action="<?= base_url('recruiter/slots/bookings') ?>">
                <div class="row">
                    <div class="col-md-4">
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

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">All Status</option>
                                <option value="confirmed" <?= ($filters['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                                <option value="rescheduled" <?= ($filters['status'] ?? '') === 'rescheduled' ? 'selected' : '' ?>>Rescheduled</option>
                                <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings Table -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">All Bookings</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Candidate</th>
                            <th>Job</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                            <th>Booked On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No bookings found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $booking): ?>
                                <?php
                                $isPast = strtotime($booking['slot_datetime']) < time();
                                $isUpcoming = strtotime($booking['slot_datetime']) > time();
                                ?>
                                <tr class="<?= $isPast ? 'table-secondary' : '' ?>">
                                    <td><?= $booking['id'] ?></td>
                                    <td>
                                        <strong><?= esc($booking['candidate_name']) ?></strong><br>
                                        <small class="text-muted"><?= esc($booking['email']) ?></small>
                                    </td>
                                    <td><?= esc($booking['job_title']) ?></td>
                                    <td>
                                        <strong><?= date('M d, Y', strtotime($booking['slot_date'])) ?></strong><br>
                                        <span class="text-primary"><?= date('h:i A', strtotime($booking['slot_time'])) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'confirmed' => 'success',
                                            'completed' => 'info',
                                            'rescheduled' => 'warning',
                                            'cancelled' => 'danger'
                                        ];
                                        $color = $statusColors[$booking['booking_status']] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?= $color ?>">
                                            <?= ucfirst($booking['booking_status']) ?>
                                        </span>
                                        <?php if ($booking['reschedule_count'] > 0): ?>
                                            <br><small class="text-muted">Rescheduled: <?= $booking['reschedule_count'] ?>x</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?= date('M d, Y', strtotime($booking['booked_at'])) ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <?php if ($isUpcoming && $booking['booking_status'] === 'confirmed'): ?>
                                                <a href="<?= base_url('recruiter/slots/reschedule/' . $booking['id']) ?>" 
                                                   class="btn btn-sm btn-warning" title="Reschedule">
                                                    <i class="fas fa-sync"></i>
                                                </a>
                                                <form method="post" action="<?= base_url('recruiter/slots/mark-completed/' . $booking['id']) ?>" style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-success" 
                                                            title="Mark Completed"
                                                            onclick="return confirm('Mark this interview as completed?')">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            <?php elseif ($isPast && $booking['booking_status'] === 'confirmed'): ?>
                                                <form method="post" action="<?= base_url('recruiter/slots/mark-completed/' . $booking['id']) ?>" style="display: inline;">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-success" 
                                                            title="Mark Completed"
                                                            onclick="return confirm('Mark this interview as completed?')">
                                                        <i class="fas fa-check"></i> Complete
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if (isset($pager) && is_object($pager) && method_exists($pager, 'links')): ?>
                <?= $pager->links() ?>
            <?php endif; ?>
        </div>
    </div>
</div>



<?= view('Layouts/recruiter_footer') ?>
