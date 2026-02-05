<?= view('Layouts/recruiter_header', ['title' => 'Post Job']) ?>

<!-- ================ Form section start ================= -->
<section class="contact-section pt-5">
            <div class="container">
                
    
                <div class="row">
                    <div class="col-12">
                        <h2 class="contact-title">Post A Job</h2>
                    </div>
                    <div class="col-lg-8">
                        <?php if(session()->getFlashdata('success')): ?>
                            <p style="color:green"><?= session()->getFlashdata('success') ?></p>
                        <?php endif; ?>

                        <form class="form-contact contact_form" method="post" action="<?= base_url('recruiter/post_job') ?>" novalidate="novalidate">
                            <?= csrf_field() ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input class="form-control valid" name="title" id="title" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Job Title'" placeholder="Job Title">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control valid" name="company" id="company" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Company'" placeholder="Company">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control valid" name="location" id="location" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Location'" placeholder="Location">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <textarea class="form-control w-100" name="description" id="description" cols="30" rows="9" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Job Decription'" placeholder=" Job Decription"></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control valid" name="experience_level" id="experience_level" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Experience'" placeholder="Experience">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control valid" name="min_ai_cutoff_score" id="min_ai_cutoff_score" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Minimum AI Cutoff Score'" placeholder="Minimum AI Cutoff Score">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-control valid" name="openings" id="openings" type="number" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Number of Openings'" placeholder="Number of Openings">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-control" name="required_skills" id="required_skills" type="text" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Required Skills'" placeholder="Required Skills">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group mt-3">
                                <button type="submit" class="button button-contactForm boxed-btn">Post</button>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
        </section>

        <?= view('layouts/recruiter_footer') ?>