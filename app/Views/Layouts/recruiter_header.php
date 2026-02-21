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
    <link rel="stylesheet" href="<?= base_url('jobboard/css/fontawesome-all.min.css') ?>">
</head>
<body id="top" class="recruiter-jobboard">
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
                <div class="site-logo col-6"><a href="<?= base_url('recruiter/dashboard') ?>">HireMatrix</a></div>
                <nav class="mx-auto site-navigation">
                    <ul class="site-menu js-clone-nav d-none d-xl-block ml-0 pl-0">
                        <li><a href="<?= base_url('recruiter/dashboard') ?>" class="nav-link">Dashboard</a></li>
                        <li><a href="<?= base_url('recruiter/jobs') ?>" class="nav-link">My Jobs</a></li>
                        <li><a href="<?= base_url('recruiter/applications') ?>" class="nav-link">Applications</a></li>
                        <li><a href="<?= base_url('recruiter/post_job') ?>" class="nav-link">Post Job</a></li>
                        <li><a href="<?= base_url('recruiter/company-profile') ?>" class="nav-link">Company Profile</a></li>
                        <li><a href="<?= base_url('recruiter/slots') ?>" class="nav-link">Interview Slots</a></li>
                        <li><a href="<?= base_url('recruiter/dashboard/leaderboard') ?>" class="nav-link">Leaderboard</a></li>
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

    <?php
    $showHero = $showHero ?? true;
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
                        <a href="<?= base_url('recruiter/dashboard') ?>">Recruiter</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong><?= $heroTitle ?></strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <main>
