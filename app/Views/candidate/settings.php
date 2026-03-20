<?= view('Layouts/candidate_header', ['title' => 'Settings']) ?>

<?php $activeTab = (string) (service('request')->getGet('tab') ?? 'visibility'); ?>

<div class="settings-jobboard">
    <style>
        .settings-jobboard .page-board-header {
            align-items: flex-start;
            padding-top: 1rem;
        }
        .settings-jobboard .company-profile-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 0.75rem;
        }
        .settings-jobboard .company-profile-actions .btn {
            border-radius: 999px;
        }
        .settings-summary-card {
            margin-top: 1rem;
            border: 1px solid #e6ebf2;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06);
        }
        .settings-summary-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 1rem 1.25rem;
            align-items: center;
        }
        .settings-summary-copy {
            min-width: 0;
        }
        .settings-summary-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            color: #3b82f6;
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 0.35rem;
        }
        .settings-summary-title {
            margin: 0;
            font-size: 1.12rem;
            font-weight: 800;
            color: #0f172a;
        }
        .settings-summary-note {
            margin: 0.35rem 0 0;
            color: #64748b;
        }
        .settings-summary-progress {
            min-width: 260px;
        }
        .settings-summary-progress .progress {
            height: 10px;
            border-radius: 999px;
            background: #edf2f7;
        }
        .settings-summary-progress .progress-bar {
            border-radius: 999px;
        }
        .settings-summary-metrics {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 0.55rem;
        }
        .settings-summary-metrics .meta-chip {
            border-radius: 999px;
        }
        .settings-shell {
            display: grid;
            grid-template-columns: 264px minmax(0, 1fr);
            gap: 24px;
            align-items: start;
            margin-top: 22px;
        }
        .settings-flash-wrap {
            margin-top: 22px;
        }
        .settings-side,
        .settings-content {
            background: #fff;
            border: 1px solid #e6ebf2;
            border-radius: 18px;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06);
        }
        .settings-side {
            padding: 16px;
        }
        .settings-side-title {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 10px;
        }
        .settings-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .settings-nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border-radius: 16px;
            text-decoration: none;
            color: #1f2937;
            border: 1px solid #e8edf5;
            background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.03);
            transition: all .2s ease;
        }
        .settings-nav-link:hover {
            text-decoration: none;
            color: #0f172a;
            border-color: #cfd8e3;
            background: #f4f8fc;
            transform: translateY(-1px);
        }
        .settings-nav-link.is-active {
            background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%);
            color: #fff;
            border-color: #2563eb;
            box-shadow: 0 14px 26px rgba(37, 99, 235, 0.18);
        }
        .settings-nav-link small {
            display: block;
            font-size: 11px;
            opacity: .75;
        }
        .settings-nav-link span:first-child {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(37, 99, 235, 0.08);
            color: #2563eb;
            flex-shrink: 0;
        }
        .settings-nav-link.is-active span:first-child {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
        }
        .settings-content {
            padding: 20px;
        }
        .settings-panel {
            display: none;
        }
        .settings-panel.is-active {
            display: block;
        }
        .settings-panel-title {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .settings-panel-copy {
            color: #64748b;
            margin-bottom: 18px;
            max-width: 760px;
        }
        .settings-card {
            border: 1px solid #eef2f7;
            border-radius: 18px;
            padding: 16px;
            background: #fbfdff;
            margin-bottom: 14px;
        }
        .settings-card:last-child {
            margin-bottom: 0;
        }
        .settings-card h6 {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 6px;
        }
        .settings-card p {
            color: #64748b;
            margin-bottom: 0;
        }
        .switch-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 13px 0;
        }
        .switch-row + .switch-row {
            border-top: 1px solid #eef2f7;
        }
        .switch-copy {
            flex: 1;
        }
        .switch-copy label {
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .switch-copy small {
            color: #64748b;
            line-height: 1.45;
        }
        .switch-toggle {
            position: relative;
            width: 42px;
            height: 24px;
            margin: 0;
            flex-shrink: 0;
        }
        .switch-toggle input {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }
        .switch-toggle-slider {
            position: absolute;
            inset: 0;
            cursor: pointer;
            background: #c7ced8;
            border-radius: 999px;
            transition: background .2s ease;
        }
        .switch-toggle-slider::before {
            content: '';
            position: absolute;
            height: 18px;
            width: 18px;
            left: 3px;
            top: 3px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 1px 4px rgba(15, 23, 42, .25);
            transition: transform .2s ease;
        }
        .switch-toggle input:checked + .switch-toggle-slider {
            background: #89ba16;
        }
        .switch-toggle input:checked + .switch-toggle-slider::before {
            transform: translateX(18px);
        }
        .settings-actions {
            margin-top: 16px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .settings-content .btn {
            border-radius: 999px;
        }
        .settings-content .form-control,
        .settings-content .form-select {
            min-height: 44px;
            border-radius: 12px;
        }
        .settings-content .form-control {
            background: #fff;
        }
        .settings-content .form-check-input {
            width: 1.1rem;
            height: 1.1rem;
        }
        @media (max-width: 991.98px) {
            .settings-summary-grid,
            .settings-shell {
                grid-template-columns: 1fr;
                margin-top: 18px;
            }
            .settings-jobboard .company-profile-actions,
            .settings-summary-metrics {
                justify-content: flex-start;
            }
            .settings-summary-progress {
                min-width: 0;
            }
            .settings-shell {
                gap: 16px;
            }
        }
    </style>

    <section class="site-section pt-0 content-wrap">
        <div class="container">
            <div class="page-board-header page-board-header-tight">
                <div class="page-board-copy">
                    <span class="page-board-kicker"><i class="fas fa-cog"></i> Account settings</span>
                    <h1 class="page-board-title">Settings</h1>
                    <p class="page-board-subtitle">Control profile visibility, notifications, and account security from one compact panel.</p>
                    <div class="company-profile-meta">
                        <span class="meta-chip"><strong><?= (int) (($user['allow_public_recruiter_visibility'] ?? 1) === 1 ? 1 : 0) ?></strong> Recruiter visibility</span>
                        <span class="meta-chip"><strong><?= (int) (($user['job_alerts_enabled'] ?? 1) === 1 ? 1 : 0) ?></strong> Job alerts</span>
                        <span class="meta-chip"><strong>Secure</strong> Account access</span>
                    </div>
                </div>
                <div class="company-profile-actions">
                    <a href="<?= base_url('candidate/profile') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-user mr-1"></i> My Profile
                    </a>
                    <a href="<?= base_url('account/change-password') ?>" class="btn btn-primary">
                        <i class="fas fa-lock mr-1"></i> Change Password
                    </a>
                </div>
            </div>

            <div class="card settings-summary-card">
                <div class="card-body">
                    <div class="settings-summary-grid">
                        <div class="settings-summary-copy">
                            <div class="settings-summary-kicker"><i class="fas fa-sliders-h"></i> Quick snapshot</div>
                            <h2 class="settings-summary-title">Keep your job search settings aligned with your profile</h2>
                            <p class="settings-summary-note">Update visibility and alerts here, then continue editing the rest of your profile in one place.</p>
                            <div class="settings-summary-progress mt-3">
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: <?= (int) ($user['allow_public_recruiter_visibility'] ?? 1) === 1 ? '100%' : '35%' ?>" aria-valuenow="<?= (int) ($user['allow_public_recruiter_visibility'] ?? 1) === 1 ? 100 : 35 ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                        <div class="settings-summary-metrics">
                            <span class="meta-chip"><strong><?= (int) (($user['allow_public_recruiter_visibility'] ?? 1) === 1 ? 1 : 0) ?></strong> Visible</span>
                            <span class="meta-chip"><strong><?= (int) (($user['job_alerts_enabled'] ?? 1) === 1 ? 1 : 0) ?></strong> Alerts</span>
                            <span class="meta-chip"><strong><?= (int) (($user['job_alert_notify_in_app'] ?? 1) === 1 ? 1 : 0) ?></strong> In-app</span>
                            <span class="meta-chip"><strong><?= (int) (($user['job_alert_notify_email'] ?? 1) === 1 ? 1 : 0) ?></strong> Email</span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (session()->getFlashdata('settings_success') || session()->getFlashdata('error')): ?>
                <div class="settings-flash-wrap">
                    <?php if (session()->getFlashdata('settings_success')): ?>
                        <div class="alert alert-success mb-0"><?= esc(session()->getFlashdata('settings_success')) ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger mb-0"><?= esc(session()->getFlashdata('error')) ?></div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="settings-shell">
                <aside class="settings-side">
                    <div class="settings-side-title">Account Settings</div>
                    <nav class="settings-nav" id="settingsNav">
                        <a href="#visibility" class="settings-nav-link <?= $activeTab === 'visibility' ? 'is-active' : '' ?>" data-settings-tab="visibility">
                            <span><i class="fas fa-user-shield"></i></span>
                            <span>
                                Profile Visibility
                                <small>Recruiter access</small>
                            </span>
                        </a>
                        <a href="#notifications" class="settings-nav-link <?= $activeTab === 'notifications' ? 'is-active' : '' ?>" data-settings-tab="notifications">
                            <span><i class="fas fa-bell"></i></span>
                            <span>
                                Notifications
                                <small>Job alerts and channels</small>
                            </span>
                        </a>
                        <a href="#account" class="settings-nav-link <?= $activeTab === 'account' ? 'is-active' : '' ?>" data-settings-tab="account">
                            <span><i class="fas fa-lock"></i></span>
                            <span>
                                Account
                                <small>Password and security</small>
                            </span>
                        </a>
                    </nav>
                </aside>

                <div class="settings-content">
                    <section class="settings-panel <?= $activeTab === 'visibility' ? 'is-active' : '' ?>" data-settings-panel="visibility">
                        <div class="settings-panel-title">Profile Visibility</div>
                        <div class="settings-panel-copy">Control whether recruiters can discover your profile outside the jobs you already applied for.</div>

                        <div class="settings-card">
                            <form method="post" action="<?= base_url('candidate/update-settings') ?>" id="visibilitySettingsForm">
                                <?= csrf_field() ?>
                                <div class="switch-row">
                                    <div class="switch-copy">
                                        <label for="allow_public_recruiter_visibility">Visible To Recruiters</label>
                                        <small>When off, recruiters can access your profile only after you apply to one of their jobs.</small>
                                    </div>
                                    <input type="hidden" name="allow_public_recruiter_visibility" value="0">
                                    <label class="switch-toggle" for="allow_public_recruiter_visibility">
                                        <input type="checkbox" name="allow_public_recruiter_visibility" id="allow_public_recruiter_visibility" value="1" <?= (int) ($user['allow_public_recruiter_visibility'] ?? 1) === 1 ? 'checked' : '' ?>>
                                        <span class="switch-toggle-slider"></span>
                                    </label>
                                </div>
                            </form>
                        </div>
                    </section>

                    <section class="settings-panel <?= $activeTab === 'notifications' ? 'is-active' : '' ?>" data-settings-panel="notifications">
                        <div class="settings-panel-title">Notifications</div>
                        <div class="settings-panel-copy">Manage job alert activation and choose where alert updates should reach you.</div>

                        <div class="settings-card">
                            <form method="post" action="<?= base_url('candidate/update-notification-settings') ?>" id="notificationSettingsForm">
                                <?= csrf_field() ?>
                                <div class="switch-row">
                                    <div class="switch-copy">
                                        <label for="job_alerts_enabled">Job Alerts</label>
                                        <small>Turn profile-based job alerts on or off without changing the preferences saved in your profile.</small>
                                    </div>
                                    <input type="hidden" name="job_alerts_enabled" value="0">
                                    <label class="switch-toggle" for="job_alerts_enabled">
                                        <input type="checkbox" name="job_alerts_enabled" id="job_alerts_enabled" value="1" <?= (int) ($user['job_alerts_enabled'] ?? 1) === 1 ? 'checked' : '' ?>>
                                        <span class="switch-toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="switch-row">
                                    <div class="switch-copy">
                                        <label for="job_alert_notify_in_app">In-App Notifications</label>
                                        <small>Show matching job updates inside the portal.</small>
                                    </div>
                                    <input type="hidden" name="job_alert_notify_in_app" value="0">
                                    <label class="switch-toggle" for="job_alert_notify_in_app">
                                        <input type="checkbox" name="job_alert_notify_in_app" id="job_alert_notify_in_app" value="1" <?= (int) ($user['job_alert_notify_in_app'] ?? 1) === 1 ? 'checked' : '' ?>>
                                        <span class="switch-toggle-slider"></span>
                                    </label>
                                </div>
                                <div class="switch-row">
                                    <div class="switch-copy">
                                        <label for="job_alert_notify_email">Email Notifications</label>
                                        <small>Send matching jobs to your registered email address.</small>
                                    </div>
                                    <input type="hidden" name="job_alert_notify_email" value="0">
                                    <label class="switch-toggle" for="job_alert_notify_email">
                                        <input type="checkbox" name="job_alert_notify_email" id="job_alert_notify_email" value="1" <?= (int) ($user['job_alert_notify_email'] ?? 1) === 1 ? 'checked' : '' ?>>
                                        <span class="switch-toggle-slider"></span>
                                    </label>
                                </div>
                                <p class="mb-0 text-muted small">Job alert criteria are now taken automatically from the Preferences section in your profile.</p>
                            </form>
                        </div>
                    </section>

                    <section class="settings-panel <?= $activeTab === 'account' ? 'is-active' : '' ?>" data-settings-panel="account">
                        <div class="settings-panel-title">Account Security</div>
                        <div class="settings-panel-copy">Use the secure password change page to update your account credentials.</div>

                        <div class="settings-card">
                            <h6>Change Password</h6>
                            <p>Your password is managed on a dedicated secure page. Open it when you want to update your credentials.</p>
                            <div class="settings-actions">
                                <a href="<?= base_url('account/change-password') ?>" class="btn btn-outline-primary">Open Change Password</a>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var navLinks = document.querySelectorAll('[data-settings-tab]');
    var panels = document.querySelectorAll('[data-settings-panel]');

    function activateTab(tabName) {
        navLinks.forEach(function (link) {
            link.classList.toggle('is-active', link.getAttribute('data-settings-tab') === tabName);
        });

        panels.forEach(function (panel) {
            panel.classList.toggle('is-active', panel.getAttribute('data-settings-panel') === tabName);
        });
    }

    navLinks.forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();
            var tabName = link.getAttribute('data-settings-tab') || 'visibility';
            activateTab(tabName);
            if (window.history && window.history.replaceState) {
                var url = new URL(window.location.href);
                url.searchParams.set('tab', tabName);
                window.history.replaceState({}, '', url.toString());
            }
        });
    });

    var notificationForm = document.getElementById('notificationSettingsForm');
    if (notificationForm) {
        var toggleInputs = notificationForm.querySelectorAll('input[type="checkbox"]');
        toggleInputs.forEach(function (input) {
            input.addEventListener('change', function () {
                notificationForm.submit();
            });
        });
    }

    var visibilityForm = document.getElementById('visibilitySettingsForm');
    if (visibilityForm) {
        var visibilityToggle = visibilityForm.querySelector('input[type="checkbox"]');
        if (visibilityToggle) {
            visibilityToggle.addEventListener('change', function () {
                visibilityForm.submit();
            });
        }
    }
});
</script>
