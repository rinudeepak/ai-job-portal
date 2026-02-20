(function () {
    function getBaseUrl() {
        var meta = document.querySelector('meta[name="base-url"]');
        return meta ? meta.getAttribute('content').replace(/\/$/, '') : window.location.origin;
    }

    window.dismissAllSuggestions = function () {
        fetch(getBaseUrl() + '/career-transition/dismiss-suggestion', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function () {
            window.location.reload();
        });
    };

    window.confirmCareerReset = function (event, targetRole) {
        if (event) {
            event.preventDefault();
        }

        fetch(getBaseUrl() + '/career-transition')
            .then(function (response) { return response.text(); })
            .then(function (html) {
                if (html.includes('Change Career Path')) {
                    var shouldReset = window.confirm(
                        'You have an existing career path. Do you want to reset it and start a new one? Your current progress will be lost.'
                    );
                    if (!shouldReset) {
                        return;
                    }
                    if (targetRole) {
                        window.location.href = getBaseUrl() + '/career-transition?reset=1&target=' + targetRole;
                    } else {
                        window.location.href = getBaseUrl() + '/career-transition?reset=1';
                    }
                } else {
                    if (targetRole) {
                        window.location.href = getBaseUrl() + '/career-transition?target=' + targetRole;
                    } else {
                        window.location.href = getBaseUrl() + '/career-transition';
                    }
                }
            });
    };

    window.editExperience = function (exp) {
        var expId = document.getElementById('exp_id');
        if (!expId) {
            return;
        }
        expId.value = exp.id;
        document.querySelector('[name="job_title"]').value = exp.job_title;
        document.querySelector('[name="company_name"]').value = exp.company_name;
        document.querySelector('[name="employment_type"]').value = exp.employment_type;
        document.querySelector('[name="location"]').value = exp.location || '';
        document.querySelector('[name="start_date"]').value = exp.start_date;
        document.querySelector('[name="end_date"]').value = exp.end_date || '';
        document.getElementById('isCurrent').checked = exp.is_current == 1;
        document.querySelector('[name="description"]').value = exp.description || '';
        document.querySelector('#addExperienceModal .modal-title').textContent = 'Edit Work Experience';
        if (window.jQuery) {
            window.jQuery('#addExperienceModal').modal('show');
        }
    };

    window.editEducation = function (edu) {
        var eduId = document.getElementById('edu_id');
        if (!eduId) {
            return;
        }
        eduId.value = edu.id;
        document.querySelector('#educationForm [name="degree"]').value = edu.degree;
        document.querySelector('#educationForm [name="field_of_study"]').value = edu.field_of_study;
        document.querySelector('#educationForm [name="institution"]').value = edu.institution;
        document.querySelector('#educationForm [name="start_year"]').value = edu.start_year;
        document.querySelector('#educationForm [name="end_year"]').value = edu.end_year;
        document.querySelector('#educationForm [name="grade"]').value = edu.grade || '';
        document.querySelector('#addEducationModal .modal-title').textContent = 'Edit Education';
        if (window.jQuery) {
            window.jQuery('#addEducationModal').modal('show');
        }
    };

    window.editCertification = function (cert) {
        var certId = document.getElementById('cert_id');
        if (!certId) {
            return;
        }
        certId.value = cert.id;
        document.querySelector('#certificationForm [name="certification_name"]').value = cert.certification_name;
        document.querySelector('#certificationForm [name="issuing_organization"]').value = cert.issuing_organization;
        document.querySelector('#certificationForm [name="issue_date"]').value = cert.issue_date;
        document.querySelector('#certificationForm [name="expiry_date"]').value = cert.expiry_date || '';
        document.querySelector('#certificationForm [name="credential_id"]').value = cert.credential_id || '';
        document.querySelector('#certificationForm [name="credential_url"]').value = cert.credential_url || '';
        document.querySelector('#addCertificationModal .modal-title').textContent = 'Edit Certification';
        if (window.jQuery) {
            window.jQuery('#addCertificationModal').modal('show');
        }
    };

    window.quickAddInterest = function (value) {
        var input = document.getElementById('quickInterestValue');
        var form = document.getElementById('quickInterestForm');
        if (!input || !form) {
            return;
        }
        input.value = value;
        form.submit();
    };

    window.previewResume = function () {
        fetch(getBaseUrl() + '/candidate/preview-resume')
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(function (data) {
                if (data.error) {
                    window.alert(data.error);
                } else if (data.url) {
                    window.open(data.url, '_blank');
                } else {
                    window.alert('Error previewing resume');
                }
            })
            .catch(function (error) {
                window.alert('Error previewing resume: ' + error.message);
            });
    };

    window.shareProfile = function () {
        var profileUrl = window.location.href;
        var userNameMeta = document.querySelector('meta[name="user-name"]');
        var userName = userNameMeta ? userNameMeta.getAttribute('content') : 'User';

        if (navigator.share) {
            navigator.share({
                title: 'My Profile - ' + userName,
                text: 'Check out my professional profile',
                url: profileUrl
            });
            return;
        }

        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(profileUrl)
                .then(function () {
                    window.alert('Profile link copied to clipboard!');
                })
                .catch(function () {
                    var textArea = document.createElement('textarea');
                    textArea.value = profileUrl;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    window.alert('Profile link copied to clipboard!');
                });
            return;
        }

        var fallbackArea = document.createElement('textarea');
        fallbackArea.value = profileUrl;
        document.body.appendChild(fallbackArea);
        fallbackArea.select();
        document.execCommand('copy');
        document.body.removeChild(fallbackArea);
        window.alert('Profile link copied to clipboard!');
    };

    if (window.jQuery) {
        window.jQuery(function () {
            window.jQuery('#addExperienceModal').on('hidden.bs.modal', function () {
                var form = document.getElementById('workExpForm');
                if (!form) {
                    return;
                }
                form.reset();
                document.getElementById('exp_id').value = '';
                document.querySelector('#addExperienceModal .modal-title').textContent = 'Add Work Experience';
            });

            window.jQuery('#addEducationModal').on('hidden.bs.modal', function () {
                var form = document.getElementById('educationForm');
                if (!form) {
                    return;
                }
                form.reset();
                document.getElementById('edu_id').value = '';
                document.querySelector('#addEducationModal .modal-title').textContent = 'Add Education';
            });

            window.jQuery('#addCertificationModal').on('hidden.bs.modal', function () {
                var form = document.getElementById('certificationForm');
                if (!form) {
                    return;
                }
                form.reset();
                document.getElementById('cert_id').value = '';
                document.querySelector('#addCertificationModal .modal-title').textContent = 'Add Certification';
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var transitionForm = document.getElementById('transitionForm');
        if (transitionForm) {
            transitionForm.addEventListener('submit', function () {
                var btnText = document.getElementById('btnText');
                var btnLoading = document.getElementById('btnLoading');
                var submitBtn = document.getElementById('submitBtn');
                if (btnText) {
                    btnText.style.display = 'none';
                }
                if (btnLoading) {
                    btnLoading.style.display = 'inline-block';
                }
                if (submitBtn) {
                    submitBtn.disabled = true;
                }
            });
        }

        var autoRefreshBlock = document.querySelector('[data-auto-refresh="1"]');
        if (autoRefreshBlock) {
            var delay = parseInt(autoRefreshBlock.getAttribute('data-refresh-delay') || '5000', 10);
            window.setTimeout(function () {
                window.location.reload();
            }, delay);
        }

        var offlineStatus = document.getElementById('offlineStatus');
        if (offlineStatus) {
            var updateOfflineStatus = function () {
                if (navigator.onLine) {
                    offlineStatus.textContent = 'Online';
                    offlineStatus.className = 'offline-badge online';
                } else {
                    offlineStatus.textContent = 'Offline';
                    offlineStatus.className = 'offline-badge offline';
                }
            };
            window.addEventListener('online', updateOfflineStatus);
            window.addEventListener('offline', updateOfflineStatus);
            updateOfflineStatus();
        }

        var coursePdfBtn = document.getElementById('coursePdfBtn');
        if (coursePdfBtn) {
            coursePdfBtn.addEventListener('click', function () {
                var downloadUrl = coursePdfBtn.getAttribute('data-download-url');
                var btnText = document.getElementById('coursePdfBtnText');
                var btnLoading = document.getElementById('coursePdfBtnLoading');
                if (btnText) {
                    btnText.style.display = 'none';
                }
                if (btnLoading) {
                    btnLoading.style.display = 'inline';
                }

                if (downloadUrl) {
                    window.location.href = downloadUrl;
                }

                window.setTimeout(function () {
                    if (btnText) {
                        btnText.style.display = 'inline';
                    }
                    if (btnLoading) {
                        btnLoading.style.display = 'none';
                    }
                }, 3000);
            });
        }
    });

    window.completeTask = function (taskId) {
        fetch(getBaseUrl() + '/career-transition/complete/' + taskId, {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (data && data.success) {
                    window.location.reload();
                } else {
                    window.alert('Failed to mark task as complete. Please try again.');
                }
            })
            .catch(function () {
                window.alert('Failed to mark task as complete. Please try again.');
            });
    };
})();
