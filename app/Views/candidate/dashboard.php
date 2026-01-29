<?= view('Layouts/candidate_header', ['title' => 'Dashboard']) ?>



<!-- Online CV Area Start -->
<div class="online-cv cv-bg section-overly pt-90 pb-120" data-background="assets/img/gallery/cv_bg.jpg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="cv-caption text-center">
                    <p class="pera2">Welcome, <?= session('user_name') ?></p>
                    

                    <!-- <p class="pera2"> Make a Difference with Your Online Resume!</p> -->
                    <!-- <a href="#" class="border-btn2 border-btn4">Upload Resume</a> -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Online CV Area End-->
 <!-- Notification Alerts Section -->
<?= view('candidate/components/dashboard_alerts', ['notifications' => $notifications ?? []]) ?>



<!-- Our Services Start -->
<div class="our-services section-pad-t30">
    <div class="container">
        <!-- Section Tittle -->
        <!-- <div class="row">
                <div class="col-lg-12">
                    <div class="section-tittle text-center">
                        <span>FEATURED TOURS Packages</span>
                        <h2>Browse Top Categories </h2>
                    </div>
                </div>
            </div> -->

        <div class="row d-flex justify-contnet-center">
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                <div class="single-services text-center mb-30">
                    <div class="services-ion">
                        <span class="flaticon-tour"></span>
                    </div>
                    <div class="services-cap">
                        <h5><a href="job_listing.html">Jobs</a></h5>
                        <span>(658)</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                <div class="single-services text-center mb-30">
                    <div class="services-ion">
                        <span class="flaticon-report"></span>
                    </div>
                    <div class="services-cap">
                        <h5><a href="job_listing.html">Applied Jobs</a></h5>
                        <span>(653)</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                <div class="single-services text-center mb-30">
                    <div class="services-ion">
                        <span class="flaticon-cms"></span>
                    </div>
                    <div class="services-cap">
                        <h5><a href="job_listing.html">AI Interviews</a></h5>
                        <span>(658)</span>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                <div class="single-services text-center mb-30">
                    <div class="services-ion">
                        <span class="flaticon-tour"></span>
                    </div>
                    <div class="services-cap">
                        <h5><a href="job_listing.html">My Profile</a></h5>
                        <span>(658)</span>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>
<!-- Our Services End -->

<?= view('layouts/candidate_footer') ?>