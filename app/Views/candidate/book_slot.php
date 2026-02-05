<?= view('Layouts/candidate_header', ['title' => 'Book Interview Slot']) ?>

<section class="contact-section pt-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="contact-title">Select Your Preferred Time</h2>
            </div>

            <div class="col-lg-8 offset-lg-2">
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle"></i> Congratulations!</h5>
                    <p class="mb-0">You have been shortlisted for <strong><?= esc($application['job_title'] ?? 'this position') ?></strong>. 
                    Please select an available interview slot below.</p>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger">
                        <?= session()->getFlashdata('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($available_slots)): ?>
                    <div class="alert alert-warning text-center">
                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                        <h5>No Slots Available</h5>
                        <p>There are currently no available interview slots. Please check back later or contact HR.</p>
                    </div>
                <?php else: ?>
                    <form method="post" action="<?= base_url('candidate/process-booking') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="application_id" value="<?= $application['id'] ?>">

                        <div class="slot-selection">
                            <?php foreach ($available_slots as $date => $slots): ?>
                                <div class="card mb-4">
                                    <div class="card-header" style="background: #667eea; color: white;">
                                        <h5 class="mb-0">
                                            <i class="fas fa-calendar-day"></i>
                                            <?= date('l, F j, Y', strtotime($date)) ?>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php foreach ($slots as $slot): ?>
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

                        <div class="alert alert-info">
                            <strong><i class="fas fa-info-circle"></i> Important:</strong>
                            <ul class="mb-0">
                                <li>You can reschedule your interview up to <strong>2 times</strong></li>
                                <li>Cancellation is <strong>not allowed</strong></li>
                                <li>Rescheduling must be done at least <strong>24 hours</strong> before the interview</li>
                            </ul>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="button button-contactForm boxed-btn">
                                <i class="fas fa-check"></i> Confirm Booking
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>



<?= view('layouts/candidate_footer') ?>