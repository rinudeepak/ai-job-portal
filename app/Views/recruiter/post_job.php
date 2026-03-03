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
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <?= session()->getFlashdata('success') ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>
                        <?php if(session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <?= esc(session()->getFlashdata('error')) ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        <?php endif; ?>

                        <form class="form-contact contact_form" method="post" action="<?= base_url('recruiter/post_job') ?>" id="jobForm">
                            <?= csrf_field() ?>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input class="form-control" name="title" id="title" type="text" value="<?= old('title') ?>" placeholder="Job Title" required>
                                        <small class="text-danger" id="title-error"></small>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input class="form-control" name="category" id="category" type="text" value="<?= old('category') ?>" placeholder="Job Category (e.g., Software Development)" required>
                                        <small class="text-danger" id="category-error"></small>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input class="form-control" name="location" id="location" type="text" value="<?= old('location') ?>" placeholder="Location" required>
                                        <small class="text-danger" id="location-error"></small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <textarea class="form-control w-100" name="description" id="description" cols="30" rows="9" placeholder="Job Description" required><?= old('description') ?></textarea>
                                        <small class="text-danger" id="description-error"></small>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <input class="form-control" name="experience_level" id="experience_level" type="text" value="<?= old('experience_level') ?>" placeholder="Experience (e.g., 2-3 years)">
                                        <small class="text-danger" id="experience_level-error"></small>
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
                                        <input class="form-control" name="min_ai_cutoff_score" id="min_ai_cutoff_score" type="number" min="0" max="100" value="<?= old('min_ai_cutoff_score') ?>" placeholder="Minimum AI Cutoff Score">
                                        <small class="text-danger" id="min_ai_cutoff_score-error"></small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-control" name="openings" id="openings" type="number" min="1" value="<?= old('openings', '1') ?>" placeholder="Number of Openings" required>
                                        <small class="text-danger" id="openings-error"></small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input class="form-control" name="required_skills" id="required_skills" type="text" value="<?= old('required_skills') ?>" placeholder="Required Skills">
                                        <small class="text-danger" id="required_skills-error"></small>
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
                const form = document.getElementById('jobForm');

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

                form.addEventListener('submit', function(e) {
                    let isValid = true;
                    document.querySelectorAll('.text-danger').forEach(el => el.textContent = '');
                    document.querySelectorAll('.form-control').forEach(el => el.classList.remove('is-invalid'));

                    const title = document.getElementById('title');
                    const category = document.getElementById('category');
                    const location = document.getElementById('location');
                    const description = document.getElementById('description');
                    const openings = document.getElementById('openings');

                    if (!title.value.trim()) {
                        document.getElementById('title-error').textContent = 'Job title is required';
                        title.classList.add('is-invalid');
                        isValid = false;
                    }

                    if (!category.value.trim()) {
                        document.getElementById('category-error').textContent = 'Category is required';
                        category.classList.add('is-invalid');
                        isValid = false;
                    }

                    if (!location.value.trim()) {
                        document.getElementById('location-error').textContent = 'Location is required';
                        location.classList.add('is-invalid');
                        isValid = false;
                    }

                    if (!description.value.trim()) {
                        document.getElementById('description-error').textContent = 'Description is required';
                        description.classList.add('is-invalid');
                        isValid = false;
                    }

                    if (!openings.value || parseInt(openings.value) < 1) {
                        document.getElementById('openings-error').textContent = 'Openings must be at least 1';
                        openings.classList.add('is-invalid');
                        isValid = false;
                    }

                    if (policySelect.value !== 'OFF' && !cutoffInput.disabled) {
                        const cutoffVal = cutoffInput.value.trim();
                        if (!cutoffVal) {
                            document.getElementById('min_ai_cutoff_score-error').textContent = 'AI cutoff score is required when AI interview is enabled';
                            cutoffInput.classList.add('is-invalid');
                            isValid = false;
                        } else {
                            const score = parseInt(cutoffVal);
                            if (score < 0 || score > 100) {
                                document.getElementById('min_ai_cutoff_score-error').textContent = 'Score must be between 0 and 100';
                                cutoffInput.classList.add('is-invalid');
                                isValid = false;
                            }
                        }
                    }

                    if (!isValid) {
                        e.preventDefault();
                        document.querySelector('.is-invalid')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                });
            })();
        </script>

        <?= view('Layouts/recruiter_footer') ?>
