<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Forgot Password | HireMatrix</title>

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
<div class="site-wrap">
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <h1 class="text-white font-weight-bold">Forgot Password</h1>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('/') ?>">Home</a> <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong>Forgot Password</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h2 class="mb-3">Reset your password</h2>
                    <p class="text-muted mb-4">Enter your account email and we will send you a reset link.</p>

                    <form method="post" action="<?= base_url('forgot-password') ?>" class="p-4 border rounded bg-white">
                        <?= csrf_field() ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                        <?php endif; ?>

                        <?php $mailPreview = session()->getFlashdata('mail_preview'); ?>
                        <?php if (!empty($mailPreview['url'])): ?>
                            <div class="alert alert-info">
                                <strong><?= esc($mailPreview['title'] ?? 'Email preview') ?>:</strong>
                                testing mode is enabled, so no real email was sent.<br>
                                Recipient: <code><?= esc($mailPreview['recipient'] ?? '') ?></code><br>
                                Link: <a href="<?= esc($mailPreview['url']) ?>"><?= esc($mailPreview['url']) ?></a>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-control" value="<?= esc(old('email')) ?>" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Send reset link</button>
                    </form>

                    <p class="mt-3 mb-0"><a href="<?= base_url('login') ?>">Back to login</a></p>
                </div>
            </div>
        </div>
    </section>
</div>
</body>
</html>
