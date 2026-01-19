<?= view('Layouts/candidate_header', ['title' => 'My Profile']) ?>

<!-- Hero Area Start-->
<div class="slider-area ">
    <div class="single-slider section-overly slider-height2 d-flex align-items-center"
        data-background="<?= base_url('assets/img/hero/about.jpg') ?>">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <div class="hero-cap text-center">
                        <h2>My Profile</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Hero Area End -->

<section class="contact-section">
    <div class="container">


        <div class="row">
            <div class="col-12">
                <h2 class="contact-title">My Profile</h2>
            </div>
            <div class="col-lg-8">
                <?php if (session()->getFlashdata('profile_success')): ?>
                    <p style="color:green">
                        <?= session()->getFlashdata('profile_success') ?>
                    </p>
                <?php endif; ?>

                <form class="form-contact contact_form" method="post" action="<?= base_url('candidate/analyze_github') ?>"
                    novalidate="novalidate">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input class="form-control valid" name="github_username" id="github_username" value="<?= esc($user['github_username'] ?? '') ?>"
                                    type="text" onfocus="this.placeholder = ''"
                                    onblur="this.placeholder = 'GiHub Username'" placeholder="GiHub Username">
                            </div>
                        </div>
                        <!-- <div class="col-sm-12">
                            <div class="form-group">
                                <input class="form-control valid" name="linkedin_link" id="linkedin_link" type="text" value="<?= esc($user['linkedin_link'] ?? '') ?>"
                                    onfocus="this.placeholder = ''" onblur="this.placeholder = 'LinkedIn Link'"
                                    placeholder="LinkedIn Link">
                            </div>
                        </div> -->
                        
                    </div>
                    <div class="form-group mt-3">
                        <button type="submit" class="button button-contactForm boxed-btn">Save</button>
                    </div>
                </form>
            </div>

            <!--resume upload-->
            <div class="col-lg-8">
                <?php if (session()->getFlashdata('upload_success')): ?>
                    <p style="color:green">
                        <?= session()->getFlashdata('upload_success') ?>
                    </p>
                <?php endif; ?>

                <form class="form-contact contact_form" method="post" action="<?= base_url('candidate/resume_upload') ?>"
                    novalidate="novalidate" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label">Upload Resume</label>
                                <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx">
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3">
                        <button type="submit" class="button button-contactForm boxed-btn">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>


<?= view('layouts/candidate_footer') ?>