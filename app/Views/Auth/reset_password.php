<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Reset Password | HireMatrix</title>

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
<body id="top" class="hirematrix-app">
<div class="site-wrap">
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <h1 class="text-white font-weight-bold">Reset Password</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="site-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h2 class="mb-3">Choose a new password</h2>

                    <form method="post" action="<?= base_url('reset-password/' . $token) ?>" class="p-4 border rounded bg-white">
                        <?= csrf_field() ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>

                        <?php $validation = session()->getFlashdata('validation'); ?>
                        <?php if ($validation): ?>
                            <div class="alert alert-danger"><?= $validation->listErrors() ?></div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="password">New Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Reset password</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
</body>
</html>
