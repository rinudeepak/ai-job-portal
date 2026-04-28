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
    <link rel="stylesheet" href="<?= base_url('jobboard/css/hirematrix-style.css?v=' . @filemtime(FCPATH . 'jobboard/css/hirematrix-style.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/fontawesome-all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/responsive.css?v=' . @filemtime(FCPATH . 'jobboard/css/responsive.css')) ?>">
    <style>
        /* Styles for the job card tools dropdown */
        .job-card-tools-wrapper {
            position: absolute;
            bottom: 14px;
            right: 44px; /* Positioned side-by-side with the save button */
            z-index: 100; /* Ensure wrapper is above card content */
            display: block;
        }

        .job-card-tools-toggle {
            height: 31px; /* Match standard button height */
            width: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            border-radius: 6px;
            cursor: pointer;
        }

        .job-card-tools-dropdown {
            display: none; /* Hidden by default */
            position: absolute;
            background-color: #ffffff;
            min-width: 170px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
            z-index: 1000; /* High z-index to allow selection */
            padding: 6px 0;
            border-radius: 4px;
            right: 0; /* Align dropdown to the right of the icon */
            bottom: 100%; /* Flip direction to open upwards */
            top: auto;
            margin-bottom: 6px; /* Small gap at the bottom */
            border: 1px solid #e2e8f0;
        }

        .job-card-tools-wrapper:hover .job-card-tools-dropdown {
            display: block; /* Show on hover */
        }

        .job-card-tools-item {
            display: block;
            width: 100%;
            padding: 8px 16px;
            text-align: left;
            background: none;
            border: none;
            font-size: 13px;
            color: #334155;
            cursor: pointer;
            transition: background 0.1s;
        }
        .job-card-tools-item:hover {
            background-color: #f1f5f9;
            color: #2563eb;
        }
    </style>
