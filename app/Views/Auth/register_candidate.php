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
    <link rel="stylesheet" href="<?= base_url('jobboard/css/hirematrix-style.css?v=' . @filemtime(FCPATH . 'jobboard/css/hirematrix-style.css')) ?>">
</head>
<body style="background: var(--background);" class="hirematrix-app">
  <nav class="navbar navbar-expand-lg navbar-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="<?= base_url('/') ?>">
        <img src="<?= base_url('jobboard/images/Serp Hwak Logo.png') ?>" alt="HireMatrix Logo" style="height: 40px; width: auto;">
        <span class="d-none d-sm-inline">HireMatrix</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('/') ?>">For Job Seekers</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('recruiter/register') ?>">For Recruiters</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <section style="min-height: calc(100vh - 160px); display: flex; align-items: center; justify-content: center; padding: 3rem 1rem;">
    <div style="width: 100%; max-width: 480px;">
      <div style="text-align: center; margin-bottom: 2rem;">
        <div style="display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
          <img src="<?= base_url('jobboard/images/Serp Hwak Logo.png') ?>" alt="HireMatrix Logo" style="height: 48px; width: auto;">
          <span style="font-weight: 700; font-size: 1.5rem; color: var(--foreground);">HireMatrix</span>
        </div>
        <h1 style="font-size: 1.875rem; font-weight: 700; color: var(--foreground); margin-bottom: 0.5rem;">Create Candidate Account</h1>
        <p style="color: var(--muted-foreground); font-size: 0.875rem;">Join HireMatrix and build your profile to start applying for jobs.</p>
      </div>

      <div class="card rounded-5 border-1" style="margin-bottom: 1.5rem;">
        <div class="card-body p-4 p-md-5">
          <?php if (session()->getFlashdata('error')): ?>
              <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
          <?php endif; ?>

          <a href="<?= base_url('auth/google') ?>" class="btn btn-google-auth btn-block mb-3">
              <span class="google-g-icon" aria-hidden="true">
                  <svg viewBox="0 0 18 18" width="18" height="18" xmlns="http://www.w3.org/2000/svg">
                      <path fill="#4285F4" d="M17.64 9.2c0-.64-.06-1.25-.16-1.84H9v3.48h4.84a4.14 4.14 0 0 1-1.8 2.72v2.26h2.92c1.7-1.56 2.68-3.86 2.68-6.62z"/>
                      <path fill="#34A853" d="M9 18c2.43 0 4.46-.8 5.95-2.18l-2.92-2.26c-.81.54-1.84.86-3.03.86-2.33 0-4.3-1.57-5-3.68H1v2.31A9 9 0 0 0 9 18z"/>
                      <path fill="#FBBC05" d="M4 10.74a5.41 5.41 0 0 1 0-3.48V4.95H1a9 9 0 0 0 0 8.1l3-2.31z"/>
                      <path fill="#EA4335" d="M9 3.58c1.32 0 2.5.45 3.43 1.34l2.57-2.57A8.98 8.98 0 0 0 1 4.95l3 2.31c.7-2.11 2.67-3.68 5-3.68z"/>
                  </svg>
              </span>
              <span>Continue with Google</span>
          </a>

          <div style="display: flex; align-items: center; gap: 1rem; margin: 1.25rem 0;">
            <div style="flex: 1; height: 1px; background: var(--border);"></div>
            <span style="font-size: 0.75rem; color: var(--muted-foreground);">OR</span>
            <div style="flex: 1; height: 1px; background: var(--border);"></div>
          </div>

          <form method="post" action="<?= base_url('register') ?>" style="display: flex; flex-direction: column; gap: 1rem;">
              <?= csrf_field() ?>

              <?php if (session()->getFlashdata('validation_error')): ?>
                  <div class="alert alert-danger"><?= esc(session()->getFlashdata('validation_error')) ?></div>
              <?php endif; ?>

              <div>
                <label class="form-label" style="font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">Full Name</label>
                <div style="position: relative;">
                  <i class="fas fa-user" style="position: absolute; left: 1rem; top: 0.875rem; color: var(--muted-foreground); pointer-events: none;"></i>
                  <input type="text" id="name" name="name" placeholder="Your full name" class="form-control" style="padding-left: 2.5rem;" value="<?= old('name') ?>" required>
                </div>
                <?php if (session('validation') && session('validation')->hasError('name')): ?>
                    <small class="text-danger"><?= session('validation')->getError('name') ?></small>
                <?php endif; ?>
              </div>

              <div>
                <label class="form-label" style="font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">Email Address</label>
                <div style="position: relative;">
                  <i class="fas fa-envelope" style="position: absolute; left: 1rem; top: 0.875rem; color: var(--muted-foreground); pointer-events: none;"></i>
                  <input type="email" id="email" name="email" placeholder="your@email.com" class="form-control" style="padding-left: 2.5rem;" value="<?= old('email') ?>" required>
                </div>
                <?php if (session('validation') && session('validation')->hasError('email')): ?>
                    <small class="text-danger"><?= session('validation')->getError('email') ?></small>
                <?php endif; ?>
              </div>

              <div>
                <label class="form-label" style="font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">Phone</label>
                <div style="position: relative;">
                  <i class="fas fa-phone" style="position: absolute; left: 1rem; top: 0.875rem; color: var(--muted-foreground); pointer-events: none;"></i>
                  <input type="tel" id="phone" name="phone" placeholder="Your phone number" class="form-control" style="padding-left: 2.5rem;" value="<?= old('phone') ?>" required>
                </div>
                <?php if (session('validation') && session('validation')->hasError('phone')): ?>
                    <small class="text-danger"><?= session('validation')->getError('phone') ?></small>
                <?php endif; ?>
              </div>

              <div>
                <label class="form-label" style="font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">Password</label>
                <div style="position: relative;">
                  <i class="fas fa-lock" style="position: absolute; left: 1rem; top: 0.875rem; color: var(--muted-foreground); pointer-events: none;"></i>
                  <input type="password" id="passwordInput" name="password" placeholder="••••••••" class="form-control" style="padding-left: 2.5rem; padding-right: 2.5rem;" required>
                  <button type="button" onclick="togglePassword('passwordInput', this)" style="position: absolute; right: 1rem; top: 0.875rem; background: transparent; border: none; color: var(--muted-foreground); cursor: pointer;">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
                <?php if (session('validation') && session('validation')->hasError('password')): ?>
                    <small class="text-danger"><?= session('validation')->getError('password') ?></small>
                <?php endif; ?>
              </div>

              <div class="mb-1">
                <label class="form-label" style="font-weight: 600; font-size: 0.875rem; margin-bottom: 0.5rem;">Re-Type Password</label>
                <div style="position: relative;">
                  <i class="fas fa-lock" style="position: absolute; left: 1rem; top: 0.875rem; color: var(--muted-foreground); pointer-events: none;"></i>
                  <input type="password" id="confirmPasswordInput" name="confirm_password" placeholder="••••••••" class="form-control" style="padding-left: 2.5rem; padding-right: 2.5rem;" required>
                  <button type="button" onclick="togglePassword('confirmPasswordInput', this)" style="position: absolute; right: 1rem; top: 0.875rem; background: transparent; border: none; color: var(--muted-foreground); cursor: pointer;">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
                <?php if (session('validation') && session('validation')->hasError('confirm_password')): ?>
                    <small class="text-danger"><?= session('validation')->getError('confirm_password') ?></small>
                <?php endif; ?>
              </div>

              <button type="submit" class="btn btn-primary btn-lg" style="margin-top: 0.5rem; width: 100%;">
                Sign Up
              </button>
          </form>
        </div>
      </div>

      <div style="text-align: center;">
        <p style="color: var(--muted-foreground); font-size: 0.875rem;">
          Already have an account?
          <a href="<?= base_url('login') ?>" style="color: var(--primary); text-decoration: none; font-weight: 600;">Login</a>
        </p>
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
          <h3>For Employers</h3>
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
        <p>© 2026 HireMatrix. All rights reserved.</p>
      </div>
    </div>
  </footer>

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
      const icon = button.querySelector('i');
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      if (icon) {
        icon.classList.toggle('fa-eye', !show);
        icon.classList.toggle('fa-eye-slash', show);
      }
    }
  </script>
</body>
</html>
