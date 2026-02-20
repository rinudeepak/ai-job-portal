</main>
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
                    <li><a href="<?= base_url('candidate/profile') ?>">Settings</a></li>
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
<script src="<?= base_url('jobboard/js/custom.js') ?>"></script>
<script src="<?= base_url('jobboard/js/candidate-pages.js') ?>"></script>

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
