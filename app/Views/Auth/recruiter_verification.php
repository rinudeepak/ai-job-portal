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
                    <p class="text-muted">Complete Firebase phone verification to activate recruiter access.</p>
                    <p class="text-muted mb-3"><small>For local testing without billing, use Firebase Authentication test phone numbers.</small></p>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <?php if (!($isPhoneVerified ?? false)): ?>
                        <div class="card">
                            <div class="card-body">
                                <h5>Phone OTP Verification</h5>

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

                                    <div id="recaptcha-container" style="display:none;"></div>
                                    <p id="otpStatus" class="small mt-3 mb-0 text-muted"></p>
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

<?php if (!($isPhoneVerified ?? false) && ($firebaseConfigured ?? false)): ?>
<script src="https://www.gstatic.com/firebasejs/10.12.5/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/10.12.5/firebase-auth-compat.js"></script>
<script>
(function () {
    const verifyBtn = document.getElementById("verifyOtpBtn");
    const formEl = document.getElementById("phoneVerifyForm");
    const otpInput = document.getElementById("otpCode");
    const phoneE164 = (document.getElementById("phoneE164").value || "").trim();
    const tokenInput = document.getElementById("firebaseIdToken");
    const statusEl = document.getElementById("otpStatus");
    let confirmationResult = null;
    let verifiedIdToken = "";

    function setStatus(message, isError) {
        if (!statusEl) {
            return;
        }
        statusEl.textContent = message;
        statusEl.className = "small mt-3 mb-0 " + (isError ? "text-danger" : "text-muted");
    }

    function disableOtpButtons() {
        if (verifyBtn) {
            verifyBtn.disabled = true;
        }
    }

    window.addEventListener("error", function (e) {
        setStatus("Script error: " + (e.message || "Unknown error"), true);
    });
    window.addEventListener("unhandledrejection", function (e) {
        const msg = e && e.reason && e.reason.message ? e.reason.message : "Unhandled promise error";
        setStatus("Runtime error: " + msg, true);
    });

    try {
        if (typeof firebase === "undefined" || !firebase.auth) {
            setStatus("Firebase SDK failed to load. Check internet/firewall or blocked scripts.", true);
            disableOtpButtons();
            return;
        }

        if (!phoneE164) {
            setStatus("Invalid phone number format. Update phone number and try again.", true);
            disableOtpButtons();
            return;
        }

        const firebaseConfig = {
            apiKey: "<?= esc((string) ($firebaseConfig['apiKey'] ?? ''), 'js') ?>",
            authDomain: "<?= esc((string) ($firebaseConfig['authDomain'] ?? ''), 'js') ?>",
            projectId: "<?= esc((string) ($firebaseConfig['projectId'] ?? ''), 'js') ?>",
            appId: "<?= esc((string) ($firebaseConfig['appId'] ?? ''), 'js') ?>",
            messagingSenderId: "<?= esc((string) ($firebaseConfig['messagingSenderId'] ?? ''), 'js') ?>"
        };

        if (!firebase.apps.length) {
            firebase.initializeApp(firebaseConfig);
        }

        const auth = firebase.auth();
        const isLocalHost = window.location.hostname === "localhost" || window.location.hostname === "127.0.0.1";
        if (isLocalHost) {
            // For local testing with Firebase "Phone numbers for testing".
            auth.settings.appVerificationDisabledForTesting = true;
        }

        if (typeof navigator !== "undefined" && navigator.onLine === false) {
            setStatus("No internet connection. Please reconnect and reload.", true);
            disableOtpButtons();
            return;
        }

        window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier("recaptcha-container", {
            size: "invisible"
        });

        window.recaptchaVerifier.render()
            .then(function () {
                setStatus("Firebase initialized. Sending OTP...", false);
                return auth.signInWithPhoneNumber(phoneE164, window.recaptchaVerifier);
            })
            .then(function (result) {
                confirmationResult = result;
                setStatus("OTP sent successfully.", false);
            })
            .catch(function (error) {
                if (error && error.code === "auth/network-request-failed") {
                    setStatus("Network request failed. Check internet, firewall/proxy, and allow access to googleapis.com/gstatic.com.", true);
                } else {
                    setStatus(error && error.message ? error.message : "Failed to send OTP.", true);
                }
                disableOtpButtons();
            });

        verifyBtn.addEventListener("click", function () {
            const code = (otpInput.value || "").trim();
            if (!confirmationResult) {
                setStatus("OTP not initialized. Please reload and try again.", true);
                return;
            }
            if (code.length < 6) {
                setStatus("Enter a valid 6-digit OTP.", true);
                return;
            }

            verifyBtn.disabled = true;
            setStatus("Verifying OTP...", false);

            confirmationResult.confirm(code)
                .then(function (result) {
                    return result.user.getIdToken(true);
                })
                .then(function (idToken) {
                    verifiedIdToken = idToken;
                    tokenInput.value = idToken;
                    setStatus("OTP verified. Completing verification...", false);
                    if (formEl) {
                        formEl.submit();
                    }
                })
                .catch(function (error) {
                    setStatus(error && error.message ? error.message : "Invalid OTP.", true);
                    verifiedIdToken = "";
                    tokenInput.value = "";
                })
                .finally(function () {
                    verifyBtn.disabled = false;
                });
        });
    } catch (error) {
        setStatus(error && error.message ? error.message : "Failed to initialize Firebase phone auth.", true);
        disableOtpButtons();
    }
})();
</script>
<?php endif; ?>

</body>
</html>
