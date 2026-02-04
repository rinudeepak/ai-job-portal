<?= view('Layouts/candidate_header', ['title' => 'Reschedule Interview']) ?>

<div class="slider-area">
    <div class="single-slider section-overly slider-height2 d-flex align-items-center"
        data-background="<?= base_url('assets/img/hero/about.jpg') ?>">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="hero-cap text-center">
                        <h2>Reschedule Interview</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="contact-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="contact-title">Select New Interview Slot</h2>
            </div>

            <div class="col-lg-8 offset-lg-2">
                <!-- Current Booking Info -->
                <div class="card mb-4" style="border-left: 4px solid #dc3545;">
                    <div class="card-body">
                        <h5><i class="fas fa-calendar-check"></i> Current Interview</h5>
                        <p class="mb-2">
                            <strong>Date:</strong> <?= date('l, F j, Y', strtotime($booking['slot_datetime'])) ?><br>
                            <strong>Time:</strong> <?= date('h:i A', strtotime($booking['slot_datetime'])) ?>
                        </p>
                        <div class="alert alert-warning mb-0">
                            <strong>Reschedules Remaining:</strong> 
                            <?= $can_reschedule_info['remaining_reschedules'] ?> out of <?= $booking['max_reschedules'] ?>
                        </div>
                    </div>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($available_slots)): ?>
                    <div class="alert alert-warning text-center">
                        <h5>No Alternative Slots Available</h5>
                        <p>Please contact HR for assistance.</p>
                    </div>
                <?php else: ?>
                    <form method="post" action="<?= base_url('candidate/process-reschedule') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="application_id" value="<?= $application['id'] ?>">

                        <div class="slot-selection">
                            <?php foreach ($available_slots as $date => $slots): ?>
                                <div class="card mb-4">
                                    <div class="card-header" style="background: #f5576c; color: white;">
                                        <h5 class="mb-0">
                                            <i class="fas fa-calendar-day"></i>
                                            <?= date('l, F j, Y', strtotime($date)) ?>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php foreach ($slots as $slot): ?>
                                                <?php
                                                // Skip if this is the current slot
                                                if ($slot['id'] == $booking['slot_id']) continue;
                                                ?>
                                                <div class="col-md-4 mb-3">
                                                    <input type="radio" name="slot_id" id="slot_<?= $slot['id'] ?>" 
                                                           value="<?= $slot['id'] ?>" class="slot-radio" required>
                                                    <label for="slot_<?= $slot['id'] ?>" class="slot-label">
                                                        <i class="fas fa-clock"></i>
                                                        <?= date('h:i A', strtotime($slot['slot_time'])) ?>
                                                        <br>
                                                        <small class="text-muted">
                                                            <?= $slot['capacity'] - $slot['booked_count'] ?> spot(s) left
                                                        </small>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="form-group">
                            <label>Reason for Rescheduling (Optional)</label>
                            <textarea class="form-control" name="reason" rows="3" 
                                      placeholder="Please provide a brief reason..."></textarea>
                        </div>

                        <div class="alert alert-danger">
                            <strong><i class="fas fa-exclamation-triangle"></i> Warning:</strong>
                            <ul class="mb-0">
                                <li>You have <strong><?= $can_reschedule_info['remaining_reschedules'] ?></strong> reschedule(s) remaining</li>
                                <li>After reaching the limit, you won't be able to reschedule again</li>
                                <li>Cancellation is <strong>NOT allowed</strong></li>
                            </ul>
                        </div>

                        <div class="text-center mt-4">
                            <a href="<?= base_url('candidate/my-bookings') ?>" class="btn btn-secondary mr-2">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="button button-contactForm boxed-btn">
                                <i class="fas fa-sync"></i> Confirm Reschedule
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <!-- Reschedule History -->
                <?php if (!empty($history)): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-history"></i> Reschedule History</h5>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <?php foreach ($history as $item): ?>
                                    <div class="timeline-item">
                                        <div class="timeline-badge">
                                            <i class="fas fa-sync"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <p class="mb-1">
                                                <strong>From:</strong> <?= date('M j, Y h:i A', strtotime($item['old_slot_datetime'])) ?><br>
                                                <strong>To:</strong> <?= date('M j, Y h:i A', strtotime($item['new_slot_datetime'])) ?>
                                            </p>
                                            <?php if ($item['reason']): ?>
                                                <p class="text-muted mb-1"><em>"<?= esc($item['reason']) ?>"</em></p>
                                            <?php endif; ?>
                                            <small class="text-muted">
                                                <?= date('M j, Y h:i A', strtotime($item['rescheduled_at'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>



<?= view('layouts/candidate_footer') ?>
