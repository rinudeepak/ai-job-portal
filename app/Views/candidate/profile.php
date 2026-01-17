<?= view('Layouts/candidate_header', ['title' => 'My Profile']) ?>

<!-- Hero Area Start-->
<div class="slider-area ">
    <div class="single-slider section-overly slider-height2 d-flex align-items-center"
        data-background="assets/img/hero/about.jpg">
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
                <?php if (session()->getFlashdata('success')): ?>
                    <p style="color:green">
                        <?= session()->getFlashdata('success') ?>
                    </p>
                <?php endif; ?>

                <form class="form-contact contact_form" method="post" action="<?= base_url('candidate/profile') ?>"
                    novalidate="novalidate" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input class="form-control valid" name="github_username" id="github_username"
                                    type="text" onfocus="this.placeholder = ''"
                                    onblur="this.placeholder = 'Gihub Username'" placeholder="Gihub Username">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <input class="form-control valid" name="linkedin_link" id="linkedin_link" type="text"
                                    onfocus="this.placeholder = ''" onblur="this.placeholder = 'LinkedIn Link'"
                                    placeholder="LinkedIn Link">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="form-label">Upload Resume</label>
                                <input type="file" name="resume" class="form-control">
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