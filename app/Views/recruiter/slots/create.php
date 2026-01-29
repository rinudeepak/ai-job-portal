<?= view('layouts/recruiter_header', ['title' => 'Create Interview Slots']) ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-calendar-plus"></i> Create Interview Slots</h2>
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
                    <h6 class="m-0 font-weight-bold text-primary">Bulk Slot Creation</h6>
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

                    <form method="post" action="<?= base_url('recruiter/slots/store') ?>" id="slotForm">
                        <?= csrf_field() ?>

                        <!-- Job Selection -->
                        <div class="form-group">
                            <label for="job_id">Select Job <span class="text-danger">*</span></label>
                            <select name="job_id" id="job_id" class="form-control" required>
                                <option value="">-- Select Job --</option>
                                <?php foreach ($jobs as $job): ?>
                                    <option value="<?= $job['id'] ?>">
                                        <?= esc($job['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Select the job position for these interview slots</small>
                        </div>

                        <div class="row">
                            <!-- Start Date -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Start Date <span class="text-danger">*</span></label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" 
                                           min="<?= date('Y-m-d') ?>" required>
                                    <small class="form-text text-muted">First date for slots</small>
                                </div>
                            </div>

                            <!-- End Date -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">End Date (Optional)</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" 
                                           min="<?= date('Y-m-d') ?>">
                                    <small class="form-text text-muted">Leave empty for single day</small>
                                </div>
                            </div>
                        </div>

                        <!-- Time Slots -->
                        <div class="form-group">
                            <label>Time Slots <span class="text-danger">*</span></label>
                            <div id="timeSlots">
                                <div class="input-group mb-2">
                                    <input type="time" name="times[]" class="form-control" required>
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-success" id="addTime">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <small class="form-text text-muted">Add multiple time slots for each day</small>
                        </div>

                        <!-- Capacity -->
                        <div class="form-group">
                            <label for="capacity">Capacity per Slot <span class="text-danger">*</span></label>
                            <input type="number" name="capacity" id="capacity" class="form-control" 
                                   min="1" max="50" value="1" required>
                            <small class="form-text text-muted">Number of candidates that can book each slot</small>
                        </div>

                        <!-- Exclude Weekends -->
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="exclude_weekends" 
                                       name="exclude_weekends" value="1" checked>
                                <label class="custom-control-label" for="exclude_weekends">
                                    Exclude Weekends (Saturday & Sunday)
                                </label>
                            </div>
                        </div>

                        <!-- Summary Section -->
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Summary</h6>
                            <p class="mb-0" id="slotSummary">
                                Please fill in the form to see the summary of slots to be created.
                            </p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Create Slots
                            </button>
                            <a href="<?= base_url('recruiter/slots') ?>" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card shadow mt-4">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold"><i class="fas fa-question-circle"></i> How it works</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Select a job position for the interview slots</li>
                        <li>Choose start date (and optionally end date for multiple days)</li>
                        <li>Add one or more time slots for each day</li>
                        <li>Set the capacity (how many candidates can book each slot)</li>
                        <li>Optionally exclude weekends from the date range</li>
                        <li>The system will automatically create all combinations</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add more time slots
    document.getElementById('addTime').addEventListener('click', function() {
        const timeSlotsDiv = document.getElementById('timeSlots');
        const newTimeSlot = document.createElement('div');
        newTimeSlot.className = 'input-group mb-2';
        newTimeSlot.innerHTML = `
            <input type="time" name="times[]" class="form-control" required>
            <div class="input-group-append">
                <button type="button" class="btn btn-danger removeTime">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        `;
        timeSlotsDiv.appendChild(newTimeSlot);
        updateSummary();
    });

    // Remove time slot
    document.addEventListener('click', function(e) {
        if (e.target.closest('.removeTime')) {
            e.target.closest('.input-group').remove();
            updateSummary();
        }
    });

    // Update summary when form changes
    const formInputs = document.querySelectorAll('#slotForm input, #slotForm select');
    formInputs.forEach(input => {
        input.addEventListener('change', updateSummary);
    });

    function updateSummary() {
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        const timeSlots = document.querySelectorAll('input[name="times[]"]').length;
        const capacity = document.getElementById('capacity').value || 1;
        const excludeWeekends = document.getElementById('exclude_weekends').checked;

        if (!startDate) {
            document.getElementById('slotSummary').textContent = 
                'Please fill in the form to see the summary of slots to be created.';
            return;
        }

        // Calculate number of days
        let days = 1;
        if (endDate && endDate > startDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            days = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

            // Estimate weekend exclusion (rough calculation)
            if (excludeWeekends) {
                const weekends = Math.floor(days / 7) * 2;
                days -= weekends;
            }
        }

        const totalSlots = days * timeSlots;
        const totalCapacity = totalSlots * capacity;

        let summary = `You are about to create <strong>${totalSlots} slots</strong> `;
        summary += `(${days} day${days > 1 ? 's' : ''} × ${timeSlots} time${timeSlots > 1 ? 's' : ''}) `;
        summary += `with a total capacity of <strong>${totalCapacity} candidates</strong>.`;

        if (excludeWeekends) {
            summary += ' <em>(Weekends excluded)</em>';
        }

        document.getElementById('slotSummary').innerHTML = summary;
    }

    // Validate end date is after start date
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = this.value;
        document.getElementById('end_date').min = startDate;
    });

    // Form validation before submit
    document.getElementById('slotForm').addEventListener('submit', function(e) {
        const times = document.querySelectorAll('input[name="times[]"]');
        const timeValues = Array.from(times).map(t => t.value);
        const uniqueTimes = new Set(timeValues);

        if (uniqueTimes.size !== timeValues.length) {
            e.preventDefault();
            alert('Duplicate time slots detected. Please ensure all time slots are unique.');
            return false;
        }

        // Confirm if creating many slots
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        
        if (endDate && endDate > startDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            const days = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            const totalSlots = days * times.length;

            if (totalSlots > 50) {
                if (!confirm(`You are about to create ${totalSlots} slots. Continue?`)) {
                    e.preventDefault();
                    return false;
                }
            }
        }
    });
});
</script>

<style>
.card {
    border: none;
    border-radius: 0.5rem;
}

.card-header {
    border-bottom: 2px solid rgba(0,0,0,.125);
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df!important;
}

.form-control:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 0.2rem rgba(78,115,223,.25);
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
}

.input-group-append .btn {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

.alert-info {
    background-color: #d1ecf1;
    border-color: #bee5eb;
    color: #0c5460;
}
</style>

<?= view('layouts/recruiter_footer') ?>