</head>
<body id="top" class="hirematrix-app candidate-app">
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
    $applicationCount = $candidateId > 0
        ? (int) model('ApplicationModel')->where('candidate_id', $candidateId)->countAllResults()
        : 0;
    $savedJobsCount = $candidateId > 0
        ? (int) model('SavedJobModel')->where('candidate_id', $candidateId)->countAllResults()
        : 0;
    $jobAlertsCount = $candidateId > 0
        ? (int) model('JobAlertModel')->where('candidate_id', $candidateId)->where('is_active', 1)->countAllResults()
        : 0;
    $profileStrength = (int) ($profileStrength ?? 0);
    $formatCompactCount = static function (int $count): string {
        return $count > 99 ? '99+' : (string) $count;
    };
    $profilePrompt = $profileStrength >= 80
        ? 'Your profile is recruiter-ready. Keep it active with fresh applications.'
        : ($profileStrength >= 50
            ? 'Complete a few more profile details to unlock better job matches.'
            : 'Complete your profile to improve visibility and get more relevant jobs.');
    $profilePromptCta = $profileStrength >= 80 ? 'View Profile' : 'Complete Profile';
    $profilePromptUrl = base_url('candidate/profile');
    $recommendationTitle = $savedJobsCount > 0 ? 'Revisit your saved jobs' : 'Jobs picked for your profile';
    $recommendationText = $savedJobsCount > 0
        ? 'You already have jobs shortlisted. Re-open them and apply before they go stale.'
        : 'Explore recommended roles matched to your profile, skills, and recent activity.';
    $recommendationUrl = $savedJobsCount > 0 ? base_url('candidate/saved-jobs') : base_url('jobs?tab=suggested');
    $recommendationCta = $savedJobsCount > 0 ? 'View Saved Jobs' : 'See Recommended Jobs';
    $isHomeActive = $pathEndsWith('/candidate') || $pathEndsWith('/candidate/dashboard');
    $isJobDetailsActive = str_contains($currentPath, '/job/');
    $isJobsListActive = $pathEndsWith('/jobs');
    $isRecommendedActive = $isJobsListActive && $activeTab === 'suggested';
    $isApplicationStatusActive = $pathEndsWith('/candidate/applications');
    $isSavedJobsActive = $pathEndsWith('/candidate/saved-jobs');
    
    $isJobAlertsActive = false;
    $isCompaniesActive = $pathEndsWith('/companies') || str_contains($currentPath, '/company/');
    $isJobsRoot = $isJobsListActive || $isSavedJobsActive || $isJobDetailsActive;
    $isJobsActive = $isJobsRoot || $isApplicationStatusActive || $isJobAlertsActive;
    $isCareerTransitionActive = str_contains($currentPath, '/career-transition');
    $isResumeStudioActive = $pathEndsWith('/candidate/resume-studio');
    $isPremiumMentorActive = str_contains($currentPath, '/premium-mentor');
    $isJobStrategyActive = str_contains($currentPath, '/job-strategy');
    $isServicesActive = $isCareerTransitionActive || $isResumeStudioActive || $isPremiumMentorActive || $isJobStrategyActive ;

    $homeNavClass = $isHomeActive ? 'nav-link active' : 'nav-link';
    $jobsNavClass = $isJobsActive ? 'nav-link active' : 'nav-link';
    $companiesNavClass = $isCompaniesActive ? 'nav-link active' : 'nav-link';
    $servicesNavClass = $isServicesActive ? 'nav-link active' : 'nav-link';
    $recommendedClass = $isRecommendedActive ? 'active' : '';
    $applicationStatusClass = $isApplicationStatusActive ? 'active' : '';
    $savedJobsClass = $isSavedJobsActive ? 'active' : '';
    
    $careerTransitionClass = $isCareerTransitionActive ? 'active' : '';
    $resumeStudioClass = $isResumeStudioActive ? 'active' : '';
    $jobStrategyClass = $isJobStrategyActive ? 'active' : '';
    
    if ($candidatePhoto === '' && $candidateId > 0) {
        $candidateRecord = model('UserModel')->findCandidateWithProfile($candidateId);
        $candidatePhoto = (string) ($candidateRecord['profile_photo'] ?? '');
    }
    $candidatePhotoUrl = '';
    if ($candidatePhoto !== '') {
        $candidatePhotoUrl = preg_match('/^https?:\/\//i', $candidatePhoto) ? $candidatePhoto : base_url($candidatePhoto);
    }
    $premiumSubscription = null;
    if ($candidateId > 0) {
        try {
            $premiumSubscription = model('SubscriptionModel')->getUserActiveSubscription($candidateId);
        } catch (\Throwable $e) {
            $premiumSubscription = null;
        }
    }
    $premiumLocked = !$premiumSubscription;
    $careerTransitionUrl = $premiumLocked ? base_url('premium/plans?service=career-transition') : base_url('career-transition');
    $resumeStudioUrl = $premiumLocked ? base_url('premium/plans?service=resume-studio') : base_url('candidate/resume-studio');
    $mentorUrl = $premiumLocked ? base_url('premium/plans?service=mentor') : base_url('premium-mentor');
    ?>
    <div class="site-mobile-menu site-navbar-target">
        <div class="site-mobile-menu-header">
            <div class="site-mobile-menu-close mt-3">
                <span class="icon-close2 js-menu-toggle"></span>
            </div>
        </div>
        <div class="site-mobile-menu-body"></div>
    </div>

    <!-- Naukri-style hamburger drawer -->
    <div class="hm-drawer" id="hmDrawer" aria-hidden="true">
        <div class="hm-drawer-inner">

            <!-- Header: profile card -->
            <div class="hm-drawer-head">
                <div class="hm-drawer-avatar">
                    <?php if ($candidatePhotoUrl !== ''): ?>
                        <img src="<?= esc($candidatePhotoUrl) ?>" alt="<?= esc($candidateName) ?>">
                    <?php else: ?>
                        <span><?= esc($candidateInitial) ?></span>
                    <?php endif; ?>
                </div>
                <div class="hm-drawer-user">
                    <strong><?= esc($candidateName) ?></strong>
                    <span><?= esc($profileHeadline ?? 'Candidate') ?></span>
                </div>
                <button class="hm-drawer-close" id="hmDrawerClose" aria-label="Close menu">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Profile completion bar -->
            <div class="hm-drawer-progress">
                <div class="hm-drawer-progress-row">
                    <span>Profile completion</span>
                    <strong><?= $profileStrength ?>%</strong>
                </div>
                <div class="hm-drawer-progress-bar">
                    <div style="width:<?= $profileStrength ?>%"></div>
                </div>
                <p class="hm-drawer-progress-note"><?= esc($profilePrompt) ?></p>
                <div class="hm-drawer-metrics" aria-label="Candidate activity snapshot">
                    <a href="<?= base_url('candidate/applications') ?>" class="hm-drawer-metric-chip">
                        <strong><?= esc($formatCompactCount($applicationCount)) ?></strong>
                        <span>Applied</span>
                    </a>
                    <a href="<?= base_url('candidate/saved-jobs') ?>" class="hm-drawer-metric-chip">
                        <strong><?= esc($formatCompactCount($savedJobsCount)) ?></strong>
                        <span>Saved</span>
                    </a>
                    <a href="<?= base_url('candidate/job-alerts') ?>" class="hm-drawer-metric-chip">
                        <strong><?= esc($formatCompactCount($jobAlertsCount)) ?></strong>
                        <span>Alerts</span>
                    </a>
                </div>
            </div>

            <div class="hm-drawer-body">
                <div class="hm-drawer-cta-card">
                    <div class="hm-drawer-cta-kicker">For You</div>
                    <h3><?= esc($recommendationTitle) ?></h3>
                    <p><?= esc($recommendationText) ?></p>
                    <div class="hm-drawer-cta-actions">
                        <a href="<?= esc($recommendationUrl) ?>" class="hm-drawer-cta-primary"><?= esc($recommendationCta) ?></a>
                        <a href="<?= esc($profilePromptUrl) ?>" class="hm-drawer-cta-secondary"><?= esc($profilePromptCta) ?></a>
                    </div>
                </div>

                <!-- Section: My Activity -->
                <div class="hm-drawer-section">
                    <div class="hm-drawer-section-title">My Activity</div>
                    <a href="<?= base_url('candidate/dashboard') ?>" class="hm-drawer-link <?= $isHomeActive ? 'is-active' : '' ?>">
                        <span class="hm-drawer-link-icon"><i class="fas fa-home"></i></span>
                        <span>Dashboard</span>
                    </a>
                    <a href="<?= base_url('candidate/applications') ?>" class="hm-drawer-link <?= $isApplicationStatusActive ? 'is-active' : '' ?>">
                        <span class="hm-drawer-link-icon"><i class="fas fa-briefcase"></i></span>
                        <span>My Applications</span>
                        <span class="hm-drawer-pill hm-drawer-pill-muted"><?= esc($formatCompactCount($applicationCount)) ?></span>
                    </a>
                    <a href="<?= base_url('candidate/saved-jobs') ?>" class="hm-drawer-link <?= $isSavedJobsActive ? 'is-active' : '' ?>">
                        <span class="hm-drawer-link-icon"><i class="fas fa-bookmark"></i></span>
                        <span>Saved Jobs</span>
                        <span class="hm-drawer-pill hm-drawer-pill-muted"><?= esc($formatCompactCount($savedJobsCount)) ?></span>
                    </a>
                    <a href="<?= base_url('candidate/my-bookings') ?>" class="hm-drawer-link">
                        <span class="hm-drawer-link-icon"><i class="fas fa-calendar-check"></i></span>
                        <span>Interview Bookings</span>
                    </a>
                    <a href="<?= base_url('notifications') ?>" class="hm-drawer-link">
                        <span class="hm-drawer-link-icon"><i class="fas fa-bell"></i></span>
                        <span>Notifications</span>
                        <?php if ($unreadNotificationCount > 0): ?>
                            <span class="hm-drawer-badge"><?= $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount ?></span>
                        <?php endif; ?>
                    </a>
                </div>

                <!-- Section: Jobs -->
                <div class="hm-drawer-section">
                    <div class="hm-drawer-section-title">Jobs</div>
                    <a href="<?= base_url('jobs') ?>" class="hm-drawer-link">
                        <span class="hm-drawer-link-icon"><i class="fas fa-search"></i></span>
                        <span>Browse All Jobs</span>
                    </a>
                    <a href="<?= base_url('jobs?tab=suggested') ?>" class="hm-drawer-link">
                        <span class="hm-drawer-link-icon"><i class="fas fa-fire"></i></span>
                        <span>Recommended Jobs</span>
                        <span class="hm-drawer-pill hm-drawer-pill-accent">For You</span>
                    </a>
                    <a href="<?= base_url('companies') ?>" class="hm-drawer-link <?= $isCompaniesActive ? 'is-active' : '' ?>">
                        <span class="hm-drawer-link-icon"><i class="fas fa-building"></i></span>
                        <span>Companies</span>
                    </a>
                    <a href="<?= base_url('candidate/job-alerts') ?>" class="hm-drawer-link">
                        <span class="hm-drawer-link-icon"><i class="fas fa-bell"></i></span>
                        <span>Job Alerts</span>
                        <span class="hm-drawer-pill hm-drawer-pill-muted"><?= esc($formatCompactCount($jobAlertsCount)) ?></span>
                    </a>
                </div>

                <!-- Section: Career Tools & Interview Prep -->
                <div class="hm-drawer-section">
                    <div class="hm-drawer-section-title">Career Tools</div>
                    <a href="<?= base_url('candidate/job-search-strategy') ?>" class="hm-drawer-link <?= $isJobStrategyActive ? 'is-active' : '' ?>">
                        <span class="hm-drawer-link-icon"><i class="fas fa-chart-line"></i></span>
                        <span>Job Search Strategy Coach</span>
                    </a>
                    <a href="<?= esc($careerTransitionUrl) ?>" class="hm-drawer-link <?= $isCareerTransitionActive ? 'is-active' : '' ?>">
                        <span class="hm-drawer-link-icon"><i class="fas fa-route"></i></span>
                        <span>Career Transition AI</span>
                        <?php if ($premiumLocked): ?><span class="hm-drawer-pro">Pro</span><?php endif; ?>
                    </a>
                    <a href="<?= esc($resumeStudioUrl) ?>" class="hm-drawer-link <?= $isResumeStudioActive ? 'is-active' : '' ?>">
                        <span class="hm-drawer-link-icon"><i class="fas fa-file-alt"></i></span>
                        <span>Resume Studio</span>
                        <?php if ($premiumLocked): ?><span class="hm-drawer-pro">Pro</span><?php endif; ?>
                    </a>
                    <a href="<?= esc($mentorUrl) ?>" class="hm-drawer-link <?= $isPremiumMentorActive ? 'is-active' : '' ?>">
                        <span class="hm-drawer-link-icon"><i class="fas fa-robot"></i></span>
                        <span>AI Career Mentor</span>
                        <?php if ($premiumLocked): ?><span class="hm-drawer-pro">Pro</span><?php endif; ?>
                    </a>
                </div>

                <!-- Section: Account -->
                <div class="hm-drawer-section">
                    <div class="hm-drawer-section-title">Account</div>
                    <a href="<?= base_url('candidate/profile') ?>" class="hm-drawer-link">
                        <span class="hm-drawer-link-icon"><i class="fas fa-user"></i></span>
                        <span>My Profile</span>
                    </a>
                    <a href="<?= base_url('candidate/settings') ?>" class="hm-drawer-link">
                        <span class="hm-drawer-link-icon"><i class="fas fa-cog"></i></span>
                        <span>Settings</span>
                    </a>
                    <a href="<?= base_url('premium/plans') ?>" class="hm-drawer-link">
                        <span class="hm-drawer-link-icon"><i class="fas fa-crown"></i></span>
                        <span>Premium Plans</span>
                    </a>
                    <a href="<?= base_url('payment/history') ?>" class="hm-drawer-link">
                        <span class="hm-drawer-link-icon"><i class="fas fa-receipt"></i></span>
                        <span>Payment History</span>
                    </a>
                </div>

            </div>

            <!-- Footer: logout -->
            <div class="hm-drawer-footer">
                <a href="<?= base_url('logout') ?>" class="hm-drawer-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>

        </div>
    </div>
    <div class="hm-drawer-overlay" id="hmDrawerOverlay"></div>

    <header class="site-navbar site-navbar-target">
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
                        <li class="has-children">
                            <a href="<?= base_url('jobs') ?>" class="<?= $jobsNavClass ?>">Jobs</a>
                            <ul class="dropdown">
                                <li><a href="<?= base_url('jobs?tab=suggested') ?>" class="<?= $recommendedClass ?>">Recommended Jobs</a></li>
                                <li><a href="<?= base_url('candidate/applications') ?>" class="<?= $applicationStatusClass ?>">Application Status</a></li>
                                <li><a href="<?= base_url('candidate/saved-jobs') ?>" class="<?= $savedJobsClass ?>">Saved Jobs</a></li>
                            </ul>
                        </li>
                        <li><a href="<?= base_url('companies') ?>" class="<?= $companiesNavClass ?>">Companies</a></li>
                        <li class="has-children">
                            <a href="#" class="<?= $servicesNavClass ?>">Services</a>
                            <ul class="dropdown">
                                <li><a href="<?= base_url('candidate/job-search-strategy') ?>" class="<?= $jobStrategyClass ?>">Job Strategy</a></li>
                                <li><a href="<?= esc($careerTransitionUrl) ?>" class="<?= $careerTransitionClass ?>">Career Transition AI</a></li>
                                <li><a href="<?= esc($resumeStudioUrl) ?>" class="<?= $resumeStudioClass ?>">Resume Studio</a></li>
                                <li><a href="<?= esc($mentorUrl) ?>" class="">AI Career Mentor</a></li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <div class="right-cta-menu text-right d-flex justify-content-end align-items-center col-6 col-xl-3">
                    <!-- Desktop search trigger (≥1200px) -->
                    <div class="header-job-search-wrap" id="headerJobSearchWrap">
                        <button type="button" class="header-job-search-trigger" id="headerJobSearchTrigger" aria-expanded="false" aria-haspopup="true">
                            <span class="icon-search" style="font-size:14px;"></span>
                            <strong>Search Jobs</strong>
                            <span class="fas fa-chevron-down" style="font-size:11px;margin-left:6px;"></span>
                        </button>
                        <div class="header-job-search-panel" id="headerJobSearchPanel">
                            <form action="<?= base_url('jobs') ?>" method="get" id="headerSearchForm">
                                <div class="hjs-row">
                                    <div class="hjs-field hjs-field-keyword">
                                        <span class="icon-search hjs-icon"></span>
                                        <input type="text" name="search" placeholder="Job title, skills or company" value="<?= esc($headerSearch !== '' ? $headerSearch : ($headerDesignation !== '' ? $headerDesignation : $headerCompany)) ?>" autocomplete="off">
                                    </div>
                                    <div class="hjs-divider"></div>
                                    <div class="hjs-field hjs-field-exp">
                                        <i class="fas fa-briefcase hjs-icon"></i>
                                        <select name="experience_level">
                                            <option value="">Experience</option>
                                            <?php foreach (['fresher' => 'Fresher', 'junior' => 'Junior', 'mid' => 'Mid-Level', 'senior' => 'Senior'] as $expValue => $expLabel): ?>
                                                <option value="<?= esc($expValue) ?>" <?= strtolower($headerExperience) === strtolower($expValue) ? 'selected' : '' ?>><?= esc($expLabel) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="hjs-divider"></div>
                                    <div class="hjs-field hjs-field-location">
                                        <i class="fas fa-map-marker-alt hjs-icon"></i>
                                        <input type="text" name="location" placeholder="Location" value="<?= esc($headerLocation) ?>" autocomplete="off">
                                    </div>
                                    <button type="submit" class="hjs-submit"><span class="icon-search"></span> Search</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Notification bell (desktop) -->
                    <a href="<?= base_url('notifications') ?>" class="header-notification-link <?= $unreadNotificationCount > 0 ? 'has-unread' : '' ?>" title="Notifications" aria-label="Notifications">
                        <span class="icon-bell" style="font-size:18px;line-height:1;"></span>
                        <?php if ($unreadNotificationCount > 0): ?>
                            <span class="header-notification-badge js-notification-badge" data-unread-count="<?= $unreadNotificationCount ?>"><?= $unreadNotificationCount > 99 ? '99+' : $unreadNotificationCount ?></span>
                        <?php endif; ?>
                    </a>

                    <!-- Avatar (desktop) -->
                    <div class="candidate-avatar-menu" id="candidateAvatarMenu">
                        <button type="button" class="candidate-avatar-btn" id="candidateAvatarBtn" aria-haspopup="true" aria-expanded="false" title="<?= esc($candidateName) ?>">
                            <?php if ($candidatePhoto !== ''): ?>
                                <img src="<?= esc($candidatePhotoUrl) ?>" alt="<?= esc($candidateName) ?>" class="candidate-avatar-photo">
                            <?php else: ?>
                                <?= esc($candidateInitial) ?>
                            <?php endif; ?>
                        </button>
                        <div class="candidate-avatar-dropdown" id="candidateAvatarDropdown">
                            <a href="<?= base_url('candidate/profile') ?>"><i class="fas fa-user"></i><span>My Profile</span></a>
                            <a href="<?= base_url('candidate/settings') ?>"><i class="fas fa-cog"></i><span>Settings</span></a>
                            <a href="<?= base_url('premium/plans') ?>"><i class="fas fa-crown"></i><span>Premium Plans</span></a>
                            <a href="<?= base_url('payment/history') ?>"><i class="fas fa-receipt"></i><span>Payment History</span></a>
                            <a href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
                        </div>
                    </div>

                    <!-- Mobile actions (search icon opens drawer, bell, hamburger) -->
                    <div class="mobile-nav-actions d-flex d-xl-none align-items-center">
                        <button type="button" class="mobile-nav-icon" id="mobileSearchToggle" title="Search" aria-label="Search">
                            <span class="icon-search"></span>
                        </button>
                        <a href="<?= base_url('notifications') ?>" class="mobile-nav-icon <?= $unreadNotificationCount > 0 ? 'has-unread' : '' ?>" title="Notifications">
                            <span class="icon-bell"></span>
                            <?php if ($unreadNotificationCount > 0): ?><span class="mobile-nav-dot"></span><?php endif; ?>
                        </a>
                        <a href="#" class="mobile-nav-hamburger" id="hmDrawerToggle" aria-label="Menu" aria-expanded="false">
                            <span></span><span></span><span></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Mobile search drawer -->
    <div id="mobileSearchDrawer" class="mobile-search-drawer">
        <form action="<?= base_url('jobs') ?>" method="get" class="mobile-search-form">
            <div class="mobile-search-field">
                <span class="icon-search mobile-search-icon"></span>
                <input type="text" name="search" placeholder="Job title, skills or company" value="<?= esc($headerSearch !== '' ? $headerSearch : ($headerDesignation !== '' ? $headerDesignation : $headerCompany)) ?>" autocomplete="off">
            </div>
            <div class="mobile-search-row2">
                <div class="mobile-search-field mobile-search-field-half">
                    <i class="fas fa-map-marker-alt mobile-search-icon"></i>
                    <input type="text" name="location" placeholder="Location" value="<?= esc($headerLocation) ?>" autocomplete="off">
                </div>
                <div class="mobile-search-field mobile-search-field-half">
                    <i class="fas fa-briefcase mobile-search-icon"></i>
                    <select name="experience_level">
                        <option value="">Experience</option>
                        <?php foreach (['fresher' => 'Fresher', 'junior' => 'Junior', 'mid' => 'Mid-Level', 'senior' => 'Senior'] as $expValue => $expLabel): ?>
                            <option value="<?= esc($expValue) ?>" <?= strtolower($headerExperience) === strtolower($expValue) ? 'selected' : '' ?>><?= esc($expLabel) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="mobile-search-submit"><span class="icon-search"></span> Search Jobs</button>
        </form>
    </div>
    <div id="mobileSearchOverlay" class="mobile-search-overlay"></div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        /* ── Desktop: search trigger dropdown ── */
        var searchWrap    = document.getElementById('headerJobSearchWrap');
        var searchTrigger = document.getElementById('headerJobSearchTrigger');
        var searchPanel   = document.getElementById('headerJobSearchPanel');
        if (searchTrigger && searchPanel) {
            searchTrigger.addEventListener('click', function (e) {
                e.stopPropagation();
                var open = searchPanel.classList.toggle('is-open');
                searchTrigger.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (open) searchPanel.querySelector('input[name="search"]').focus();
            });
            document.addEventListener('click', function (e) {
                if (searchWrap && !searchWrap.contains(e.target)) {
                    searchPanel.classList.remove('is-open');
                    searchTrigger.setAttribute('aria-expanded', 'false');
                }
            });
        }

        /* ── Mobile: search drawer ── */
        var mobileToggle  = document.getElementById('mobileSearchToggle');
        var mobileDrawer  = document.getElementById('mobileSearchDrawer');
        var mobileOverlay = document.getElementById('mobileSearchOverlay');
        function openMobileSearch() {
            mobileDrawer.classList.add('is-open');
            mobileOverlay.classList.add('is-open');
            document.body.classList.add('mobile-search-open');
            setTimeout(function () { mobileDrawer.querySelector('input[name="search"]').focus(); }, 100);
        }
        function closeMobileSearch() {
            mobileDrawer.classList.remove('is-open');
            mobileOverlay.classList.remove('is-open');
            document.body.classList.remove('mobile-search-open');
        }
        if (mobileToggle) mobileToggle.addEventListener('click', openMobileSearch);
        if (mobileOverlay) mobileOverlay.addEventListener('click', closeMobileSearch);

        /* ── Hamburger drawer ── */
        var hmToggle  = document.getElementById('hmDrawerToggle');
        var hmDrawer  = document.getElementById('hmDrawer');
        var hmOverlay = document.getElementById('hmDrawerOverlay');
        var hmClose   = document.getElementById('hmDrawerClose');
        function openHmDrawer() {
            hmDrawer.classList.add('is-open');
            hmOverlay.classList.add('is-open');
            hmDrawer.setAttribute('aria-hidden', 'false');
            document.body.classList.add('hm-drawer-open');
        }
        function closeHmDrawer() {
            hmDrawer.classList.remove('is-open');
            hmOverlay.classList.remove('is-open');
            hmDrawer.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('hm-drawer-open');
        }
        if (hmToggle)  hmToggle.addEventListener('click',  function(e){ e.preventDefault(); openHmDrawer(); });
        if (hmClose)   hmClose.addEventListener('click',   closeHmDrawer);
        if (hmOverlay) hmOverlay.addEventListener('click', closeHmDrawer);

        /* ── Desktop: avatar dropdown ── */
        var avatarMenu     = document.getElementById('candidateAvatarMenu');
        var avatarBtn      = document.getElementById('candidateAvatarBtn');
        var avatarDropdown = document.getElementById('candidateAvatarDropdown');
        if (avatarBtn && avatarDropdown) {
            avatarBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                avatarDropdown.classList.toggle('is-open');
                avatarBtn.setAttribute('aria-expanded', avatarDropdown.classList.contains('is-open') ? 'true' : 'false');
            });
            document.addEventListener('click', function (e) {
                if (avatarMenu && !avatarMenu.contains(e.target)) {
                    avatarDropdown.classList.remove('is-open');
                    avatarBtn.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
    </script>
    <main>
    <!-- Global mobile bottom tab bar (visible on all candidate pages ≤1100px) -->
    <nav class="dash-mobile-tabs" aria-label="Quick navigation">
        <?php
        $currentPath2 = '/' . trim((string) parse_url(current_url(), PHP_URL_PATH), '/');
        $tabActive = [
            'home'     => $currentPath2 === '/candidate' || $currentPath2 === '/candidate/dashboard',
            'jobs'     => str_contains($currentPath2, '/jobs') || str_contains($currentPath2, '/job/'),
            'applied'  => str_contains($currentPath2, '/candidate/applications'),
            'saved'    => str_contains($currentPath2, '/candidate/saved-jobs'),
            'profile'  => str_contains($currentPath2, '/candidate/profile') || str_contains($currentPath2, '/candidate/settings'),
        ];
        ?>
        <a href="<?= base_url('candidate/dashboard') ?>" class="dash-tab <?= $tabActive['home'] ? 'is-active' : '' ?>">
            <i class="fas fa-home"></i><span>Home</span>
        </a>
        <a href="<?= base_url('jobs?tab=suggested') ?>" class="dash-tab <?= $tabActive['jobs'] ? 'is-active' : '' ?>">
            <i class="fas fa-fire"></i><span>Jobs</span>
        </a>
        <a href="<?= base_url('candidate/applications') ?>" class="dash-tab <?= $tabActive['applied'] ? 'is-active' : '' ?>">
            <i class="fas fa-briefcase"></i><span>Applied</span>
        </a>
        <a href="<?= base_url('candidate/saved-jobs') ?>" class="dash-tab <?= $tabActive['saved'] ? 'is-active' : '' ?>">
            <i class="fas fa-bookmark"></i><span>Saved</span>
        </a>
        <a href="<?= base_url('candidate/profile') ?>" class="dash-tab <?= $tabActive['profile'] ? 'is-active' : '' ?>">
            <i class="fas fa-user"></i><span>Profile</span>
        </a>
    </nav>
