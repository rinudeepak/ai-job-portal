<?= view('Layouts/candidate_header', ['title' => 'My Interview Bookings']) ?>

<section class="contact-section pt-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="contact-title">Your Scheduled Interviews</h2>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="col-lg-10 offset-lg-1">
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('success') ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="col-lg-10 offset-lg-1">
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                    </div>
                </div>
            <?php endif; ?>

            <div class="col-lg-10 offset-lg-1">
                <?php if (empty($bookings)): ?>
                    <div class="card text-center">
                        <div class="card-body py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <h5>No Interview Bookings</h5>
                            <p class="text-muted">You don't have any scheduled interviews yet.</p>
                            <a href="<?= base_url('dashboard') ?>" class="btn btn-primary">
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
                        
                        <div class="card mb-4 <?= $isPast ? 'border-secondary' : 'border-primary' ?>">
                            <div class="card-header bg-<?= $statusColor ?> text-white">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">
                                        <i class="fas fa-briefcase"></i> 
                                        <?= esc($booking['job_title']) ?>
                                    </h5>
                                    <span class="badge badge-light text-<?= $statusColor ?>">
                                        <?= ucfirst($booking['booking_status']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-calendar-day"></i> Interview Date</h6>
                                        <p class="mb-3">
                                            <?= date('l, F j, Y', strtotime($booking['slot_datetime'])) ?><br>
                                            <strong><?= date('h:i A', strtotime($booking['slot_datetime'])) ?></strong>
                                        </p>
                                        
                                        <?php if ($isUpcoming): ?>
                                            <?php
                                            $diff = strtotime($booking['slot_datetime']) - time();
                                            $days = floor($diff / 86400);
                                            $hours = floor(($diff % 86400) / 3600);
                                            ?>
                                            <div class="alert alert-info">
                                                <strong><i class="fas fa-clock"></i> Time Remaining:</strong><br>
                                                <?php if ($days > 0): ?>
                                                    <?= $days ?> day(s) <?= $hours ?> hour(s)
                                                <?php else: ?>
                                                    <?= $hours ?> hour(s)
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-info-circle"></i> Booking Details</h6>
                                        <p class="mb-2">
                                            <strong>Booked on:</strong> <?= date('M j, Y', strtotime($booking['booked_at'])) ?>
                                        </p>
                                        <p class="mb-2">
                                            <strong>Reschedules used:</strong> 
                                            <?= $booking['reschedule_count'] ?> / <?= $booking['max_reschedules'] ?>
                                        </p>
                                        <?php if ($booking['last_rescheduled_at']): ?>
                                            <p class="mb-2">
                                                <strong>Last rescheduled:</strong> 
                                                <?= date('M j, Y h:i A', strtotime($booking['last_rescheduled_at'])) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <?php if ($canReschedule): ?>
                                        <a href="<?= base_url('candidate/reschedule-slot/' . $booking['application_id']) ?>" 
                                           class="btn btn-warning">
                                            <i class="fas fa-sync"></i> Reschedule Interview
                                        </a>
                                    <?php elseif ($isUpcoming && !$canReschedule): ?>
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fas fa-ban"></i> Cannot Reschedule
                                        </button>
                                        <small class="text-muted">
                                            <?php if ($booking['reschedule_count'] >= $booking['max_reschedules']): ?>
                                                Reschedule limit reached
                                            <?php else: ?>
                                                Too close to interview time (< 24 hours)
                                            <?php endif; ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">Interview completed</span>
                                    <?php endif; ?>
                                    
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
        </div>
    </div>
</section>

<?= view('layouts/candidate_footer') ?>
