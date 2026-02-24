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
    $isJobsRoot = $isJobsListActive || $isSavedJobsActive || $isJobDetailsActive;
    $isJobsActive = $isJobsRoot || $isApplicationStatusActive;
    $isCareerTransitionActive = str_contains($currentPath, '/career-transition');

    $homeNavClass = $isHomeActive ? 'nav-link active' : 'nav-link';
    $jobsNavClass = $isJobsActive ? 'nav-link active' : 'nav-link';
    $careerNavClass = $isCareerTransitionActive ? 'nav-link active' : 'nav-link';
    $recommendedClass = $isRecommendedActive ? 'active' : '';
    $applicationStatusClass = $isApplicationStatusActive ? 'active' : '';
    $savedJobsClass = $isSavedJobsActive ? 'active' : '';

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
                <div class="site-logo col-6 col-xl-2"><a href="<?= base_url('candidate/dashboard') ?>">HireMatrix</a></div>
                <nav class="mx-auto site-navigation col-xl-7">
                    <ul class="site-menu js-clone-nav d-none d-xl-block ml-0 pl-0">
                        <li><a href="<?= base_url('candidate/dashboard') ?>" class="<?= $homeNavClass ?>">Home</a></li>
                        <li class="has-children">
                            <a href="<?= base_url('jobs') ?>" class="<?= $jobsNavClass ?>">Jobs</a>
                            <ul class="dropdown">
                                <li><a href="<?= base_url('jobs?tab=suggested') ?>" class="<?= $recommendedClass ?>">Recommended Jobs</a></li>
                                <li><a href="<?= base_url('candidate/applications') ?>" class="<?= $applicationStatusClass ?>">Application Status</a></li>
                                <li><a href="<?= base_url('candidate/saved-jobs') ?>" class="<?= $savedJobsClass ?>">Saved Jobs</a></li>
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
                        .header-job-search-form {
                            display: inline-flex;
                            align-items: center;
                            margin-right: 10px;
                        }
                        .header-job-search-input {
                            width: 170px;
                            height: 42px;
                            border: 1px solid rgba(0, 0, 0, 0.15);
                            border-radius: 999px;
                            padding: 0 14px;
                            font-size: 14px;
                            outline: none;
                        }
                        .header-job-search-btn {
                            margin-left: 6px;
                            height: 42px;
                            border: 0;
                            border-radius: 999px;
                            padding: 0 10px;
                            background: #198754;
                            color: #fff;
                            font-size: 13px;
                            cursor: pointer;
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
                    <form action="<?= base_url('jobs') ?>" method="get" class="header-job-search-form d-none d-lg-inline-flex">
                        <input
                            type="text"
                            name="search"
                            class="header-job-search-input"
                            placeholder="Search jobs..."
                            value="<?= esc($headerSearch) ?>"
                        >
                        <button type="submit" class="header-job-search-btn">Search</button>
                    </form>
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

            if (!menu || !button || !dropdown) {
                return;
            }

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
        });
    </script>
    <main>
