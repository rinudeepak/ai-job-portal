// ---------------------chat-------------------------
// Auto-scroll to bottom
document.addEventListener('DOMContentLoaded', function () {
    const chatContainer = document.getElementById('chatContainer');
    if (chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }


    // Character counter
    const textarea = document.getElementById('answer');
    const charCount = document.getElementById('charCount');

    if (textarea && charCount) {
        textarea.addEventListener('input', function () {
            charCount.textContent = this.value.length + ' characters';
        });
    }


    // Prevent accidental page leave
    const answerBox = document.getElementById('answer');
    if (answerBox) {
        window.addEventListener('beforeunload', function (e) {
            e.preventDefault();
            e.returnValue = '';
        });
    }

});
// ---------------------leaderboard-------------------------
document.addEventListener('DOMContentLoaded', function () {
    // Auto-submit form on select change
    const selects = document.querySelectorAll('#filterForm select');
    selects.forEach(select => {
        select.addEventListener('change', function () {
            // Optional: Auto-submit on change
            // document.getElementById('filterForm').submit();
        });
    });

    // Animate score bars on load
    const scoreBars = document.querySelectorAll('.score-fill');
    scoreBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
});
// ---------------------create slots-------------------------
document.addEventListener('DOMContentLoaded', function () {
    // Add more time slots
    const addTimeBtn = document.getElementById('addTime');
    if (addTimeBtn) {
        addTimeBtn.addEventListener('click', function () {

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
    }

    // Remove time slot
    document.addEventListener('click', function (e) {
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
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', function () {
            endDateInput.min = this.value;
        });
    }


    // Form validation before submit

    const slotForm = document.getElementById('slotForm');
    if (slotForm) {
        slotForm.addEventListener('submit', function (e) {

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
    }
});


// ---------------------edit slots-------------------------
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('editSlotForm');

    if (form) {
        form.addEventListener('submit', function (e) {
            const slotDate = document.getElementById('slot_date').value;
            const slotTime = document.getElementById('slot_time').value;
            const slotDateTime = new Date(slotDate + ' ' + slotTime);
            const now = new Date();

            if (slotDateTime < now) {
                e.preventDefault();
                alert('Cannot set slot date/time in the past!');
                return false;
            }

            if (!confirm('Are you sure you want to update this slot?')) {
                e.preventDefault();
                return false;
            }
        });
    }
});

// ---------------------reschedule-------------------------

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('rescheduleForm');

    if (form) {
        form.addEventListener('submit', function (e) {
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
            radio.addEventListener('change', function () {
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
// ----------------------Profile Page JavaScript Functions----------------------------------------

// Tab functionality for older Bootstrap versions
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('#profileTabs button');
    const tabContents = document.querySelectorAll('.tab-pane');
    
    if (tabs.length > 0) {
        tabs.forEach(tab => {
            tab.addEventListener('click', function() {
                // Remove active class from all tabs and contents
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(content => {
                    content.classList.remove('show', 'active');
                });
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Show corresponding content
                const target = this.getAttribute('data-bs-target');
                const targetContent = document.querySelector(target);
                if (targetContent) {
                    targetContent.classList.add('show', 'active');
                }
            });
        });
    }
});

// Resume preview function
function previewResume() {
    const baseUrl = document.querySelector('meta[name="base-url"]')?.getAttribute('content') || window.location.origin;
    
    fetch(baseUrl + '/candidate/preview-resume')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else if (data.url) {
                window.open(data.url, '_blank');
            } else {
                alert('Error previewing resume');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error previewing resume: ' + error.message);
        });
}

// Share profile function
function shareProfile() {
    const profileUrl = window.location.href;
    const userName = document.querySelector('meta[name="user-name"]')?.getAttribute('content') || 'User';
    
    if (navigator.share) {
        navigator.share({
            title: 'My Profile - ' + userName,
            text: 'Check out my professional profile',
            url: profileUrl
        });
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(profileUrl).then(() => {
            alert('Profile link copied to clipboard!');
        }).catch(() => {
            // Manual copy fallback
            const textArea = document.createElement('textarea');
            textArea.value = profileUrl;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('Profile link copied to clipboard!');
        });
    }
}