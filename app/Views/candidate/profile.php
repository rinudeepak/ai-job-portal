<?= view('Layouts/candidate_header', ['title' => 'My Profile']) ?>

<section class="contact-section pt-5">
    <div class="container">
        <!-- Profile Completion Progress -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-chart-line"></i> Profile Completion</h5>
                        <div class="progress mb-2">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?= $completion['percentage'] ?>%" aria-valuenow="<?= $completion['percentage'] ?>" aria-valuemin="0" aria-valuemax="100"><?= $completion['percentage'] ?>%</div>
                        </div>
                        <small class="text-muted">Complete your profile to increase job match accuracy</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Profile Card -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="profile-avatar mb-3">
                            <?php if (!empty($user['profile_photo'])): ?>
                                <img src="<?= base_url($user['profile_photo']) ?>" alt="Profile" class="rounded-circle" width="120" height="120" style="object-fit: cover; border: 4px solid #e9ecef;">
                            <?php else: ?>
                                <img src="<?= base_url('assets/img/default-avatar.png') ?>" alt="Profile" class="rounded-circle" width="120" height="120" style="object-fit: cover; border: 4px solid #e9ecef;">
                            <?php endif; ?>
                            <div class="mt-2">
                                <form method="post" action="<?= base_url('candidate/upload-photo') ?>" enctype="multipart/form-data" style="display: inline;">
                                    <?= csrf_field() ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('profilePhoto').click()">
                                        <i class="fas fa-camera"></i> Change Photo
                                    </button>
                                    <input type="file" id="profilePhoto" name="profile_photo" accept="image/*" style="display: none;" onchange="this.form.submit()">
                                </form>
                            </div>
                        </div>
                        <h4><?= esc(session()->get('user_name')) ?></h4>
                        <p class="text-muted">Job Seeker</p>
                        <div class="profile-stats row text-center">
                            <div class="col-4">
                                <strong><?= $stats['applications'] ?></strong><br>
                                <small>Applications</small>
                            </div>
                            <div class="col-4">
                                <strong><?= $stats['interviews'] ?></strong><br>
                                <small>Interviews</small>
                            </div>
                            <div class="col-4">
                                <strong><?= $stats['offers'] ?></strong><br>
                                <small>Offers</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h6>
                        <div class="d-grid gap-2">
                            <?php if (!empty($user['resume_path'])): ?>
                                <a href="<?= base_url('candidate/download-resume') ?>" class="btn btn-outline-primary btn-sm"><i class="fas fa-download"></i> Download Resume</a>
                                <button class="btn btn-outline-success btn-sm" onclick="previewResume()"><i class="fas fa-eye"></i> Preview Profile</button>
                            <?php else: ?>
                                <button class="btn btn-outline-secondary btn-sm" disabled><i class="fas fa-download"></i> No Resume</button>
                                <button class="btn btn-outline-secondary btn-sm" disabled><i class="fas fa-eye"></i> Upload Resume First</button>
                            <?php endif; ?>
                            <button class="btn btn-outline-info btn-sm" onclick="shareProfile()"><i class="fas fa-share"></i> Share Profile</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-4" id="profileTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
                            <i class="fas fa-user"></i> Personal Info
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="resume-tab" data-bs-toggle="tab" data-bs-target="#resume" type="button" role="tab">
                            <i class="fas fa-file-alt"></i> Resume
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="github-tab" data-bs-toggle="tab" data-bs-target="#github" type="button" role="tab">
                            <i class="fab fa-github"></i> GitHub
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="skills-tab" data-bs-toggle="tab" data-bs-target="#skills" type="button" role="tab">
                            <i class="fas fa-code"></i> Skills
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="profileTabsContent">
                    <!-- Personal Info Tab -->
                    <div class="tab-pane fade show active" id="personal" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-user"></i> Personal Information</h5>
                            </div>
                            <div class="card-body">
                                <?php if (session()->getFlashdata('personal_success')): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('personal_success') ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <form method="post" action="<?= base_url('candidate/update_personal') ?>">
                                    <?= csrf_field() ?>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fas fa-user"></i> Full Name</label>
                                            <input type="text" name="name" class="form-control" value="<?= esc(session()->get('user_name')) ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fas fa-envelope"></i> Email</label>
                                            <input type="email" name="email" class="form-control" value="<?= esc($user['email'] ?? '') ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fas fa-phone"></i> Phone</label>
                                            <input type="tel" name="phone" class="form-control" value="<?= esc($user['phone'] ?? '') ?>">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label"><i class="fas fa-map-marker-alt"></i> Location</label>
                                            <input type="text" name="location" class="form-control" value="<?= esc($user['location'] ?? '') ?>">
                                        </div>
                                        <div class="col-12 mb-3">
                                            <label class="form-label"><i class="fas fa-info-circle"></i> Bio</label>
                                            <textarea name="bio" class="form-control" rows="4" placeholder="Tell us about yourself..."><?= esc($user['bio'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Resume Tab -->
                    <div class="tab-pane fade" id="resume" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-file-alt"></i> Resume Management</h5>
                            </div>
                            <div class="card-body">
                                <?php if (session()->getFlashdata('upload_success')): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('upload_success') ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($user['resume_path'])): ?>
                                    <div class="current-resume mb-4">
                                        <div class="d-flex align-items-center p-3 border rounded bg-light">
                                            <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">Current Resume</h6>
                                                <small class="text-muted"><?= esc($user['resume_path']) ?></small>
                                            </div>
                                            <div>
                                                <button class="btn btn-outline-primary btn-sm me-2" onclick="previewResume()"><i class="fas fa-eye"></i> Preview</button>
                                                <a href="<?= base_url('candidate/download-resume') ?>" class="btn btn-outline-success btn-sm"><i class="fas fa-download"></i> Download</a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="<?= base_url('candidate/resume_upload') ?>" enctype="multipart/form-data">
                                    <?= csrf_field() ?>
                                    <div class="upload-area border-2 border-dashed rounded p-4 text-center mb-3" style="border-color: #dee2e6;">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                        <h6>Drag & Drop your resume here</h6>
                                        <p class="text-muted mb-3">or click to browse files</p>
                                        <input type="file" name="resume" class="form-control" accept=".pdf,.doc,.docx" required>
                                        <small class="text-muted">Supported formats: PDF, DOC, DOCX (Max 5MB)</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-upload"></i> Upload Resume</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- GitHub Tab -->
                    <div class="tab-pane fade" id="github" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fab fa-github"></i> GitHub Integration</h5>
                            </div>
                            <div class="card-body">
                                <?php if (session()->getFlashdata('profile_success')): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <i class="fas fa-check-circle"></i> <?= session()->getFlashdata('profile_success') ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <form method="post" action="<?= base_url('candidate/analyze_github') ?>">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label class="form-label"><i class="fab fa-github"></i> GitHub Username</label>
                                        <div class="input-group">
                                            <span class="input-group-text">github.com/</span>
                                            <input type="text" name="github_username" class="form-control" value="<?= esc($github['github_username'] ?? '') ?>" placeholder="your-username">
                                        </div>
                                        <small class="text-muted">We'll analyze your repositories to extract skills automatically</small>
                                    </div>
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-sync"></i> Analyze GitHub</button>
                                </form>

                                <?php if (!empty($github['github_username'])): ?>
                                    <div class="github-stats mt-4">
                                        <h6><i class="fas fa-chart-bar"></i> GitHub Stats</h6>
                                        <div class="row text-center">
                                            <div class="col-4">
                                                <div class="stat-card p-3 border rounded">
                                                    <strong><?= esc($github['repo_count'] ?? 0) ?></strong><br>
                                                    <small>Repositories</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="stat-card p-3 border rounded">
                                                    <strong><?= esc($github['commit_count'] ?? 0) ?></strong><br>
                                                    <small>Commits</small>
                                                </div>
                                            </div>
                                            <div class="col-4">
                                                <div class="stat-card p-3 border rounded">
                                                    <strong><?= count(explode(',', $github['languages_used'] ?? '')) ?></strong><br>
                                                    <small>Languages</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <h6><i class="fas fa-code"></i> Languages Used</h6>
                                            <div class="languages-list">
                                                <?php 
                                                $languages = explode(',', $github['languages_used'] ?? '');
                                                foreach($languages as $lang): 
                                                    if(trim($lang)): 
                                                ?>
                                                    <span class="badge bg-info me-1 mb-1"><?= esc(trim($lang)) ?></span>
                                                <?php 
                                                    endif;
                                                endforeach; 
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Skills Tab -->
                    <div class="tab-pane fade" id="skills" role="tabpanel">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-code"></i> Skills & Technologies</h5>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($skills['skill_name'])): ?>
                                    <div class="skills-section mb-4">
                                        <h6><i class="fas fa-laptop-code"></i> Extracted Skills</h6>
                                        <div class="skills-tags">
                                            <?php 
                                            $skillList = explode(',', $skills['skill_name']);
                                            foreach($skillList as $skill): 
                                                $trimmedSkill = trim($skill);
                                                if($trimmedSkill): 
                                            ?>
                                                <span class="badge bg-primary me-2 mb-2"><?= esc($trimmedSkill) ?></span>
                                            <?php 
                                                endif;
                                            endforeach; 
                                            ?>
                                        </div>
                                        <small class="text-muted">Skills extracted from your resume</small>
                                    </div>
                                <?php endif; ?>

                                <?php if (!empty($github['languages_used'])): ?>
                                    <div class="skills-section mb-4">
                                        <h6><i class="fab fa-github"></i> GitHub Languages</h6>
                                        <div class="skills-tags">
                                            <?php 
                                            $languages = explode(',', $github['languages_used']);
                                            foreach($languages as $lang): 
                                                $trimmedLang = trim($lang);
                                                if($trimmedLang): 
                                            ?>
                                                <span class="badge bg-success me-2 mb-2"><?= esc($trimmedLang) ?></span>
                                            <?php 
                                                endif;
                                            endforeach; 
                                            ?>
                                        </div>
                                        <small class="text-muted">Languages from your GitHub repositories</small>
                                    </div>
                                <?php endif; ?>

                                <?php if (empty($skills['skill_name']) && empty($github['languages_used'])): ?>
                                    <div class="text-center py-4">
                                        <i class="fas fa-code fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted">No Skills Found</h6>
                                        <p class="text-muted">Upload your resume or connect GitHub to extract skills automatically</p>
                                        <div class="mt-3">
                                            <button class="btn btn-primary me-2" onclick="document.querySelector('#resume-tab').click()">Upload Resume</button>
                                            <button class="btn btn-success" onclick="document.querySelector('#github-tab').click()">Connect GitHub</button>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="skills-section mt-4">
                                    <h6><i class="fas fa-plus-circle"></i> Add Custom Skills</h6>
                                    <form method="post" action="<?= base_url('candidate/add-skill') ?>" class="d-flex">
                                        <?= csrf_field() ?>
                                        <input type="text" name="skill_name" class="form-control me-2" placeholder="Enter skill name" required>
                                        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Add</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<?= view('layouts/candidate_footer') ?>