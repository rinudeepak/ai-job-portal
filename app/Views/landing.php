<?php
$platformStats = $platformStats ?? [];
$featuredJobs = $featuredJobs ?? [];

$jobsPostedCount = (int) ($platformStats['jobs_posted'] ?? count($featuredJobs));
$candidateCount = (int) ($platformStats['candidates'] ?? 0);
$interviewCount = (int) ($platformStats['interviews_booked'] ?? 0);
$recruiterCount = (int) ($platformStats['recruiters'] ?? 0);

$jobIconSet = [
    'developer' => 'fas fa-code',
    'engineer' => 'fas fa-cogs',
    'designer' => 'fas fa-palette',
    'manager' => 'fas fa-chart-line',
    'data' => 'fas fa-database',
    'marketing' => 'fas fa-bullhorn',
    'product' => 'fas fa-briefcase',
];

$pickJobIcon = static function (string $title) use ($jobIconSet): string {
    $needle = strtolower($title);
    foreach ($jobIconSet as $key => $icon) {
        if (str_contains($needle, $key)) {
            return $icon;
        }
    }

    return 'fas fa-briefcase';
};

$formatAge = static function ($value): string {
    if ($value === null || $value === '') {
        return 'Recently';
    }

    $date = strtotime((string) $value);
    if ($date === false) {
        return 'Recently';
    }

    return date('M d, Y', $date);
};

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>HireMatrix | Home</title>
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
    <link rel="stylesheet" href="<?= base_url('jobboard/css/hirematrix-style.css?v=' . @filemtime(FCPATH . 'jobboard/css/hirematrix-style.css')) ?>">
