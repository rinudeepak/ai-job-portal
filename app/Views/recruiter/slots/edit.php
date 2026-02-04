<?= view('layouts/recruiter_header', ['title' => 'Edit Interview Slot']) ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-edit"></i> Edit Interview Slot</h2>
                <a href="<?= base_url('recruiter/slots') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Slots
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Slot Details</h6>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle"></i> <?= session()->getFlashdata('error') ?>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if ($slot['booked_count'] > 0): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> This slot has <?= $slot['booked_count'] ?> booking(s). 
                            Slots with bookings cannot be edited.
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('recruiter/slots/update/' . $slot['id']) ?>" id="editSlotForm">
                        <?= csrf_field() ?>

                        <!-- Job Selection (Read-only) -->
                        <div class="form-group">
                            <label for="job_id">Job Position</label>
                            <select name="job_id" id="job_id" class="form-control" disabled>
                                <?php foreach ($jobs as $job): ?>
                                    <option value="<?= $job['id'] ?>" <?= $slot['job_id'] == $job['id'] ? 'selected' : '' ?>>
                                        <?= esc($job['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Job position cannot be changed</small>
                        </div>

                        <div class="row">
                            <!-- Slot Date -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="slot_date">Date <span class="text-danger">*</span></label>
                                    <input type="date" name="slot_date" id="slot_date" class="form-control" 
                                           value="<?= esc($slot['slot_date']) ?>" 
                                           min="<?= date('Y-m-d') ?>" 
                                           <?= $slot['booked_count'] > 0 ? 'readonly' : '' ?> required>
                                </div>
                            </div>

                            <!-- Slot Time -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="slot_time">Time <span class="text-danger">*</span></label>
                                    <input type="time" name="slot_time" id="slot_time" class="form-control" 
                                           value="<?= esc($slot['slot_time']) ?>" 
                                           <?= $slot['booked_count'] > 0 ? 'readonly' : '' ?> required>
                                </div>
                            </div>
                        </div>

                        <!-- Capacity -->
                        <div class="form-group">
                            <label for="capacity">Capacity <span class="text-danger">*</span></label>
                            <input type="number" name="capacity" id="capacity" class="form-control" 
                                   value="<?= esc($slot['capacity']) ?>" 
                                   min="<?= $slot['booked_count'] ?>" max="50" 
                                   <?= $slot['booked_count'] > 0 ? 'readonly' : '' ?> required>
                            <?php if ($slot['booked_count'] > 0): ?>
                                <small class="form-text text-muted">
                                    Cannot reduce capacity below current bookings (<?= $slot['booked_count'] ?>)
                                </small>
                            <?php else: ?>
                                <small class="form-text text-muted">Number of candidates that can book this slot</small>
                            <?php endif; ?>
                        </div>

                        <!-- Current Status -->
                        <div class="form-group">
                            <label>Current Status</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Booked:</strong> 
                                            <span class="badge badge-primary badge-lg">
                                                <?= $slot['booked_count'] ?> / <?= $slot['capacity'] ?>
                                            </span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Available:</strong> 
                                            <?php if ($slot['is_available'] && strtotime($slot['slot_datetime']) > time()): ?>
                                                <span class="badge badge-success badge-lg">Yes</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger badge-lg">No</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Created Info -->
                        <div class="form-group">
                            <label>Additional Information</label>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <small>
                                        <strong>Slot ID:</strong> #<?= $slot['id'] ?><br>
                                        <strong>Created:</strong> <?= date('M d, Y h:i A', strtotime($slot['created_at'])) ?><br>
                                        <?php if (isset($slot['updated_at'])): ?>
                                            <strong>Last Updated:</strong> <?= date('M d, Y h:i A', strtotime($slot['updated_at'])) ?>
                                        <?php endif; ?>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-group mb-0">
                            <?php if ($slot['booked_count'] == 0): ?>
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save"></i> Update Slot
                                </button>
                            <?php endif; ?>
                            <a href="<?= base_url('recruiter/slots') ?>" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($slot['booked_count'] > 0): ?>
                <!-- Bookings Info -->
                <div class="card shadow mt-4">
                    <div class="card-header py-3 bg-info text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-users"></i> Existing Bookings (<?= $slot['booked_count'] ?>)
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">
                            This slot has <?= $slot['booked_count'] ?> active booking(s). 
                            To modify this slot, please contact the candidates to reschedule or cancel their bookings first.
                        </p>
                        <div class="mt-3">
                            <a href="<?= base_url('recruiter/slots/bookings?slot_id=' . $slot['id']) ?>" 
                               class="btn btn-info">
                                <i class="fas fa-eye"></i> View Bookings
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<?= view('layouts/recruiter_footer') ?>