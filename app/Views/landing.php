<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>JobBoard | Home</title>
    <meta name="description" content="AI Job Portal home page">

    <link rel="stylesheet" href="<?= base_url('jobboard/css/custom-bs.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/jquery.fancybox.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/bootstrap-select.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/fonts/icomoon/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/fonts/line-icons/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/owl.carousel.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/animate.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/fontawesome-all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/style.css') ?>">
</head>
<body id="top">
<div id="overlayer"></div>
<div class="loader">
    <div class="spinner-border text-primary" role="status"><span class="sr-only">Loading...</span></div>
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

    <header class="site-navbar mt-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="site-logo col-6"><a href="<?= base_url('/') ?>">JobBoard</a></div>

                <nav class="mx-auto site-navigation">
                    <ul class="site-menu js-clone-nav d-none d-xl-block ml-0 pl-0">
                        <li><a href="<?= base_url('/') ?>" class="nav-link active">Home</a></li>
                        <li><a href="<?= base_url('login') ?>">Browse Jobs</a></li>
                        <li><a href="<?= base_url('login') ?>">Career Transition AI</a></li>
                    </ul>
                </nav>

                <div class="right-cta-menu text-right d-flex aligin-items-center col-6">
                    <div class="ml-auto">
                        <a href="<?= base_url('recruiter/register') ?>" class="btn btn-outline-white border-width-2 d-none d-lg-inline-block"><span class="mr-2 icon-add"></span>Recruiter</a>
                        <a href="<?= base_url('register') ?>" class="btn btn-outline-white border-width-2 d-none d-lg-inline-block">Candidate</a>
                        <a href="<?= base_url('login') ?>" class="btn btn-primary border-width-2 d-none d-lg-inline-block"><span class="mr-2 icon-lock_outline"></span>Log In</a>
                    </div>
                    <a href="#" class="site-menu-toggle js-menu-toggle d-inline-block d-xl-none mt-lg-2 ml-3"><span class="icon-menu h3 m-0 p-0 mt-2"></span></a>
                </div>
            </div>
        </div>
    </header>

    <section class="home-section section-hero overlay bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-12">
                    <div class="mb-5 text-center">
                        <h1 class="text-white font-weight-bold">The Easiest Way To Get Your Dream Job</h1>
                        <p class="lead text-white">AI-powered hiring for candidates and recruiters in one modern workflow.</p>
                    </div>
                    <form action="<?= base_url('login') ?>" method="get" class="search-jobs-form">
                        <div class="row mb-5">
                            <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
                                <input type="text" class="form-control form-control-lg" placeholder="Job title, Company..." disabled>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
                                <select class="selectpicker" data-style="btn-white btn-lg" data-width="100%" title="Select Location" disabled>
                                    <option>Anywhere</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
                                <select class="selectpicker" data-style="btn-white btn-lg" data-width="100%" title="Select Job Type" disabled>
                                    <option>Full Time</option>
                                    <option>Part Time</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-4 mb-lg-0">
                                <button type="submit" class="btn btn-primary btn-lg btn-block text-white btn-search"><span class="icon-lock_outline icon mr-2"></span>Login To Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <a href="#next" class="scroll-button smoothscroll">
            <span class="icon-keyboard_arrow_down"></span>
        </a>
    </section>

    <section class="py-5 bg-image overlay-primary fixed overlay" id="next" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');">
        <div class="container">
            <div class="row mb-5 justify-content-center">
                <div class="col-md-7 text-center">
                    <h2 class="section-title mb-2 text-white">Platform Stats</h2>
                    <p class="lead text-white">A unified workspace for candidate growth and recruiter hiring.</p>
                </div>
            </div>
            <div class="row pb-0 block__19738 section-counter">
                <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
                    <div class="d-flex align-items-center justify-content-center mb-2"><strong class="number" data-number="1900">0</strong></div>
                    <span class="caption">Candidates</span>
                </div>
                <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
                    <div class="d-flex align-items-center justify-content-center mb-2"><strong class="number" data-number="450">0</strong></div>
                    <span class="caption">Jobs Posted</span>
                </div>
                <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
                    <div class="d-flex align-items-center justify-content-center mb-2"><strong class="number" data-number="320">0</strong></div>
                    <span class="caption">Interviews Booked</span>
                </div>
                <div class="col-6 col-md-6 col-lg-3 mb-5 mb-lg-0">
                    <div class="d-flex align-items-center justify-content-center mb-2"><strong class="number" data-number="120">0</strong></div>
                    <span class="caption">Recruiters</span>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section">
        <div class="container">
            <div class="row mb-5 justify-content-center">
                <div class="col-md-7 text-center">
                    <h2 class="section-title mb-2">Featured Roles</h2>
                    <p>Sign in to view complete listings, AI match score, and application status.</p>
                </div>
            </div>

            <ul class="job-listings mb-5">
                <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center">
                    <a href="<?= base_url('login') ?>"></a>
                    <div class="job-listing-logo">
                        <img src="<?= base_url('jobboard/images/job_logo_1.jpg') ?>" alt="Job" class="img-fluid">
                    </div>
                    <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
                        <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
                            <h2>Frontend Developer</h2>
                            <strong>Product Team</strong>
                        </div>
                        <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
                            <span class="icon-room"></span> Bangalore, India
                        </div>
                        <div class="job-listing-meta"><span class="badge badge-success">Full Time</span></div>
                    </div>
                </li>

                <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center">
                    <a href="<?= base_url('login') ?>"></a>
                    <div class="job-listing-logo">
                        <img src="<?= base_url('jobboard/images/job_logo_2.jpg') ?>" alt="Job" class="img-fluid">
                    </div>
                    <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
                        <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
                            <h2>DevOps Engineer</h2>
                            <strong>Cloud Team</strong>
                        </div>
                        <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
                            <span class="icon-room"></span> Hyderabad, India
                        </div>
                        <div class="job-listing-meta"><span class="badge badge-danger">Remote</span></div>
                    </div>
                </li>

                <li class="job-listing d-block d-sm-flex pb-3 pb-sm-0 align-items-center">
                    <a href="<?= base_url('login') ?>"></a>
                    <div class="job-listing-logo">
                        <img src="<?= base_url('jobboard/images/job_logo_3.jpg') ?>" alt="Job" class="img-fluid">
                    </div>
                    <div class="job-listing-about d-sm-flex custom-width w-100 justify-content-between mx-4">
                        <div class="job-listing-position custom-width w-50 mb-3 mb-sm-0">
                            <h2>Data Analyst</h2>
                            <strong>Analytics Team</strong>
                        </div>
                        <div class="job-listing-location mb-3 mb-sm-0 custom-width w-25">
                            <span class="icon-room"></span> Chennai, India
                        </div>
                        <div class="job-listing-meta"><span class="badge badge-warning">Hybrid</span></div>
                    </div>
                </li>
            </ul>

            <div class="row justify-content-center">
                <div class="col-md-7 text-center">
                    <a href="<?= base_url('register') ?>" class="btn btn-primary border-width-2 btn-lg mr-2">Create Candidate Account</a>
                    <a href="<?= base_url('recruiter/register') ?>" class="btn btn-outline-primary border-width-2 btn-lg">Join as Recruiter</a>
                </div>
            </div>
        </div>
    </section>

    <footer class="site-footer">
        <a href="#top" class="smoothscroll scroll-top">
            <span class="icon-keyboard_arrow_up"></span>
        </a>

        <div class="container">
            <div class="row mb-5">
                <div class="col-6 col-md-3 mb-4 mb-md-0">
                    <h3>Candidate</h3>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('register') ?>">Register</a></li>
                        <li><a href="<?= base_url('login') ?>">Login</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-3 mb-4 mb-md-0">
                    <h3>Recruiter</h3>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('recruiter/register') ?>">Register</a></li>
                        <li><a href="<?= base_url('login') ?>">Login</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-3 mb-4 mb-md-0">
                    <h3>Platform</h3>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('/') ?>">Home</a></li>
                        <li><a href="<?= base_url('login') ?>">Jobs</a></li>
                    </ul>
                </div>
                <div class="col-6 col-md-3 mb-4 mb-md-0">
                    <h3>Account</h3>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('login') ?>">Sign In</a></li>
                    </ul>
                </div>
            </div>

            <div class="row text-center">
                <div class="col-12">
                    <p class="copyright"><small>Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved</small></p>
                </div>
            </div>
        </div>
    </footer>
</div>

<script src="<?= base_url('jobboard/js/jquery.min.js') ?>"></script>
<script src="<?= base_url('jobboard/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('jobboard/js/isotope.pkgd.min.js') ?>"></script>
<script src="<?= base_url('jobboard/js/stickyfill.min.js') ?>"></script>
<script src="<?= base_url('jobboard/js/jquery.fancybox.min.js') ?>"></script>
<script src="<?= base_url('jobboard/js/jquery.easing.1.3.js') ?>"></script>
<script src="<?= base_url('jobboard/js/jquery.waypoints.min.js') ?>"></script>
<script src="<?= base_url('jobboard/js/jquery.animateNumber.min.js') ?>"></script>
<script src="<?= base_url('jobboard/js/owl.carousel.min.js') ?>"></script>
<script src="<?= base_url('jobboard/js/bootstrap-select.min.js') ?>"></script>
<script src="<?= base_url('jobboard/js/custom.js') ?>"></script>
</body>
</html>
