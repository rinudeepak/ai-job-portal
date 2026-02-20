<?= view('Layouts/candidate_header', ['title' => 'Book Interview Slot']) ?>

<div class="book-slot-jobboard">
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1 class="text-white font-weight-bold">Book Interview Slot</h1>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                        <span class="mx-2 slash">/</span>
                        <a href="<?= base_url('candidate/my-bookings') ?>">My Bookings</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong>Book Slot</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section pt-0 content-wrap">
        <div class="container">
            <div class="col-lg-10 offset-lg-1 px-0">
                <div class="alert alert-success booking-alert">
                    <h5><i class="fas fa-check-circle mr-2"></i>Congratulations!</h5>
                    <p class="mb-0">
                        You have been shortlisted for <strong><?= esc($application['job_title'] ?? 'this position') ?></strong>.
                        Please select an available interview slot below.
                    </p>
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
                    <form method="post" action="<?= base_url('candidate/process-booking') ?>" class="slot-form-card">
                        <?= csrf_field() ?>
                        <input type="hidden" name="application_id" value="<?= $application['id'] ?>">

                        <div class="slot-selection">
                            <?php foreach ($available_slots as $date => $slots): ?>
                                <div class="card mb-4 slot-day-card">
                                    <div class="card-header slot-day-head">
                                        <h5 class="mb-0">
                                            <i class="fas fa-calendar-day mr-2"></i>
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
                                                        <span class="slot-time"><i class="fas fa-clock mr-2"></i><?= date('h:i A', strtotime($slot['slot_time'])) ?></span>
                                                        <small class="slot-spots">
                                                            <?= (int) $slot['capacity'] - (int) $slot['booked_count'] ?> spot(s) left
                                                        </small>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="alert alert-info booking-alert mb-4">
                            <strong><i class="fas fa-info-circle mr-1"></i>Important:</strong>
                            <ul class="mb-0">
                                <li>You can reschedule your interview up to <strong>2 times</strong></li>
                                <li>Cancellation is <strong>not allowed</strong></li>
                                <li>Rescheduling must be done at least <strong>24 hours</strong> before the interview</li>
                            </ul>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check mr-1"></i> Confirm Booking
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>
