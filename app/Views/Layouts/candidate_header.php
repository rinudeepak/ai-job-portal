<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="base-url" content="<?= base_url() ?>">
    <title><?= $title  ?> </title>

    <!-- CSS here -->
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/owl.carousel.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/price_rangs.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/flaticon.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/slicknav.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/animate.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/magnific-popup.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/fontawesome-all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/themify-icons.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/slick.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/nice-select.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">

</head>

<body class="bg-light">

    <header>
        <div class="header-area header-transparrent">
            <div class="headder-top header-sticky">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-3">
                            <div class="logo">
                                <a href="<?= base_url() ?>">
                                    <img src="<?= base_url('assets/img/logo/logo.png') ?>" alt="">
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-9">
                            <div class="menu-wrapper">
                                <!-- Main-menu -->
                                <div class="main-menu">
                                    <nav class="d-none d-lg-block">
                                        <ul id="navigation">
                                            <li><a href="<?= base_url('candidate/dashboard') ?>">Home</a></li>
                                            <li><a href="<?= base_url('jobs') ?>">Jobs</a></li>
                                            <li><a href="<?= base_url('candidate/profile') ?>">My Profile</a>
                                            </li>
                                            <li><a href="<?= base_url('candidate/applications') ?>">My Applications</a>
                                            </li>
                                            <li><a href="<?= base_url('career-transition') ?>">Career Transition AI</a>
                                            </li>
                                            <li class="nav-item">
   <?php /*
 <?= view('candidate/components/notification_bell', [
        'notifications' => model('NotificationModel')->getUnreadNotifications(session()->get('user_id'), 5),
        'unread_count' => model('NotificationModel')->getUnreadCount(session()->get('user_id'))
    ]) ?>*/?>
</li>
                                            
                                        </ul>
                                    </nav>
                                </div>

                                <!-- Header-btn -->
                                <div class="header-btn f-right">
                                    
                                    <a href="<?= base_url('logout') ?>" class="genric-btn danger circle">
                                        Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main>