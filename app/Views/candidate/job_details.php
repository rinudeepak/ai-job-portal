<?= view('Layouts/candidate_header', ['title' => 'Job Details']) ?>

<!-- job post company Start -->
<div class="job-post-company pt-5 pb-120">
    <div class="container">
        <div class="row justify-content-between">
            <!-- Left Content -->
            <div class="col-xl-7 col-lg-8">
                <!-- job single -->
                <div class="single-job-items mb-50">
                    <div class="job-items">
                        <div class="company-img company-img-details">
                            <a href="#"><img src="<?= base_url('assets/img/icon/job-list1.png') ?>" alt=""></a>
                        </div>
                        <div class="job-tittle">
                            <a href="#">
                                <h4><?= esc($job['title']) ?></h4>
                            </a>
                            <ul>
                                <li><?= esc($job['company']) ?></li>
                                <li><i class="fas fa-map-marker-alt"></i><?= esc($job['location']) ?></li>
                                <li>$3500 - $4000</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- job single End -->

                <div class="job-post-details">
                    <div class="post-details1 mb-50">
                        <!-- Small Section Tittle -->
                        <div class="small-section-tittle">
                            <h4>Job Description</h4>
                        </div>
                        <p><?= esc($job['description']) ?></p>
                    </div>
                    <div class="post-details2  mb-50">
                        <!-- Small Section Tittle -->
                        <div class="small-section-tittle">
                            <h4>Required Knowledge, Skills, and Abilities</h4>
                        </div>
                        <?= esc($job['required_skills']) ?>
                    </div>
                    <div class="post-details2  mb-50">
                        <!-- Small Section Tittle -->
                        <div class="small-section-tittle">
                            <h4>Education + Experience</h4>
                        </div>
                        <?= esc($job['experience_level']) ?>
                    </div>
                </div>

            </div>
            <!-- Right Content -->
            <div class="col-xl-4 col-lg-4">
                <!-- Career Transition Suggestion -->
                <?php if (session()->getFlashdata('career_suggestion')): 
                    $suggestion = session()->getFlashdata('career_suggestion'); ?>
                <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                    <h6><i class="fas fa-rocket"></i> Career Transition Opportunity!</h6>
                    <p class="small mb-2"><?= $suggestion['message'] ?></p>
                    <a href="<?= base_url('career-transition') ?>" class="btn btn-sm btn-primary w-100">
                        <i class="fas fa-graduation-cap"></i> Get Learning Roadmap
                    </a>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>
                
                <div class="post-details3  mb-50">
                    <!-- Small Section Tittle -->
                    <div class="small-section-tittle">
                        <h4>Job Overview</h4>
                    </div>
                    <ul>
                        <li>Posted date : <span><?= date('d M Y', strtotime($job['created_at'])) ?></span></li>
                        <li>Location : <span><?= esc($job['location']) ?></span></li>
                        <!-- <li>Vacancy : <span>02</span></li>
                              <li>Job nature : <span>Full time</span></li>
                              <li>Salary :  <span>$7,800 yearly</span></li>
                              <li>Application date : <span>12 Sep 2020</span></li> -->
                    </ul>
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <div class="apply-btn2">
                        <?php if ($alreadyApplied): ?>
                            <button class="btn" disabled style="background:#999;cursor:not-allowed;">
                                Already Applied
                            </button>
                        <?php else: ?>
                            <form method="post" action="<?= base_url('job/apply/' . $job['id']) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn">Apply Now</button>
                            </form>
                        <?php endif; ?>
                    </div>

                </div>
                <!-- <div class="post-details4  mb-50">-->
                <!-- Small Section Tittle -->
                <!--<div class="small-section-tittle">
                               <h4>Company Information</h4>
                           </div>
                              <span>Colorlib</span>
                              <p>It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout.</p>
                            <ul>
                                <li>Name: <span>Colorlib </span></li>
                                <li>Web : <span> colorlib.com</span></li>
                                <li>Email: <span>carrier.colorlib@gmail.com</span></li>
                            </ul>
                       </div> -->
            </div>
        </div>
    </div>
</div>
<!-- job post company End -->
<?= view('layouts/candidate_footer') ?>