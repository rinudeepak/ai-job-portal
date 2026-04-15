</main>
<?php
$candidateId = (int) (session()->get('user_id') ?? 0);
$premiumMentorSubscription = null;
if ($candidateId > 0) {
    try {
        $premiumMentorSubscription = (new \App\Models\SubscriptionModel())->getUserActiveSubscription($candidateId);
    } catch (\Throwable $e) {
        $premiumMentorSubscription = null;
    }
}
$premiumMentorUrl = $premiumMentorSubscription ? base_url('premium-mentor') : base_url('premium/plans?service=mentor');
$premiumMentorLabel = $premiumMentorSubscription ? 'AI Career Mentor' : 'Unlock AI Mentor';
$premiumMentorSubLabel = $premiumMentorSubscription ? 'Open your mentor' : 'View plans';
?>
<style>
    .floating-mentor-launcher {
        position: fixed;
        right: 22px;
        bottom: 22px;
        z-index: 1400;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
    }
    .floating-mentor-launcher:hover {
        text-decoration: none;
    }
    .floating-mentor-launcher__label {
        background: #ffffff;
        color: #0f172a;
        border: 1px solid rgba(148, 163, 184, 0.35);
        border-radius: 16px;
        padding: 10px 14px;
        box-shadow: 0 16px 38px rgba(15, 23, 42, 0.14);
        line-height: 1.15;
        min-width: 156px;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .floating-mentor-launcher__title {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }
    .floating-mentor-launcher__subtitle {
        display: block;
        margin-top: 3px;
        font-size: 11px;
        color: #64748b;
    }
    .floating-mentor-launcher__button {
        width: 64px;
        height: 64px;
        min-width: 64px;
        min-height: 64px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0b66ff 0%, #14b8a6 100%);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 18px 36px rgba(11, 102, 255, 0.28);
        position: relative;
        flex-shrink: 0;
        overflow: visible;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .floating-mentor-launcher__button i {
        font-size: 24px;
        line-height: 1;
        display: block;
    }
    .floating-mentor-launcher__badge {
        position: absolute;
        top: 4px;
        right: 2px;
        min-width: 22px;
        height: 22px;
        border-radius: 999px;
        background: #fff;
        color: #0b66ff;
        font-size: 10px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.12);
    }
    .floating-mentor-launcher:hover .floating-mentor-launcher__button,
    .floating-mentor-launcher:hover .floating-mentor-launcher__label {
        transform: translateY(-2px);
    }
    .floating-mentor-launcher:hover .floating-mentor-launcher__button {
        box-shadow: 0 22px 42px rgba(11, 102, 255, 0.32);
    }
    .floating-mentor-launcher.is-locked .floating-mentor-launcher__button {
        background: linear-gradient(135deg, #64748b 0%, #94a3b8 100%);
        box-shadow: 0 18px 36px rgba(100, 116, 139, 0.22);
    }
    .floating-mentor-launcher.is-locked .floating-mentor-launcher__badge {
        color: #475569;
    }
    @media (max-width: 767.98px) {
        .floating-mentor-launcher {
            right: 16px;
            bottom: 16px;
        }
        .floating-mentor-launcher__label {
            display: none;
        }
        .floating-mentor-launcher__button {
            width: 58px;
            height: 58px;
            min-width: 58px;
            min-height: 58px;
        }
        .floating-mentor-launcher__button i {
            font-size: 22px;
            line-height: 1;
            display: block;
        }
    }
</style>
<a
    href="<?= esc($premiumMentorUrl) ?>"
    class="floating-mentor-launcher <?= $premiumMentorSubscription ? '' : 'is-locked' ?>"
    title="<?= esc($premiumMentorLabel) ?>"
    aria-label="<?= esc($premiumMentorLabel) ?>"
>
    <span class="floating-mentor-launcher__label">
        <span class="floating-mentor-launcher__title"><?= esc($premiumMentorLabel) ?></span>
        <span class="floating-mentor-launcher__subtitle"><?= esc($premiumMentorSubLabel) ?></span>
    </span>
    <span class="floating-mentor-launcher__button">
        <i class="fas fa-robot"></i>
        <span class="floating-mentor-launcher__badge"><?= $premiumMentorSubscription ? 'AI' : 'Go' ?></span>
    </span>
</a>
<footer class="site-footer">
    <a href="#top" class="smoothscroll scroll-top">
        <span class="icon-keyboard_arrow_up"></span>
    </a>
    <div class="container">
        <div class="row mb-4">
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <h3>Candidate</h3>
                <ul class="list-unstyled">
                    <li><a href="<?= base_url('candidate/dashboard') ?>">Dashboard</a></li>
                    <li><a href="<?= base_url('candidate/profile') ?>">Profile</a></li>
                    <li><a href="<?= base_url('candidate/applications') ?>">Applications</a></li>
                    <li><a href="<?= base_url('candidate/my-bookings') ?>">Interviews</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <h3>Jobs</h3>
                <ul class="list-unstyled">
                    <li><a href="<?= base_url('jobs') ?>">Browse Jobs</a></li>
                    <li><a href="<?= base_url('career-transition') ?>">Career Transition AI</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <h3>Account</h3>
                <ul class="list-unstyled">
                    <li><a href="<?= base_url('candidate/settings') ?>">Settings</a></li>
                    <li><a href="<?= base_url('account/change-password') ?>">Change Password</a></li>
                    <li><a href="<?= base_url('logout') ?>">Logout</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3 mb-4 mb-md-0">
                <h3>Tools</h3>
                <div class="translate-widget-wrap mb-3">
                    <?= view('components/google_translate_widget') ?>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row text-center">
                <div class="col-12">
                    <p class="copyright">
                        <small>Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- SCRIPTS -->
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
<script src="<?= base_url('jobboard/js/custom.js?v=' . @filemtime(FCPATH . 'jobboard/js/custom.js')) ?>"></script>
<script src="<?= base_url('jobboard/js/candidate-pages.js?v=' . @filemtime(FCPATH . 'jobboard/js/candidate-pages.js')) ?>"></script>
<script src="<?= base_url('jobboard/js/candidate-application-actions.js?v=' . @filemtime(FCPATH . 'jobboard/js/candidate-application-actions.js')) ?>"></script>
<script src="<?= base_url('jobboard/js/notification-actions.js?v=' . @filemtime(FCPATH . 'jobboard/js/notification-actions.js')) ?>"></script>

<!-- Service Worker Registration -->
<script>
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('<?= base_url('sw.js') ?>')
        .then(reg => console.log('Service Worker registered'))
        .catch(err => console.log('Service Worker registration failed:', err));
}
</script>

 </div>

</body>

</html>
