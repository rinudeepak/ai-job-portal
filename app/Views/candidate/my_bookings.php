<?= view('Layouts/candidate_header', ['title' => 'My Interview Bookings']) ?>

<div class="my-bookings-jobboard">
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1 class="text-white font-weight-bold">My Interview Bookings</h1>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong>My Bookings</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section pt-0 content-wrap">
        <div class="container">
            <?php
            $bookings = $bookings ?? [];
            $upcomingCount = 0;
            $completedCount = 0;
            foreach ($bookings as $b) {
                if (strtotime($b['slot_datetime']) >= time()) {
                    $upcomingCount++;
                } elseif (($b['booking_status'] ?? '') === 'completed') {
                    $completedCount++;
                }
            }
            ?>

            <div class="bookings-summary-row">
                <span class="summary-chip">Total: <?= count($bookings) ?></span>
                <span class="summary-chip">Upcoming: <?= $upcomingCount ?></span>
                <span class="summary-chip">Completed: <?= $completedCount ?></span>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <?php if (empty($bookings)): ?>
                <div class="card booking-empty-card text-center">
                    <div class="card-body py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                        <h5 class="mb-2">No Interview Bookings</h5>
                        <p class="text-muted mb-4">You do not have any scheduled interviews yet.</p>
                        <a href="<?= base_url('candidate/dashboard') ?>" class="btn btn-primary">
                            Go to Dashboard
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($bookings as $booking): ?>
                    <?php
                    $isPast = strtotime($booking['slot_datetime']) < time();
                    $isUpcoming = !$isPast;
                    $canReschedule = $isUpcoming && $booking['reschedule_count'] < $booking['max_reschedules']
                                  && (strtotime($booking['slot_datetime']) - time()) > 86400;

                    $statusColors = [
                        'booked' => 'primary',
                        'rescheduled' => 'warning',
                        'completed' => 'success',
                        'cancelled' => 'danger'
                    ];
                    $statusColor = $statusColors[$booking['booking_status']] ?? 'secondary';
                    ?>

                    <div class="card booking-card mb-4 <?= $isPast ? 'booking-past' : 'booking-upcoming' ?>">
                        <div class="card-header booking-card-head">
                            <div class="d-flex justify-content-between align-items-center flex-wrap" style="gap: 10px;">
                                <h5 class="mb-0">
                                    <i class="fas fa-briefcase mr-2"></i><?= esc($booking['job_title']) ?>
                                </h5>
                                <span class="badge badge-<?= $statusColor ?> booking-status-badge">
                                    <?= ucfirst($booking['booking_status']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body booking-card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <h6 class="booking-label"><i class="fas fa-calendar-day mr-2"></i>Interview Date</h6>
                                    <p class="mb-2">
                                        <?= date('l, F j, Y', strtotime($booking['slot_datetime'])) ?><br>
                                        <strong><?= date('h:i A', strtotime($booking['slot_datetime'])) ?></strong>
                                    </p>

                                    <?php if ($isUpcoming): ?>
                                        <?php
                                        $diff = strtotime($booking['slot_datetime']) - time();
                                        $days = floor($diff / 86400);
                                        $hours = floor(($diff % 86400) / 3600);
                                        ?>
                                        <div class="alert alert-info booking-time-alert mb-0">
                                            <strong><i class="fas fa-clock mr-1"></i>Time Remaining:</strong>
                                            <?php if ($days > 0): ?>
                                                <?= $days ?> day(s) <?= $hours ?> hour(s)
                                            <?php else: ?>
                                                <?= $hours ?> hour(s)
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="booking-label"><i class="fas fa-info-circle mr-2"></i>Booking Details</h6>
                                    <p class="mb-2"><strong>Booked on:</strong> <?= date('M j, Y', strtotime($booking['booked_at'])) ?></p>
                                    <p class="mb-2">
                                        <strong>Reschedules used:</strong>
                                        <?= $booking['reschedule_count'] ?> / <?= $booking['max_reschedules'] ?>
                                    </p>
                                    <?php if ($booking['last_rescheduled_at']): ?>
                                        <p class="mb-0">
                                            <strong>Last rescheduled:</strong>
                                            <?= date('M j, Y h:i A', strtotime($booking['last_rescheduled_at'])) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-between align-items-center flex-wrap booking-actions-row" style="gap: 10px;">
                                <div>
                                    <?php if ($canReschedule): ?>
                                        <a href="<?= base_url('candidate/reschedule-slot/' . $booking['application_id']) ?>" class="btn btn-warning">
                                            <i class="fas fa-sync mr-1"></i>Reschedule Interview
                                        </a>
                                    <?php elseif ($isUpcoming && !$canReschedule): ?>
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-ban mr-1"></i>Cannot Reschedule
                                        </button>
                                        <small class="text-muted d-block mt-2">
                                            <?php if ($booking['reschedule_count'] >= $booking['max_reschedules']): ?>
                                                Reschedule limit reached
                                            <?php else: ?>
                                                Too close to interview time (&lt; 24 hours)
                                            <?php endif; ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">Interview completed</span>
                                    <?php endif; ?>
                                </div>

                                <?php if ($isUpcoming): ?>
                                    <div class="alert alert-danger mb-0 py-2 px-3">
                                        <small><strong>Note:</strong> Cancellation not allowed</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