</head>
<body id="top" class="hirematrix-app landing-page">
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

    <header class="site-navbar site-navbar-target">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="site-logo col-6 col-xl-2">
                    <a href="<?= base_url('/') ?>" class="d-inline-flex align-items-center">
                        <img src="<?= base_url('jobboard/images/Serp Hwak Logo.png') ?>" alt="HireMatrix Logo" style="height: 34px; width: auto; margin-right: 8px;">
                        <span style="text-transform: none;">HireMatrix</span>
                    </a>
                </div>
                <nav class="mx-auto site-navigation col-xl-7">
                    <ul class="site-menu js-clone-nav d-none d-xl-block ml-0 pl-0">
                        <li><a class="active" href="<?= base_url('/') ?>">Home</a></li>
                        <li><a href="<?= base_url('login') ?>">Browse Jobs</a></li>
                        <li><a href="<?= base_url('login') ?>">Career Transition AI</a></li>
                    </ul>
                </nav>
                <div class="right-cta-menu text-right d-flex justify-content-end align-items-center col-6 col-xl-3">
                    <a href="<?= base_url('recruiter/register') ?>" class="btn btn-outline-secondary btn-sm me-2">
                        <span class="d-none d-lg-inline ms-1">Recruiter</span>
                    </a>
                    <a href="<?= base_url('register') ?>" class="btn btn-ghost btn-sm me-2">Candidate</a>
                    <a href="<?= base_url('login') ?>" class="btn btn-primary btn-sm">Log In</a>
                    <a href="#" class="site-menu-toggle js-menu-toggle d-inline-block d-xl-none mt-lg-2 ml-3">
                        <span class="icon-menu h3 m-0 p-0 mt-2"></span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <section class="hero py-5">
        <div class="container">
            <div class="status-pill">
                <i class="fas fa-arrow-trend-up" style="color: var(--primary);"></i>
                <?= max(1, $jobsPostedCount) ?>+ Active Jobs Available
            </div>

            <h1 class="hero-title">
                Find Your
                <span class="gradient-text">Dream Job</span>
                Today
            </h1>

            <p class="hero-subtitle">
                Connect with top companies and discover opportunities that match your skills.
                AI-powered recommendations to fast-track your career.
            </p>

            <div class="card mb-4" style="max-width: 800px;">
                <div class="card-body p-3 p-md-4">
                    <form action="<?= base_url('jobs') ?>" method="get">
                        <div class="row g-3 g-md-2">
                            <div class="col-12 col-md-6 col-lg-5">
                                <div class="search-input-group">
                                    <i class="fas fa-search" style="color: var(--muted-foreground);"></i>
                                    <input type="text" name="search" placeholder="Job title, skills, or company" class="form-control border-0">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="search-input-group">
                                    <i class="fas fa-map-pin" style="color: var(--muted-foreground);"></i>
                                    <input type="text" name="location" placeholder="City or location" class="form-control border-0">
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <button class="btn btn-primary w-100" type="submit">Search Jobs</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="mb-5">
                <span class="text-muted me-3" style="font-size: 0.875rem; font-weight: 500;">Popular:</span>
                <div class="btn-group" role="group">
                    <a class="btn btn-outline-primary btn-sm" href="<?= base_url('jobs?search=developer') ?>" style="border-width: 2px;">Developer</a>
                    <a class="btn btn-sm" href="<?= base_url('jobs?search=designer') ?>" style="background: rgba(255, 123, 42, 0.2); color: var(--secondary); border: none;">Designer</a>
                    <a class="btn btn-sm" href="<?= base_url('jobs?search=marketing') ?>" style="background: rgba(0, 191, 165, 0.2); color: var(--accent); border: none;">Marketing</a>
                    <a class="btn btn-sm" href="<?= base_url('jobs?location=remote') ?>" style="background: rgba(59, 130, 246, 0.2); color: var(--primary); border: none;">Remote</a>
                    <a class="btn btn-sm" href="<?= base_url('jobs?employment_type=full-time') ?>" style="background: rgba(255, 123, 42, 0.2); color: var(--secondary); border: none;">Full-time</a>
                </div>
            </div>

            <section class="landing-career-transition">
                <div class="landing-career-transition-inner">
                    <div class="landing-career-transition-copy">
                        <div class="landing-career-transition-kicker">
                            <i class="fas fa-sparkles"></i>
                            Career Transition AI
                        </div>
                        <h2 class="landing-career-transition-title">Career Transition AI</h2>
                        <p class="landing-career-transition-text">
                            We analyze your current skill set and generate a focused roadmap for your target role.
                            Start your career transition journey today!
                        </p>
                        <a href="<?= base_url('career-transition') ?>" class="btn btn-light landing-career-transition-btn">
                            Generate Roadmap <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                    <div class="landing-career-transition-art d-none d-lg-flex" aria-hidden="true">
                        <div class="landing-career-transition-orb">
                            <i class="fas fa-sparkles"></i>
                        </div>
                    </div>
                </div>
            </section>

            <p class="text-center text-muted" style="font-size: 0.875rem;">
                Sign in to view complete listings, AI match score, and application status.
            </p>
        </div>
    </section>

    <section class="py-5" id="jobs">
        <div class="container">
            <div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3">
                <div>
                    <div class="ai-badge">
                        <i class="fas fa-sparkles"></i>
                        Live Open Roles
                    </div>
                    <h2 class="section-title">Featured Jobs</h2>
                    <p class="section-subtitle">Live openings pulled from the database. Sign in to get personalized matching.</p>
                </div>
                <a href="<?= base_url('jobs') ?>" class="btn btn-ghost landing-view-all-link">View all jobs <i class="fas fa-arrow-right ms-2"></i></a>
            </div>

            <div class="landing-featured-jobs-grid mb-4">
                <?php if (!empty($featuredJobs)): ?>
                    <?php foreach (array_slice($featuredJobs, 0, 6) as $job): ?>
                        <?php
                        $title = (string) ($job['title'] ?? 'Untitled Role');
                        $company = trim((string) ($job['company'] ?? 'Company'));
                        $location = trim((string) ($job['location'] ?? 'N/A'));
                        $postedAt = $formatAge($job['created_at'] ?? $job['posted_at'] ?? null);
                        $matchScore = (int) round((float) ($job['match_score'] ?? 85));
                        ?>
                        <div class="landing-featured-jobs-item">
                            <div class="job-card">
                                <div class="job-card-icon"><i class="<?= esc($pickJobIcon($title)) ?>"></i></div>
                                <h3 class="job-card-title"><?= esc($title) ?></h3>
                                <p class="job-card-company"><?= esc($company) ?></p>
                                <div class="job-card-meta">
                                    <span><i class="fas fa-map-pin"></i> <?= esc($location) ?></span>
                                    <span><i class="fas fa-clock"></i> <?= esc($postedAt) ?></span>
                                </div>
                                <div class="job-card-tags">
                                    <span class="badge badge-primary">Full-time</span>
                                    <span class="badge badge-secondary"><?= esc(substr($title, 0, 15) ?: 'Role') ?></span>
                                </div>
                                <div class="progress-container">
                                    <div class="progress-bar-custom" style="width: <?= max(10, min(100, $matchScore)) ?>%;"></div>
                                    <span class="progress-label"><?= max(10, min(100, $matchScore)) ?>%</span>
                                </div>
                                <a href="<?= base_url('login') ?>" class="view-details">View Details &rarr;</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="landing-featured-jobs-item">
                        <div class="job-card">
                            <div class="job-card-icon"><i class="fas fa-robot"></i></div>
                            <h3 class="job-card-title">Data Scientist</h3>
                            <p class="job-card-company">AI Dynamics</p>
                            <div class="job-card-meta">
                                <span><i class="fas fa-map-pin"></i> Boston, MA</span>
                                <span><i class="fas fa-clock"></i> 2 days ago</span>
                            </div>
                            <div class="job-card-tags">
                                <span class="badge badge-primary">Full-time</span>
                                <span class="badge badge-secondary">Python</span>
                            </div>
                            <div class="progress-container">
                                <div class="progress-bar-custom" style="width: 88%;"></div>
                                <span class="progress-label">88%</span>
                            </div>
                            <a href="<?= base_url('login') ?>" class="view-details">View Details &rarr;</a>
                        </div>
                    </div>

                    <div class="landing-featured-jobs-item">
                        <div class="job-card">
                            <div class="job-card-icon"><i class="fas fa-pencil-ruler"></i></div>
                            <h3 class="job-card-title">UI/UX Designer</h3>
                            <p class="job-card-company">Design Studio Pro</p>
                            <div class="job-card-meta">
                                <span><i class="fas fa-map-pin"></i> Los Angeles, CA</span>
                                <span><i class="fas fa-clock"></i> 4 days ago</span>
                            </div>
                            <div class="job-card-tags">
                                <span class="badge badge-primary">Remote</span>
                                <span class="badge badge-secondary">Design</span>
                            </div>
                            <div class="progress-container">
                                <div class="progress-bar-custom" style="width: 91%;"></div>
                                <span class="progress-label">91%</span>
                            </div>
                            <a href="<?= base_url('login') ?>" class="view-details">View Details &rarr;</a>
                        </div>
                    </div>

                    <div class="landing-featured-jobs-item">
                        <div class="job-card">
                            <div class="job-card-icon"><i class="fas fa-code"></i></div>
                            <h3 class="job-card-title">Backend Engineer</h3>
                            <p class="job-card-company">Cloud Systems Inc</p>
                            <div class="job-card-meta">
                                <span><i class="fas fa-map-pin"></i> Seattle, WA</span>
                                <span><i class="fas fa-clock"></i> 1 day ago</span>
                            </div>
                            <div class="job-card-tags">
                                <span class="badge badge-primary">Full-time</span>
                                <span class="badge badge-secondary">Node.js</span>
                            </div>
                            <div class="progress-container">
                                <div class="progress-bar-custom" style="width: 86%;"></div>
                                <span class="progress-label">86%</span>
                            </div>
                            <a href="<?= base_url('login') ?>" class="view-details">View Details &rarr;</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <p class="text-center text-muted" style="font-size: 0.875rem;">
                Sign in to see personalized match scores, saved jobs, and application status.
            </p>
        </div>
    </section>

    <section class="landing-get-started" id="get-started">
        <div class="container">
            <div class="text-center landing-get-started-head">
                <h2 class="landing-get-started-title">Get Started Today</h2>
                <p class="landing-get-started-subtitle">
                    Whether you're looking for your next opportunity or searching for top talent, HireMatrix has you covered.
                </p>
            </div>

            <div class="row g-4 justify-content-center landing-get-started-grid">
                <div class="col-lg-5">
                    <div class="landing-start-card landing-start-card-candidate h-100">
                        <div class="landing-start-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>For Job Seekers</h3>
                        <p>Discover opportunities tailored to your skills and career goals.</p>
                        <ul class="landing-start-list">
                            <li><i class="fas fa-check"></i> AI-powered job recommendations</li>
                            <li><i class="fas fa-check"></i> Skill gap analysis</li>
                            <li><i class="fas fa-check"></i> Career transition tools</li>
                        </ul>
                        <a href="<?= base_url('register') ?>" class="btn btn-primary landing-start-btn">
                            Create Candidate Account <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="landing-start-card landing-start-card-recruiter h-100">
                        <div class="landing-start-icon landing-start-icon-recruiter">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <h3>For Recruiters</h3>
                        <p>Find and connect with the best talent for your organization.</p>
                        <ul class="landing-start-list landing-start-list-recruiter">
                            <li><i class="fas fa-check"></i> Smart candidate matching</li>
                            <li><i class="fas fa-check"></i> ATS integration</li>
                            <li><i class="fas fa-check"></i> Team collaboration tools</li>
                        </ul>
                        <a href="<?= base_url('recruiter/register') ?>" class="btn btn-primary landing-start-btn landing-start-btn-recruiter">
                            Join as Recruiter <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer mt-5">
        <div class="container">
            <div class="row g-5 mb-5">
                <div class="col-md-3">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <img src="<?= base_url('jobboard/images/Serp Hwak Logo.png') ?>" alt="HireMatrix Logo" style="height: 40px; width: auto;">
                        <span style="font-weight: 700; font-size: 1.125rem;">HireMatrix</span>
                    </div>
                    <p style="font-size: 0.875rem; opacity: 0.8;">
                        Connecting talent with opportunities through AI-powered recommendations.
                    </p>
                </div>

                <div class="footer-section col-md-3">
                    <h3>For Job Seekers</h3>
                    <a href="<?= base_url('jobs') ?>">Browse Jobs</a>
                    <a href="<?= base_url('/#get-started') ?>">Get Started</a>
                    <a href="<?= base_url('register') ?>">Create Candidate Account</a>
                </div>

                <div class="footer-section col-md-3">
                    <h3>For Recruiters</h3>
                    <a href="<?= base_url('recruiter/register') ?>">Join as Recruiter</a>
                    <a href="<?= base_url('login') ?>">Sign In</a>
                </div>

            </div>

            <div class="footer-bottom">
                <div class="footer-social">
                    <a href="#" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    <a href="#" title="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" title="Facebook"><i class="fab fa-facebook"></i></a>
                </div>
                <p>&copy; <?= date('Y') ?> HireMatrix. All rights reserved.</p>
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
