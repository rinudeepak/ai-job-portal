<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Candidate Registration | HireMatrix</title>

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
            <div class="site-mobile-menu-close mt-3"><span class="icon-close2 js-menu-toggle"></span></div>
        </div>
        <div class="site-mobile-menu-body"></div>
    </div>

    <header class="site-navbar mt-3">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="site-logo col-6"><a href="<?= base_url('/') ?>">HireMatrix</a></div>
                <nav class="mx-auto site-navigation">
                    <ul class="site-menu js-clone-nav d-none d-xl-block ml-0 pl-0">
                        <li><a href="<?= base_url('/') ?>">Home</a></li>
                        <li><a href="<?= base_url('register') ?>" class="nav-link active">Candidate Register</a></li>
                        <li><a href="<?= base_url('recruiter/register') ?>">Recruiter Register</a></li>
                        <li><a href="<?= base_url('login') ?>">Login</a></li>
                    </ul>
                </nav>
                <div class="right-cta-menu text-right d-flex aligin-items-center col-6">
                    <div class="ml-auto">
                        <a href="<?= base_url('register') ?>" class="btn btn-primary border-width-2 d-none d-lg-inline-block">Candidate</a>
                        <a href="<?= base_url('recruiter/register') ?>" class="btn btn-outline-white border-width-2 d-none d-lg-inline-block">Recruiter</a>
                        <a href="<?= base_url('login') ?>" class="btn btn-outline-white border-width-2 d-none d-lg-inline-block">Log In</a>
                    </div>
                    <a href="#" class="site-menu-toggle js-menu-toggle d-inline-block d-xl-none mt-lg-2 ml-3"><span class="icon-menu h3 m-0 p-0 mt-2"></span></a>
                </div>
            </div>
        </div>
    </header>

    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <h1 class="text-white font-weight-bold">Sign Up</h1>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('/') ?>">Home</a> <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong>Candidate Register</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section pt-0">
        <div class="container pt-4">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h2 class="mb-4">Sign Up To HireMatrix</h2>
                    <div class="mb-3">
                        <a href="<?= base_url('auth/google') ?>" class="btn btn-outline-danger btn-block">
                            <span class="fab fa-google mr-2"></span>Continue with Google
                        </a>
                    </div>
                    <div class="text-center text-muted mb-3"><small>or sign up with email</small></div>                    <form method="post" action="<?= base_url('register') ?>" class="p-4 border rounded bg-white">
                        <?= csrf_field() ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>

                        <div class="row form-group">
                            <div class="col-md-12">
                                <label class="text-black" for="name">Full Name</label>
                                <input type="text" id="name" name="name" class="form-control" value="<?= old('name') ?>" required>
                                <?php if (session('validation') && session('validation')->hasError('name')): ?>
                                    <small class="text-danger"><?= session('validation')->getError('name') ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-12">
                                <label class="text-black" for="email">Email Address</label>
                                <input type="email" id="email" name="email" class="form-control" value="<?= old('email') ?>" required>
                                <?php if (session('validation') && session('validation')->hasError('email')): ?>
                                    <small class="text-danger"><?= session('validation')->getError('email') ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-12">
                                <label class="text-black" for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone" class="form-control" value="<?= old('phone') ?>" required>
                                <?php if (session('validation') && session('validation')->hasError('phone')): ?>
                                    <small class="text-danger"><?= session('validation')->getError('phone') ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-12">
                                <label class="text-black" for="password">Password</label>
                                <div class="input-group">
                                    <input type="password" id="password" name="password" class="form-control" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password', this)">Show</button>
                                    </div>
                                </div>
                                <?php if (session('validation') && session('validation')->hasError('password')): ?>
                                    <small class="text-danger"><?= session('validation')->getError('password') ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row form-group mb-4">
                            <div class="col-md-12">
                                <label class="text-black" for="confirm_password">Re-Type Password</label>
                                <div class="input-group">
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('confirm_password', this)">Show</button>
                                    </div>
                                </div>
                                <?php if (session('validation') && session('validation')->hasError('confirm_password')): ?>
                                    <small class="text-danger"><?= session('validation')->getError('confirm_password') ?></small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row form-group">
                            <div class="col-md-12">
                                <button type="submit" class="btn px-4 btn-primary text-white">Sign Up</button>
                            </div>
                        </div>
                    </form>

                    <p class="mt-3">Already have an account? <a href="<?= base_url('login') ?>">Login</a></p>
                </div>
            </div>
        </div>
    </section>
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
<script>
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    button.textContent = show ? 'Hide' : 'Show';
}
</script>
</body>
</html>
