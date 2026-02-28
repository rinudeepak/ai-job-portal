<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="base-url" content="<?= base_url() ?>">
    <title><?= esc($title ?? 'Candidate Portal') ?></title>

    <link rel="stylesheet" href="<?= base_url('jobboard/css/custom-bs.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/jquery.fancybox.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/bootstrap-select.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/fonts/icomoon/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/fonts/line-icons/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/owl.carousel.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/animate.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/candidate-pages.css?v=' . @filemtime(FCPATH . 'jobboard/css/candidate-pages.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/fontawesome-all.min.css') ?>">
</head>
<body id="top">
<div id="overlayer"></div>
<div class="loader">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<div class="site-wrap">
    <?php
    $candidateId = (int) (session()->get('user_id') ?? 0);
    $candidateName = (string) (session()->get('user_name') ?? 'User');
    $candidateInitial = strtoupper(substr(trim($candidateName), 0, 1) ?: 'U');
    $request = service('request');
    $activeTab = (string) ($request->getGet('tab') ?? '');
    $headerSearch = (string) ($request->getGet('search') ?? '');
    $headerDesignation = (string) ($request->getGet('designation') ?? '');
    $headerCompany = (string) ($request->getGet('company') ?? '');
    $headerLocation = (string) ($request->getGet('location') ?? '');
    $headerExperienceRaw = $request->getGet('experience_level');
    $headerExperience = is_array($headerExperienceRaw)
        ? implode(', ', array_filter(array_map('strval', $headerExperienceRaw)))
        : (string) ($headerExperienceRaw ?? '');
    $currentPath = '/' . trim((string) parse_url(current_url(), PHP_URL_PATH), '/');
    $pathEndsWith = static function (string $suffix) use ($currentPath): bool {
        return $currentPath === $suffix || str_ends_with($currentPath, $suffix);
    };
    $candidatePhoto = (string) ($user['profile_photo'] ?? session()->get('profile_photo') ?? '');
    $unreadNotificationCount = $candidateId > 0
        ? (int) model('NotificationModel')->getUnreadCount($candidateId)
        : 0;

    $isHomeActive = $pathEndsWith('/candidate') || $pathEndsWith('/candidate/dashboard');
    $isJobDetailsActive = str_contains($currentPath, '/job/');
    $isJobsListActive = $pathEndsWith('/jobs');
    $isRecommendedActive = $isJobsListActive && $activeTab === 'suggested';
    $isApplicationStatusActive = $pathEndsWith('/candidate/applications');
    $isSavedJobsActive = $pathEndsWith('/candidate/saved-jobs');
    $isJobAlertsActive = $pathEndsWith('/candidate/job-alerts');
    $isJobsRoot = $isJobsListActive || $isSavedJobsActive || $isJobDetailsActive;
    $isJobsActive = $isJobsRoot || $isApplicationStatusActive || $isJobAlertsActive;
    $isCareerTransitionActive = str_contains($currentPath, '/career-transition');

    $homeNavClass = $isHomeActive ? 'nav-link active' : 'nav-link';
    $jobsNavClass = $isJobsActive ? 'nav-link active' : 'nav-link';
    $careerNavClass = $isCareerTransitionActive ? 'nav-link active' : 'nav-link';
    $recommendedClass = $isRecommendedActive ? 'active' : '';
    $applicationStatusClass = $isApplicationStatusActive ? 'active' : '';
    $savedJobsClass = $isSavedJobsActive ? 'active' : '';
    $jobAlertsClass = $isJobAlertsActive ? 'active' : '';

    if ($candidatePhoto === '' && $candidateId > 0) {
        $candidateRecord = model('UserModel')->select('profile_photo')->find($candidateId);
        $candidatePhoto = (string) ($candidateRecord['profile_photo'] ?? '');
    }
    ?>
    <div class="site-mobile-menu site-navbar-target">
        <div class="site-mobile-menu-header">
            <div class="site-mobile-menu-close mt-3">
                <span class="icon-close2 js-menu-toggle"></span>
            </div>
        </div>
        <div class="site-mobile-menu-body"></div>
    </div>

    <header class="site-navbar mt-3 site-navbar-target">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="site-logo col-6 col-xl-2">
                    <a href="<?= base_url('candidate/dashboard') ?>" class="d-inline-flex align-items-center">
                        <img src="<?= base_url('jobboard/images/Serp Hwak Logo.png') ?>" alt="HireMatrix Logo" style="height: 34px; width: auto; margin-right: 8px;">
                        <span style="text-transform: none;">HireMatrix</span>
                    </a>
                </div>
                <nav class="mx-auto site-navigation col-xl-7">
                    <ul class="site-menu js-clone-nav d-none d-xl-block ml-0 pl-0">
                        <li><a href="<?= base_url('candidate/dashboard') ?>" class="<?= $homeNavClass ?>">Home</a></li>
                        <li class="has-children">
                            <a href="<?= base_url('jobs') ?>" class="<?= $jobsNavClass ?>">Jobs</a>
                            <ul class="dropdown">
                                <li><a href="<?= base_url('jobs?tab=suggested') ?>" class="<?= $recommendedClass ?>">Recommended Jobs</a></li>
                                <li><a href="<?= base_url('candidate/applications') ?>" class="<?= $applicationStatusClass ?>">Application Status</a></li>
                                <li><a href="<?= base_url('candidate/saved-jobs') ?>" class="<?= $savedJobsClass ?>">Saved Jobs</a></li>
                                <li><a href="<?= base_url('candidate/job-alerts') ?>" class="<?= $jobAlertsClass ?>">Job Alerts</a></li>
                            </ul>
                        </li>
                        <li><a href="<?= base_url('career-transition') ?>" class="<?= $careerNavClass ?>">Career Transition AI</a></li>
                    </ul>
                </nav>
                <div class="right-cta-menu text-right d-flex justify-content-end align-items-center col-6 col-xl-3">
                    <style>
                        .candidate-avatar-menu {
                            position: relative;
                        }
                        .header-notification-link {
                            position: relative;
                            width: 42px;
                            height: 42px;
                            border-radius: 50%;
                            border: 1px solid rgba(0, 0, 0, 0.15);
                            background: #fff;
                            color: #111827;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            margin-right: 10px;
                            text-decoration: none;
                            transition: all .2s ease;
                        }
                        .header-notification-link:hover {
                            background: #f3f6ff;
                            border-color: #b8c6ea;
                            color: #1f4bb8;
                        }
                        .header-notification-badge {
                            position: absolute;
                            top: -5px;
                            right: -5px;
                            min-width: 18px;
                            height: 18px;
                            border-radius: 999px;
                            background: #dc3545;
                            color: #fff;
                            font-size: 11px;
                            font-weight: 700;
                            line-height: 18px;
                            text-align: center;
                            padding: 0 4px;
                        }
                        .header-notification-link.has-unread i {
                            animation: bell-ring 1.6s ease-in-out infinite;
                            transform-origin: top center;
                        }
                        @keyframes bell-ring {
                            0%, 60%, 100% { transform: rotate(0deg); }
                            65% { transform: rotate(12deg); }
                            75% { transform: rotate(-10deg); }
                            85% { transform: rotate(6deg); }
                            95% { transform: rotate(-4deg); }
                        }
                        .header-job-search-wrap {
                            position: relative;
                            margin-right: 10px;
                        }
                        .header-job-search-trigger {
                            width: 220px;
                            height: 42px;
                            border: 1px solid rgba(0, 0, 0, 0.15);
                            border-radius: 999px;
                            background: #fff;
                            color: #495057;
                            display: inline-flex;
                            align-items: center;
                            justify-content: space-between;
                            padding: 0 14px;
                            font-size: 13px;
                            cursor: pointer;
                        }
                        .header-job-search-trigger strong {
                            margin-left: 8px;
                            font-weight: 600;
                            color: #111827;
                        }
                        .header-job-search-panel {
                            position: absolute;
                            top: calc(100% + 10px);
                            right: 0;
                            width: 560px;
                            max-width: min(560px, calc(100vw - 24px));
                            background: #fff;
                            border: 1px solid rgba(0, 0, 0, 0.12);
                            border-radius: 12px;
                            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.16);
                            padding: 14px;
                            z-index: 1200;
                            display: none;
                        }
                        .header-job-search-panel.is-open {
                            display: block;
                        }
                        .header-job-search-grid {
                            display: grid;
                            grid-template-columns: 1fr 1fr;
                            gap: 10px;
                        }
                        .header-job-search-grid input {
                            width: 100%;
                            height: 38px;
                            border: 1px solid #d5d9df;
                            border-radius: 8px;
                            padding: 0 10px;
                            font-size: 13px;
                            outline: none;
                        }
                        .header-job-search-actions {
                            margin-top: 12px;
                            display: flex;
                            gap: 8px;
                        }
                        .header-job-search-actions button,
                        .header-job-search-actions a {
                            height: 36px;
                            border-radius: 8px;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            padding: 0 12px;
                            font-size: 13px;
                            text-decoration: none;
                        }
                        .header-job-search-submit {
                            border: 0;
                            background: #198754;
                            color: #fff;
                            font-weight: 600;
                        }
                        .header-job-search-clear {
                            border: 1px solid #d5d9df;
                            background: #fff;
                            color: #374151;
                        }
                        .candidate-avatar-btn {
                            width: 42px;
                            height: 42px;
                            border-radius: 50%;
                            border: 0;
                            background: #0d6efd;
                            color: #fff;
                            font-weight: 700;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            cursor: pointer;
                            overflow: hidden;
                            padding: 0;
                        }
                        .candidate-avatar-photo {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            display: block;
                        }
                        .candidate-avatar-dropdown {
                            position: absolute;
                            right: 0;
                            top: calc(100% + 10px);
                            min-width: 170px;
                            background: #fff;
                            border: 1px solid rgba(0, 0, 0, 0.1);
                            border-radius: 8px;
                            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
                            padding: 8px 0;
                            z-index: 1000;
                            display: none;
                        }
                        .candidate-avatar-dropdown.is-open {
                            display: block;
                        }
                        .candidate-avatar-dropdown a {
                            display: block;
                            padding: 8px 14px;
                            color: #212529;
                            text-decoration: none;
                            text-align: left;
                            font-size: 14px;
                        }
                        .candidate-avatar-dropdown a:hover {
                            background: #f8f9fa;
                        }
                    </style>
                    <div class="header-job-search-wrap d-none d-lg-inline-flex" id="headerJobSearchWrap">
                        <button type="button" class="header-job-search-trigger" id="headerJobSearchTrigger" aria-expanded="false" aria-haspopup="true">
                            <span><span class="icon-search"></span><strong>Search Jobs</strong></span>
                            <span class="fas fa-chevron-down" style="font-size: 12px;"></span>
                        </button>
                        <div class="header-job-search-panel" id="headerJobSearchPanel">
                            <form action="<?= base_url('jobs') ?>" method="get">
                                <div class="header-job-search-grid">
                                    <input type="text" name="designation" placeholder="Designation (e.g. React Developer)" value="<?= esc($headerDesignation) ?>">
                                    <input type="text" name="company" placeholder="Company" value="<?= esc($headerCompany) ?>">
                                    <input type="text" name="experience_level" placeholder="Experience (e.g. 2-4 years)" value="<?= esc($headerExperience) ?>">
                                    <input type="text" name="location" placeholder="Location" value="<?= esc($headerLocation) ?>">
                                    <input type="text" name="search" placeholder="Keywords (skills, tools)" value="<?= esc($headerSearch) ?>" style="grid-column: 1 / span 2;">
                                </div>
                                <div class="header-job-search-actions">
                                    <button type="submit" class="header-job-search-submit">
                                        <span class="icon-search mr-1"></span> Search
                                    </button>
                                    <a href="<?= base_url('jobs') ?>" class="header-job-search-clear">Clear</a>
                                </div>
                            </form>
                        </div>
                    </div>
                    <a href="<?= base_url('notifications') ?>" class="header-notification-link d-none d-lg-inline-flex <?= $unreadNotificationCount > 0 ? 'has-unread' : '' ?>" title="Notifications" aria-label="Notifications">
                        <span class="icon-bell" style="font-size: 18px; line-height: 1;"></span>
                        <?php if ($unreadNotificationCount > 0): ?>
                            <span class="header-notification-badge"><?= $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount ?></span>
                        <?php endif; ?>
                    </a>
                    <div class="d-none d-lg-inline-block candidate-avatar-menu" id="candidateAvatarMenu">
                        <button type="button" class="candidate-avatar-btn" id="candidateAvatarBtn" aria-haspopup="true" aria-expanded="false" title="<?= esc($candidateName) ?>">
                            <?php if ($candidatePhoto !== ''): ?>
                                <img src="<?= base_url($candidatePhoto) ?>" alt="<?= esc($candidateName) ?>" class="candidate-avatar-photo">
                            <?php else: ?>
                                <?= esc($candidateInitial) ?>
                            <?php endif; ?>
                        </button>
                        <div class="candidate-avatar-dropdown" id="candidateAvatarDropdown">
                            <a href="<?= base_url('candidate/profile') ?>">My Profile</a>
                            <a href="<?= base_url('logout') ?>">Logout</a>
                        </div>
                    </div>
                    <a href="#" class="site-menu-toggle js-menu-toggle d-inline-block d-xl-none mt-lg-2 ml-3">
                        <span class="icon-menu h3 m-0 p-0 mt-2"></span>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menu = document.getElementById('candidateAvatarMenu');
            const button = document.getElementById('candidateAvatarBtn');
            const dropdown = document.getElementById('candidateAvatarDropdown');
            const searchWrap = document.getElementById('headerJobSearchWrap');
            const searchTrigger = document.getElementById('headerJobSearchTrigger');
            const searchPanel = document.getElementById('headerJobSearchPanel');

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

            if (searchWrap && searchTrigger && searchPanel) {
                searchTrigger.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    const open = searchPanel.classList.toggle('is-open');
                    searchTrigger.setAttribute('aria-expanded', open ? 'true' : 'false');
                });

                document.addEventListener('click', function (event) {
                    if (!searchWrap.contains(event.target)) {
                        searchPanel.classList.remove('is-open');
                        searchTrigger.setAttribute('aria-expanded', 'false');
                    }
                });
            }
        });
    </script>
    <main>
