<?= view('Layouts/candidate_header', ['title' => 'Job Listing']) ?>

<!-- Hero Area Start-->
<div class="slider-area ">
    <div class="single-slider section-overly slider-height2 d-flex align-items-center"
        data-background="assets/img/hero/about.jpg">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="hero-cap text-center">
                        <h2>Get your job</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Hero Area End -->
<!-- Job List Area Start -->
<div class="job-listing-area pt-120 pb-120">
    <div class="container">
        <div class="row">
            <!-- Left content -->

            <!-- Right content -->
            <div class="col-xl-9 col-lg-9 col-md-8">
                <!-- Featured_job_start -->
                <section class="featured-job-area">
                    <div class="container">
                        <!-- Count of Job list Start -->

                        <div class="row">
                            <div class="col-lg-12">
                                <div class="count-job mb-35">

                                    <?php if (empty($jobs)): ?>
                                        <span>No jobs available.</span>
                                    <?php else: ?>
                                        <span><?= esc($totalJobs) ?> Jobs found</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <!-- Count of Job list End -->
                        <?php foreach ($jobs as $job): ?>

                            <!-- single-job-content -->
                            <div class="single-job-items mb-30">
                                <div class="job-items">
                                    <div class="company-img">
                                        <a href="#"><img src="assets/img/icon/job-list1.png" alt=""></a>
                                    </div>
                                    <div class="job-tittle job-tittle2">
                                        <!-- <a href="#"> -->
                                        <h4><?= esc($job['title']) ?></h4>
                                        <!-- </a> -->
                                        <ul>
                                            <li><?= esc($job['company']) ?></li>
                                            <li><i class="fas fa-map-marker-alt"></i><?= esc($job['location']) ?></li>
                                            <li><?= esc($job['experience_level']) ?></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="items-link items-link2 f-right">
                                    <a href="<?= base_url('job/' . $job['id']) ?>">View</a>
                                </div>

                            </div>
                        <?php endforeach; ?>

                    </div>
                </section>
                <!-- Featured_job_end -->
            </div>
        </div>
    </div>
</div>
<!-- Job List Area End -->
<?php if (!empty($jobs)): ?>
    <!--Pagination Start  -->
    <!-- <div class="pagination-area pb-115 text-center">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="single-wrap d-flex justify-content-center">
                        <nav aria-label="Page navigation example">
                            <ul class="pagination justify-content-start">
                                <li class="page-item active"><a class="page-link" href="#">01</a></li>
                                <li class="page-item"><a class="page-link" href="#">02</a></li>
                                <li class="page-item"><a class="page-link" href="#">03</a></li>
                                <li class="page-item"><a class="page-link" href="#"><span class="ti-angle-right"></span></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
    <!--Pagination End  -->
<?php endif; ?>

<?= view('layouts/candidate_footer') ?>