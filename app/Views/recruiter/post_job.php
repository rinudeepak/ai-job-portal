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
                        <?php if(session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
                        <?php endif; ?>

                        <form class="form-contact contact_form" method="post" action="<?= base_url('recruiter/post_job') ?>" novalidate="novalidate">
                            <?= csrf_field() ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input class="form-control valid" name="title" id="title" type="text" value="<?= old('title') ?>" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Job Title'" placeholder="Job Title">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input class="form-control valid" name="category" id="category" type="text" value="<?= old('category') ?>" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Job Category (e.g., Software Development)'" placeholder="Job Category (e.g., Software Development)">
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input class="form-control valid" name="location" id="location" type="text" value="<?= old('location') ?>" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Location'" placeholder="Location">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <textarea class="form-control w-100" name="description" id="description" cols="30" rows="9" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Job Decription'" placeholder=" Job Decription"><?= old('description') ?></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control valid" name="experience_level" id="experience_level" type="text" value="<?= old('experience_level') ?>" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Experience (e.g., 2-3 years)'" placeholder="Experience (e.g., 2-3 years)">
                                    </div>
                                </div>
                                <?php $selectedPolicy = old('ai_interview_policy', 'REQUIRED_HARD'); ?>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <select class="form-control" name="ai_interview_policy" id="ai_interview_policy">
                                            <option value="REQUIRED_HARD" <?= $selectedPolicy === 'REQUIRED_HARD' ? 'selected' : '' ?>>AI Interview: Mandatory (Strict)</option>
                                            <option value="REQUIRED_SOFT" <?= $selectedPolicy === 'REQUIRED_SOFT' ? 'selected' : '' ?>>AI Interview: Mandatory (Recruiter Can Override)</option>
                                            <option value="OPTIONAL" <?= $selectedPolicy === 'OPTIONAL' ? 'selected' : '' ?>>AI Interview: Optional</option>
                                            <option value="OFF" <?= $selectedPolicy === 'OFF' ? 'selected' : '' ?>>AI Interview: Not Required</option>
                                        </select>
                                        <small class="text-muted d-block mt-2">
                                            Choose how AI interview affects applications: strict reject, recruiter override, optional, or disabled.
                                        </small>
                                    </div>
                                </div>
                                <div class="col-sm-6" id="minAiCutoffWrap">
                                    <div class="form-group">
                                        <input class="form-control valid" name="min_ai_cutoff_score" id="min_ai_cutoff_score" type="number" min="0" max="100" value="<?= old('min_ai_cutoff_score') ?>" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Minimum AI Cutoff Score'" placeholder="Minimum AI Cutoff Score">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-control valid" name="openings" id="openings" type="number" value="<?= old('openings') ?>" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Number of Openings'" placeholder="Number of Openings">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-control" name="required_skills" id="required_skills" type="text" value="<?= old('required_skills') ?>" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Required Skills'" placeholder="Required Skills">
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
        <script>
            (function () {
                const policySelect = document.getElementById('ai_interview_policy');
                const cutoffWrap = document.getElementById('minAiCutoffWrap');
                const cutoffInput = document.getElementById('min_ai_cutoff_score');

                if (!policySelect || !cutoffWrap || !cutoffInput) {
                    return;
                }

                function toggleCutoffField() {
                    const isAiOff = policySelect.value === 'OFF';
                    cutoffWrap.style.display = isAiOff ? 'none' : '';
                    cutoffInput.disabled = isAiOff;
                    cutoffInput.required = !isAiOff;
                    if (isAiOff) {
                        cutoffInput.value = '';
                    }
                }

                policySelect.addEventListener('change', toggleCutoffField);
                toggleCutoffField();
            })();
        </script>

        <?= view('Layouts/recruiter_footer') ?>
