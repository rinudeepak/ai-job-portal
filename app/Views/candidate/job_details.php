<?= view('Layouts/candidate_header', ['title' => 'Job Details']) ?>
 <!-- Hero Area Start-->
        <div class="slider-area ">
        <div class="single-slider section-overly slider-height2 d-flex align-items-center" data-background="<?= base_url('assets/img/hero/about.jpg') ?>">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="hero-cap text-center">
                            <h2><?= esc($job['title']) ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
        <!-- Hero Area End -->
        <!-- job post company Start -->
        <div class="job-post-company pt-120 pb-120">
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
                            <form method="post" action="<?= base_url('job/apply/' . $job['id']) ?>">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn">Apply Now</button>
                            </form>

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