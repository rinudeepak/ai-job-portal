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
                <div class="site-logo col-6"><a href="<?= base_url('candidate/dashboard') ?>">HireMatrix</a></div>
                <nav class="mx-auto site-navigation">
                    <ul class="site-menu js-clone-nav d-none d-xl-block ml-0 pl-0">
                        <li><a href="<?= base_url('candidate/dashboard') ?>" class="nav-link">Home</a></li>
                        <li><a href="<?= base_url('jobs') ?>" class="nav-link">Jobs</a></li>
                        <li><a href="<?= base_url('candidate/profile') ?>" class="nav-link">My Profile</a></li>
                        <li><a href="<?= base_url('candidate/applications') ?>" class="nav-link">Applications</a></li>
                        <li><a href="<?= base_url('career-transition') ?>" class="nav-link">Career Transition AI</a></li>
                    </ul>
                </nav>
                <div class="right-cta-menu text-right d-flex aligin-items-center col-6">
                    <div class="ml-auto">
                        <a href="<?= base_url('logout') ?>" class="btn btn-primary border-width-2 d-none d-lg-inline-block">
                            <span class="mr-2 icon-lock_outline"></span>Logout
                        </a>
                    </div>
                    <a href="#" class="site-menu-toggle js-menu-toggle d-inline-block d-xl-none mt-lg-2 ml-3">
                        <span class="icon-menu h3 m-0 p-0 mt-2"></span>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <main>
