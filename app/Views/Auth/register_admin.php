<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Recruiter Registration | HireMatrix</title>

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
    <link rel="stylesheet" href="<?= base_url('custom/public-pages.css?v=' . @filemtime(FCPATH . 'custom/public-pages.css')) ?>">
</head>
<?= view('Layouts/public_header', ['body_class' => 'public-auth-page']) ?>

  <section class="auth-page-shell">
    <div class="auth-page-column auth-page-column--md">
      <div class="auth-page-head">
        <div class="auth-page-brand">
          <img src="<?= base_url('jobboard/images/Serp Hwak Logo.png') ?>" alt="HireMatrix Logo">
          <span class="auth-page-brand-text">HireMatrix</span>
        </div>
        <h1 class="auth-page-title">Create Recruiter Account</h1>
        <p class="auth-page-subtitle">Create your recruiter account to post jobs and manage applications.</p>
      </div>

      <div class="card rounded-5 border-1 auth-page-card">
        <div class="card-body p-4 p-md-5">
          <?php if (session()->getFlashdata('error')) : ?>
              <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
          <?php endif; ?>

          <form method="post" action="<?= base_url('recruiter/register') ?>" class="auth-form">
              <?= csrf_field() ?>

              <div>
                <label class="form-label auth-field-label">Company Name</label>
                <div class="auth-field-wrap">
                  <i class="fas fa-building auth-field-icon"></i>
                  <input type="text" id="company_name" name="company_name" placeholder="Your company name" class="form-control auth-input" value="<?= old('company_name') ?>" required>
                </div>
              </div>

              <div>
                <label class="form-label auth-field-label">Recruiter Name</label>
                <div class="auth-field-wrap">
                  <i class="fas fa-user auth-field-icon"></i>
                  <input type="text" id="name" name="name" placeholder="Your full name" class="form-control auth-input" value="<?= old('name') ?>" required>
                </div>
                <?php if (session('validation') && session('validation')->hasError('name')): ?>
                    <small class="text-danger"><?= session('validation')->getError('name') ?></small>
                <?php endif; ?>
              </div>

              <div>
                <label class="form-label auth-field-label">Designation</label>
                <div class="auth-field-wrap">
                  <i class="fas fa-id-badge auth-field-icon"></i>
                  <input type="text" id="designation" name="designation" placeholder="e.g., Talent Acquisition Specialist" class="form-control auth-input" value="<?= old('designation') ?>" required>
                </div>
                <?php if (session('validation') && session('validation')->hasError('designation')): ?>
                    <small class="text-danger"><?= session('validation')->getError('designation') ?></small>
                <?php endif; ?>
              </div>

              <div>
                <label class="form-label auth-field-label">Email Address</label>
                <div class="auth-field-wrap">
                  <i class="fas fa-envelope auth-field-icon"></i>
                  <input type="email" id="email" name="email" placeholder="you@company.com" class="form-control auth-input" value="<?= old('email') ?>" required>
                </div>
                <small class="text-muted">Use company domain email only (free providers are blocked).</small>
                <?php if (session('validation') && session('validation')->hasError('email')): ?>
                    <small class="text-danger"><?= session('validation')->getError('email') ?></small>
                <?php endif; ?>
              </div>

              <div>
                <label class="form-label auth-field-label">Phone Number</label>
                <div class="auth-field-wrap">
                  <i class="fas fa-phone auth-field-icon"></i>
                  <input type="text" id="phone" name="phone" placeholder="Phone number" class="form-control auth-input" value="<?= old('phone') ?>" required>
                </div>
                <?php if (session('validation') && session('validation')->hasError('phone')): ?>
                    <small class="text-danger"><?= session('validation')->getError('phone') ?></small>
                <?php endif; ?>
              </div>

              <div>
                <label class="form-label auth-field-label">Password</label>
                <div class="auth-field-wrap">
                  <i class="fas fa-lock auth-field-icon"></i>
                  <input type="password" id="passwordInput" name="password" placeholder="Password" class="form-control auth-input auth-input--password" required>
                  <button type="button" class="auth-password-toggle" data-password-target="passwordInput">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
                <?php if (session('validation') && session('validation')->hasError('password')): ?>
                    <small class="text-danger"><?= session('validation')->getError('password') ?></small>
                <?php endif; ?>
              </div>

              <div class="mb-1">
                <label class="form-label auth-field-label">Re-Type Password</label>
                <div class="auth-field-wrap">
                  <i class="fas fa-lock auth-field-icon"></i>
                  <input type="password" id="confirmPasswordInput" name="confirm_password" placeholder="Password" class="form-control auth-input auth-input--password" required>
                  <button type="button" class="auth-password-toggle" data-password-target="confirmPasswordInput">
                    <i class="fas fa-eye"></i>
                  </button>
                </div>
                <?php if (session('validation') && session('validation')->hasError('confirm_password')): ?>
                    <small class="text-danger"><?= session('validation')->getError('confirm_password') ?></small>
                <?php endif; ?>
              </div>

              <button type="submit" class="btn btn-primary btn-lg auth-secondary-btn">
                Register as Recruiter
              </button>
          </form>
        </div>
      </div>

      <div class="auth-footer-copy">
        <p>
          Already have an account?
          <a href="<?= base_url('login') ?>" class="auth-footer-link">Login</a>
        </p>
      </div>
    </div>
  </section>

<?= view('Layouts/public_footer') ?>
</body>
</html>
