<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="base-url" content="<?= base_url() ?>">
    <title><?= esc($title ?? 'Recruiter Portal') ?></title>

    <link rel="stylesheet" href="<?= base_url('jobboard/css/custom-bs.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/jquery.fancybox.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/bootstrap-select.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/fonts/icomoon/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/fonts/line-icons/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/owl.carousel.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/animate.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/recruiter-pages.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/hirematrix-style.css?v=' . @filemtime(FCPATH . 'jobboard/css/hirematrix-style.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/fontawesome-all.min.css') ?>">
</head>
<body id="top" class="hirematrix-app recruiter-jobboard">
<div id="overlayer"></div>
<div class="loader">
    <div class="spinner-border text-primary" role="status">
        <span class="sr-only">Loading...</span>
    </div>
</div>

<div class="site-wrap">
    <?php
    $recruiterId = (int) (session()->get('user_id') ?? 0);
    $recruiterUnreadNotificationCount = $recruiterId > 0
        ? (int) model('NotificationModel')->getUnreadCount($recruiterId)
        : 0;
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
                <div class="site-logo col-6">
                    <a href="<?= base_url('recruiter/dashboard') ?>" class="d-inline-flex align-items-center">
                        <img src="<?= base_url('jobboard/images/Serp Hwak Logo.png') ?>" alt="HireMatrix Logo" style="height: 34px; width: auto; margin-right: 8px;">
                        <span style="text-transform: none;">HireMatrix</span>
                    </a>
                </div>
                <nav class="mx-auto site-navigation">
                    <ul class="site-menu js-clone-nav d-none d-xl-block ml-0 pl-0">
                        <li><a href="<?= base_url('recruiter/dashboard') ?>" class="nav-link">Dashboard</a></li>
                        <li class="has-children">
                            <a href="<?= base_url('recruiter/jobs') ?>" class="nav-link">Jobs</a>
                            <ul class="dropdown">
                                <li><a href="<?= base_url('recruiter/jobs') ?>">My Jobs</a></li>
                                <li><a href="<?= base_url('recruiter/post_job') ?>">Post a Job</a></li>
                            </ul>
                        </li>
                        <li><a href="<?= base_url('recruiter/candidates') ?>" class="nav-link">Candidates</a></li>
                        <li><a href="<?= base_url('recruiter/slots') ?>" class="nav-link">Interview Slots</a></li>
                    </ul>
                </nav>
                <div class="right-cta-menu text-right d-flex aligin-items-center col-6">
                    <style>
                        .recruiter-notification-link {
                            position: relative;
                            width: 42px;
                            height: 42px;
                            border: 0;
                            background: transparent;
                            color: #111827;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            margin-right: 10px;
                            text-decoration: none;
                        }
                        .recruiter-notification-link:hover {
                            color: #111827;
                            background: rgba(255, 123, 42, 0.04);
                            border-radius: 12px;
                        }
                        .recruiter-notification-link .icon-bell {
                            color: #111827;
                        }
                        .recruiter-notification-badge {
                            position: absolute;
                            top: -1px;
                            right: 2px;
                            min-width: 8px;
                            height: 8px;
                            border-radius: 999px;
                            background: #0b66ff;
                            color: transparent;
                            font-size: 0;
                            line-height: 0;
                            text-align: center;
                            padding: 0;
                            overflow: hidden;
                            box-shadow: 0 0 0 2px #fff;
                        }
                        .recruiter-avatar-menu {
                            position: relative;
                        }
                        .recruiter-avatar-btn {
                            width: 42px;
                            height: 42px;
                            border-radius: 12px;
                            border: 0;
                            background: linear-gradient(135deg, #3b82f6 0%, #ff7b2a 100%);
                            color: #fff;
                            display: inline-flex;
                            align-items: center;
                            justify-content: center;
                            font-weight: 700;
                            font-size: 16px;
                            cursor: pointer;
                            overflow: hidden;
                            padding: 0;
                            box-shadow: 0 8px 18px rgba(59, 130, 246, 0.24);
                            transition: transform .2s ease, box-shadow .2s ease;
                        }
                        .recruiter-avatar-btn:hover {
                            transform: translateY(-1px);
                            box-shadow: 0 10px 22px rgba(59, 130, 246, 0.28);
                        }
                        .recruiter-avatar-dropdown {
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
                        .recruiter-avatar-dropdown.is-open {
                            display: block;
                        }
                        .recruiter-avatar-dropdown a {
                            display: flex;
                            align-items: center;
                            gap: 10px;
                            padding: 8px 14px;
                            color: #212529;
                            text-decoration: none;
                            text-align: left;
                            font-size: 14px;
                        }
                        .recruiter-avatar-dropdown a:hover {
                            background: #f8f9fa;
                        }
                        .recruiter-avatar-dropdown a i {
                            width: 16px;
                            text-align: center;
                            color: #64748b;
                        }
                    </style>
                    <div class="ml-auto d-flex align-items-center">
                        <a href="<?= base_url('notifications') ?>" class="recruiter-notification-link d-none d-lg-inline-flex" title="Notifications" aria-label="Notifications">
                            <span class="icon-bell" style="font-size: 18px; line-height: 1;"></span>
                            <?php if ($recruiterUnreadNotificationCount > 0): ?>
                                <span class="recruiter-notification-badge"><?= $recruiterUnreadNotificationCount > 99 ? '99+' : $recruiterUnreadNotificationCount ?></span>
                            <?php endif; ?>
                        </a>
                        <div class="d-none d-lg-inline-block recruiter-avatar-menu" id="recruiterAvatarMenu">
                            <button type="button" class="recruiter-avatar-btn" id="recruiterAvatarBtn" aria-haspopup="true" aria-expanded="false" title="<?= esc((string) (session()->get('user_name') ?? 'Recruiter')) ?>">
                                <?= strtoupper(substr(session()->get('user_name') ?? 'R', 0, 1)) ?>
                            </button>
                            <div class="recruiter-avatar-dropdown" id="recruiterAvatarDropdown">
                                <a href="<?= base_url('recruiter/company-profile') ?>"><i class="fas fa-briefcase"></i><span>Company Profile</span></a>
                                <a href="<?= base_url('account/change-password') ?>"><i class="fas fa-lock"></i><span>Change Password</span></a>
                                <a href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
                            </div>
                        </div>
                    </div>
                    <a href="#" class="site-menu-toggle js-menu-toggle d-inline-block d-xl-none mt-lg-2 ml-3">
                        <span class="icon-menu h3 m-0 p-0 mt-2"></span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <?php
    $showHero = $showHero ?? false;
    $heroTitle = esc($title ?? 'Recruiter');
    ?>
    <?php if ($showHero): ?>
    <section class="section-hero overlay inner-page bg-image recruiter-global-hero" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1 class="text-white font-weight-bold"><?= $heroTitle ?></h1>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('recruiter/dashboard') ?>">Home</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong><?= $heroTitle ?></strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <main>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menu = document.getElementById('recruiterAvatarMenu');
            const button = document.getElementById('recruiterAvatarBtn');
            const dropdown = document.getElementById('recruiterAvatarDropdown');

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
