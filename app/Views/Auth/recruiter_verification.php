<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Recruiter Verification | HireMatrix</title>

    <link rel="stylesheet" href="<?= base_url('jobboard/css/custom-bs.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/style.css') ?>">
</head>
<body>
<div class="site-wrap">
    <section class="site-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <h2 class="mb-4">Recruiter Verification</h2>
                    <p class="text-muted">Complete phone OTP verification to activate recruiter access.</p>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <?php if (!($isPhoneVerified ?? false)): ?>
                        <div class="card">
                            <div class="card-body">
                                <h5>Step 1: Phone OTP Verification</h5>
                                <form method="post" action="<?= base_url('recruiter/verify-phone') ?>">
                                    <?= csrf_field() ?>
                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <?php
                                        $rawPhone = (string) ($phone ?? '');
                                        $maskedPhone = $rawPhone !== ''
                                            ? str_repeat('*', max(strlen($rawPhone) - 4, 0)) . substr($rawPhone, -4)
                                            : 'Not available';
                                        ?>
                                        <input type="text" class="form-control" value="<?= esc($maskedPhone) ?>" readonly>
                                        <input type="hidden" name="email" value="<?= esc($email ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>OTP</label>
                                        <input type="text" name="otp" class="form-control" maxlength="6" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Verify Phone OTP</button>
                                </form>
                                <form method="post" action="<?= base_url('recruiter/resend-phone-otp') ?>" class="mt-3">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="email" value="<?= esc($email ?? '') ?>">
                                    <button type="submit" class="btn btn-outline-primary btn-sm">Resend OTP</button>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-body">
                                <h5>Verification Complete</h5>
                                <p class="mb-3 text-success">Phone verification is complete.</p>
                                <a href="<?= base_url('login') ?>" class="btn btn-primary">Go to Login</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <p class="mt-3"><a href="<?= base_url('login') ?>">Back to login</a></p>
                </div>
            </div>
        </div>
    </section>
</div>
</body>
</html>
