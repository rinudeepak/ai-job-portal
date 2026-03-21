(function () {
    function getBaseUrl() {
        var meta = document.querySelector('meta[name="base-url"]');
        return meta ? meta.getAttribute('content').replace(/\/$/, '') : window.location.origin;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var menu = document.getElementById('recruiterAvatarMenu');
        var button = document.getElementById('recruiterAvatarBtn');
        var dropdown = document.getElementById('recruiterAvatarDropdown');

        if (menu && button && dropdown) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                event.stopPropagation();
                dropdown.classList.toggle('is-open');
                button.setAttribute('aria-expanded', dropdown.classList.contains('is-open') ? 'true' : 'false');
            });

            document.addEventListener('click', function (event) {
                if (!menu.contains(event.target)) {
                    dropdown.classList.remove('is-open');
                    button.setAttribute('aria-expanded', 'false');
                }
            });
        }

        var policySelect = document.getElementById('ai_interview_policy');
        var cutoffWrap = document.getElementById('minAiCutoffWrap');
        var cutoffInput = document.getElementById('min_ai_cutoff_score');

        if (policySelect && cutoffWrap && cutoffInput) {
            var toggleCutoffField = function () {
                var isAiOff = policySelect.value === 'OFF';
                cutoffWrap.style.display = isAiOff ? 'none' : '';
                cutoffInput.disabled = isAiOff;
                cutoffInput.required = !isAiOff;
                if (isAiOff) {
                    cutoffInput.value = '';
                }
            };

            policySelect.addEventListener('change', toggleCutoffField);
            toggleCutoffField();
        }

        var slotForm = document.getElementById('slotForm');
        var addTimeBtn = document.getElementById('addTime');
        var timeSlots = document.getElementById('timeSlots');
        var summary = document.getElementById('slotSummary');

        if (addTimeBtn && timeSlots) {
            addTimeBtn.addEventListener('click', function () {
                var row = document.createElement('div');
                row.className = 'input-group mb-2';
                row.innerHTML = '\n                    <input type="time" name="times[]" class="form-control" required>\n                    <div class="input-group-append">\n                        <button type="button" class="btn btn-outline-danger remove-time">\n                            <i class="fas fa-minus"></i>\n                        </button>\n                    </div>\n                ';
                timeSlots.appendChild(row);
            });
        }

        if (timeSlots) {
            timeSlots.addEventListener('click', function (event) {
                var btn = event.target.closest('.remove-time');
                if (!btn) {
                    return;
                }

                var row = btn.closest('.input-group');
                if (row) {
                    row.remove();
                }
            });
        }

        if (slotForm && summary) {
            var updateSummary = function () {
                var job = document.getElementById('job_id');
                var start = document.getElementById('start_date');
                var end = document.getElementById('end_date');
                var capacity = document.getElementById('capacity');
                var times = Array.from(document.querySelectorAll('input[name="times[]"]')).map(function (input) {
                    return input.value.trim();
                }).filter(Boolean);
                var weekends = document.getElementById('exclude_weekends');

                summary.textContent = [
                    job && job.selectedOptions.length ? 'Job: ' + job.selectedOptions[0].textContent.trim() : 'Job not selected',
                    start && start.value ? 'Start: ' + start.value : 'Start date pending',
                    end && end.value ? 'End: ' + end.value : 'Single day or open range',
                    capacity && capacity.value ? 'Capacity: ' + capacity.value : 'Capacity pending',
                    times.length ? times.length + ' time slot(s)' : 'No times added yet',
                    weekends && weekends.checked ? 'Weekends excluded' : 'Weekends included'
                ].join(' • ');
            };

            ['change', 'input'].forEach(function (eventName) {
                slotForm.addEventListener(eventName, updateSummary, true);
            });
            updateSummary();
        }

        var selectAll = document.getElementById('selectAllApplications');
        var checkboxes = Array.prototype.slice.call(document.querySelectorAll('.application-checkbox'));
        var bulkForm = document.getElementById('bulkActionForm');
        var bulkAction = document.getElementById('bulkActionSelect');
        var bulkMessage = document.getElementById('bulkMessageInput');

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(function (cb) {
                    cb.checked = selectAll.checked;
                });
            });
        }

        checkboxes.forEach(function (cb) {
            cb.addEventListener('change', function () {
                if (!selectAll) {
                    return;
                }
                var allChecked = checkboxes.length > 0 && checkboxes.every(function (item) {
                    return item.checked;
                });
                selectAll.checked = allChecked;
            });
        });

        if (bulkForm) {
            bulkForm.addEventListener('submit', function (event) {
                var selected = checkboxes.filter(function (cb) {
                    return cb.checked;
                });
                if (!bulkAction || !bulkAction.value) {
                    event.preventDefault();
                    window.alert('Please choose a bulk action.');
                    return;
                }
                if (selected.length === 0) {
                    event.preventDefault();
                    window.alert('Please select at least one candidate.');
                    return;
                }
                if (bulkAction.value === 'message' && (!bulkMessage || bulkMessage.value.trim() === '')) {
                    event.preventDefault();
                    window.alert('Please enter a message for selected candidates.');
                    return;
                }

                bulkForm.querySelectorAll('input[name="application_ids[]"]').forEach(function (input) {
                    input.remove();
                });

                selected.forEach(function (cb) {
                    var hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'application_ids[]';
                    hidden.value = cb.value;
                    bulkForm.appendChild(hidden);
                });
            });
        }
    });
})();
