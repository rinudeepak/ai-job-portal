<?= view('admin/layouts/header', ['title' => 'Reschedule Booking']) ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-sync-alt"></i> Reschedule Interview</h2>
                <a href="<?= base_url('admin/slots/bookings') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Bookings
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Current Booking Details -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Current Booking Details</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted">Booking ID:</label>
                        <div class="font-weight-bold">#<?= $booking['id'] ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">Candidate:</label>
                        <div class="font-weight-bold"><?= esc($booking['candidate_name'] ?? 'N/A') ?></div>
                        <small class="text-muted"><?= esc($booking['email'] ?? 'N/A') ?></small>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">Job Position:</label>
                        <div class="font-weight-bold"><?= esc($booking['job_title'] ?? 'N/A') ?></div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">Current Schedule:</label>
                        <div class="alert alert-info mb-0">
                            <strong><?= date('l, M d, Y', strtotime($booking['slot_datetime'])) ?></strong><br>
                            <span class="h5"><?= date('h:i A', strtotime($booking['slot_datetime'])) ?></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="text-muted">Status:</label>
                        <div>
                            <span class="badge badge-<?= $booking['booking_status'] === 'confirmed' ? 'success' : 'warning' ?>">
                                <?= ucfirst($booking['booking_status']) ?>
                            </span>
                        </div>
                    </div>

                    <?php if (isset($booking['reschedule_count']) && $booking['reschedule_count'] > 0): ?>
                        <div class="mb-0">
                            <label class="text-muted">Reschedule History:</label>
                            <div class="font-weight-bold text-warning">
                                Rescheduled <?= $booking['reschedule_count'] ?> time(s)
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3 bg-warning text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-exclamation-triangle"></i> Important</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0 small">
                        <li>The candidate will be notified automatically</li>
                        <li>This action cannot be undone</li>
                        <li>Provide a clear reason for rescheduling</li>
                        <li>Old slot will be released for other candidates</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Reschedule Form -->
        <div class="col-lg-8">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Select New Slot</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('admin/slots/process-reschedule') ?>" id="rescheduleForm">
                        <?= csrf_field() ?>
                        <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">

                        <!-- Reason for Reschedule -->
                        <div class="form-group">
                            <label for="reason">Reason for Rescheduling <span class="text-danger">*</span></label>
                            <textarea name="reason" id="reason" class="form-control" rows="3" 
                                      placeholder="Please provide a reason for rescheduling this interview..." required></textarea>
                            <small class="form-text text-muted">This will be shared with the candidate</small>
                        </div>

                        <!-- Available Slots -->
                        <div class="form-group">
                            <label>Available Slots <span class="text-danger">*</span></label>
                            
                            <?php if (empty($available_slots)): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    No available slots found for this job position. 
                                    <a href="<?= base_url('admin/slots/create') ?>" class="alert-link">Create new slots</a>
                                </div>
                            <?php else: ?>
                                <div class="slot-selection">
                                    <?php foreach ($available_slots as $date => $slots): ?>
                                        <div class="date-group mb-4">
                                            <h6 class="bg-light p-2 border-left border-primary pl-3">
                                                <i class="fas fa-calendar"></i> 
                                                <?= date('l, F d, Y', strtotime($date)) ?>
                                            </h6>
                                            
                                            <div class="row">
                                                <?php foreach ($slots as $slot): ?>
                                                    <div class="col-md-6 mb-3">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" 
                                                                   id="slot_<?= $slot['id'] ?>" 
                                                                   name="slot_id" 
                                                                   value="<?= $slot['id'] ?>" 
                                                                   class="custom-control-input" 
                                                                   required>
                                                            <label class="custom-control-label slot-label" for="slot_<?= $slot['id'] ?>">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <strong class="text-primary"><?= date('h:i A', strtotime($slot['slot_time'])) ?></strong>
                                                                    </div>
                                                                    <div>
                                                                        <span class="badge badge-success">
                                                                            <?= $slot['capacity'] - $slot['booked_count'] ?> available
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Confirmation -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="confirm_reschedule" required>
                                <label class="custom-control-label" for="confirm_reschedule">
                                    I confirm that I want to reschedule this interview and notify the candidate
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-group mb-0">
                            <?php if (!empty($available_slots)): ?>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sync-alt"></i> Reschedule Interview
                                </button>
                            <?php endif; ?>
                            <a href="<?= base_url('recruiter/slots/bookings') ?>" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('rescheduleForm');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            const selectedSlot = document.querySelector('input[name="slot_id"]:checked');
            const reason = document.getElementById('reason').value.trim();
            const confirm = document.getElementById('confirm_reschedule').checked;

            if (!selectedSlot) {
                e.preventDefault();
                alert('Please select a new time slot');
                return false;
            }

            if (!reason) {
                e.preventDefault();
                alert('Please provide a reason for rescheduling');
                return false;
            }

            if (!confirm) {
                e.preventDefault();
                alert('Please confirm that you want to reschedule this interview');
                return false;
            }

            if (!window.confirm('Are you sure you want to reschedule this interview? The candidate will be notified automatically.')) {
                e.preventDefault();
                return false;
            }
        });

        // Highlight selected slot
        const radioButtons = document.querySelectorAll('input[name="slot_id"]');
        radioButtons.forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.slot-label').forEach(label => {
                    label.classList.remove('selected-slot');
                });
                if (this.checked) {
                    this.closest('.custom-radio').querySelector('.slot-label').classList.add('selected-slot');
                }
            });
        });
    }
});
</script>

<style>
.card {
    border: none;
    border-radius: 0.5rem;
}

.slot-label {
    cursor: pointer;
    padding: 1rem;
    border: 2px solid #e3e6f0;
    border-radius: 0.5rem;
    display: block;
    transition: all 0.3s;
}

.slot-label:hover {
    border-color: #4e73df;
    background-color: #f8f9fc;
}

.selected-slot {
    border-color: #4e73df !important;
    background-color: #e7f0ff !important;
}

.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #4e73df;
    border-color: #4e73df;
}

.date-group {
    margin-bottom: 1.5rem;
}

.border-left-primary {
    border-left-width: 4px !important;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78,115,223,.25);
}
</style>

<?= view('admin/layouts/footer') ?>