<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Recruiter Verification | HireMatrix</title>

    <link rel="stylesheet" href="<?= base_url('jobboard/css/custom-bs.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/hirematrix-style.css?v=' . @filemtime(FCPATH . 'jobboard/css/hirematrix-style.css')) ?>">
</head>
<body class="hirematrix-app public-auth-page">
<div class="site-wrap">
    <section class="site-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6" id="recruiterVerificationApp"
                     data-email="<?= esc($email ?? '', 'attr') ?>"
                     data-phone-e164="<?= esc($phoneE164 ?? '', 'attr') ?>"
                     data-firebase-configured="<?= !empty($firebaseConfigured) ? '1' : '0' ?>"
                     data-firebase-api-key="<?= esc((string) ($firebaseConfig['apiKey'] ?? ''), 'attr') ?>"
                     data-firebase-auth-domain="<?= esc((string) ($firebaseConfig['authDomain'] ?? ''), 'attr') ?>"
                     data-firebase-project-id="<?= esc((string) ($firebaseConfig['projectId'] ?? ''), 'attr') ?>"
                     data-firebase-app-id="<?= esc((string) ($firebaseConfig['appId'] ?? ''), 'attr') ?>"
                     data-firebase-messaging-sender-id="<?= esc((string) ($firebaseConfig['messagingSenderId'] ?? ''), 'attr') ?>">
                    <h2 class="mb-4">Recruiter Verification</h2>
                    <p class="text-muted">Complete company email verification first, then finish phone OTP verification to activate recruiter access.</p>
                    <p class="text-muted mb-3"><small>For local testing without billing, use Firebase Authentication test phone numbers.</small></p>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
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

                    <div class="card mb-3">
                        <div class="card-body">
                            <h5>Step 1: Company Email Verification</h5>
                            <div class="form-group">
                                <label>Email Address</label>
                                <input type="text" class="form-control" value="<?= esc($email ?? 'Not available') ?>" readonly>
                            </div>

                            <?php if ($isEmailVerified ?? false): ?>
                                <div class="alert alert-success mb-0">
                                    Company email verified successfully.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    Your recruiter account is waiting for email verification. Open the verification link sent to your inbox.
                                </div>
                                <form method="post" action="<?= base_url('recruiter/resend-verification-email') ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="email" value="<?= esc($email ?? '') ?>">
                                    <button type="submit" class="btn btn-outline-primary">Resend Verification Email</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (($isEmailVerified ?? false) && !($isPhoneVerified ?? false)): ?>
                        <div class="card">
                            <div class="card-body">
                                <h5>Step 2: Phone OTP Verification</h5>

                                <?php if (!($firebaseConfigured ?? false)): ?>
                                    <div class="alert alert-warning mb-3">
                                        Firebase is not configured. Add Firebase web config values in `.env` to enable phone OTP.
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="<?= base_url('recruiter/verify-phone') ?>" id="phoneVerifyForm">
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
                                        <input type="hidden" id="phoneE164" value="<?= esc($phoneE164 ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>OTP</label>
                                        <input type="text" id="otpCode" class="form-control" maxlength="6" autocomplete="one-time-code" placeholder="Enter 6-digit OTP" required>
                                    </div>

                                    <input type="hidden" name="firebase_id_token" id="firebaseIdToken">
                                    <button type="button" id="verifyOtpBtn" class="btn btn-primary">Verify OTP</button>

                                    <div id="recaptcha-container" class="d-none"></div>
                                    <p id="otpStatus" class="small mt-3 mb-0 text-muted"></p>
                                </form>
                            </div>
                        </div>
                    <?php elseif (!($isEmailVerified ?? false)): ?>
                        <div class="card">
                            <div class="card-body">
                                <h5>Step 2: Phone OTP Verification</h5>
                                <div class="alert alert-secondary mb-0">
                                    Phone OTP will be enabled after your company email is verified.
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-body">
                                <h5>Verification Complete</h5>
                                <p class="mb-3 text-success">Company email and phone verification are complete.</p>
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

<?php if (($isEmailVerified ?? false) && !($isPhoneVerified ?? false) && ($firebaseConfigured ?? false)): ?>
<script src="https://www.gstatic.com/firebasejs/10.12.5/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.5/firebase-auth-compat.js"></script>
<script src="<?= base_url('custom/recruiter-verification.js') ?>"></script>
<?php endif; ?>

</body>
</html>
