<?= view('Layouts/candidate_header', ['title' => 'My Profile']) ?>

<div class="profile-jobboard">
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <h1 class="text-white font-weight-bold">My Profile</h1>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong>My Profile</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

<section class="site-section pt-0 content-wrap">
    <div class="container">
        <style>
            .profile-quick-actions .btn {
                width: 100%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                border-radius: 8px;
                font-weight: 600;
                margin-bottom: 10px;
            }
            .profile-quick-actions .btn:last-child {
                margin-bottom: 0;
            }
            @media (min-width: 992px) {
                .profile-two-pane {
                    align-items: flex-start;
                }
                .profile-two-pane .profile-left-pane {
                    position: sticky;
                    top: 110px;
                }
                .profile-two-pane .profile-right-pane {
                    max-height: calc(100vh - 130px);
                    overflow-y: auto;
                    padding-right: 8px;
                }
            }
        </style>
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

        <div class="row profile-two-pane">
            <!-- Profile Card -->
            <div class="col-lg-4 mb-4 profile-left-pane">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <div class="profile-avatar mb-3">
                            <?php if (!empty($user['profile_photo'])): ?>
                                <img src="<?= base_url($user['profile_photo']) ?>" alt="Profile" class="rounded-circle" width="120" height="120" style="object-fit: cover; border: 4px solid #e9ecef;">
                            <?php else: ?>
                                <div class="rounded-circle mx-auto" style="width: 120px; height: 120px; border: 4px solid #e9ecef; background: transparent;"></div>
                            <?php endif; ?>
                            <div class="mt-2">
                                <form method="post" action="<?= base_url('candidate/upload-photo') ?>" enctype="multipart/form-data" style="display: inline;">
                                    <?= csrf_field() ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="document.getElementById('profilePhoto').click()">
                                        <i class="fas fa-camera"></i> Change Photo
                                    </button>
                                    <input type="file" id="profilePhoto" name="profile_photo" accept="image/*" style="display: none;" onchange="this.form.submit()">
                                </form>
                                <?php if (!empty($user['profile_photo'])): ?>
                                    <form method="post" action="<?= base_url('candidate/remove-photo') ?>" style="display: inline;">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger ml-2" onclick="return confirm('Remove profile photo?')">
                                            <i class="fas fa-trash"></i> Remove Photo
                                        </button>
                                    </form>
                                <?php endif; ?>
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
                        <div class="profile-quick-actions">
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

                <div class="card shadow-sm mt-3">
                    <div class="card-body">
                        <h6 class="card-title"><i class="fas fa-link"></i> Quick Links</h6>
                        <div class="list-group list-group-flush">
                            <a href="#personal" class="list-group-item list-group-item-action px-0 py-2">Personal Information</a>
                            <a href="#resume" class="list-group-item list-group-item-action px-0 py-2">Resume</a>
                            <a href="#github" class="list-group-item list-group-item-action px-0 py-2">GitHub</a>
                            <a href="#skills" class="list-group-item list-group-item-action px-0 py-2">Skills</a>
                            <a href="#interests" class="list-group-item list-group-item-action px-0 py-2">Interests</a>
                            <a href="#experience" class="list-group-item list-group-item-action px-0 py-2">Experience</a>
                            <a href="#education" class="list-group-item list-group-item-action px-0 py-2">Education</a>
                            <a href="#certifications" class="list-group-item list-group-item-action px-0 py-2">Certifications</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-8 profile-right-pane">
                <div class="mb-4">
                    <h5 class="mb-2"><i class="fas fa-id-card"></i> Complete Profile Overview</h5>
                    <p class="text-muted mb-0">All profile sections are shown below as separate cards.</p>
                </div>

                <div id="profileSections">
                    <div class="profile-section mb-4" id="personal">
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

                    <div class="profile-section mb-4" id="resume">
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

                    <div class="profile-section mb-4" id="github">
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

                    <div class="profile-section mb-4" id="skills">
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
                                            <button class="btn btn-primary me-2" type="button" onclick="document.getElementById('resume').scrollIntoView({ behavior: 'smooth', block: 'start' })">Upload Resume</button>
                                            <button class="btn btn-success" type="button" onclick="document.getElementById('github').scrollIntoView({ behavior: 'smooth', block: 'start' })">Connect GitHub</button>
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

                    <div class="profile-section mb-4" id="interests">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-heart"></i> Job Interests</h5>
                            </div>
                            <div class="card-body">
                                <?php if (session()->getFlashdata('success')): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?= esc(session()->getFlashdata('success')) ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                <?php if (session()->getFlashdata('error')): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?= esc(session()->getFlashdata('error')) ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>

                                <p class="text-muted mb-3">
                                    <i class="fas fa-info-circle"></i>
                                    Add job categories, roles, or technologies you're interested in.
                                    These are used to personalise your <strong>For You</strong> job recommendations.
                                </p>

                                <!-- Current interests -->
                                <?php if (!empty($interests)): ?>
                                    <div class="mb-4">
                                        <h6><i class="fas fa-tags"></i> Your Interests</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            <?php foreach ($interests as $interest): ?>
                                                <span class="badge bg-success d-inline-flex align-items-center gap-1 px-3 py-2" style="font-size:.85rem;">
                                                    <?= esc($interest) ?>
                                                    <a href="<?= base_url('candidate/delete-interest/' . urlencode($interest)) ?>"
                                                       onclick="return confirm('Remove this interest?')"
                                                       class="text-white ms-1"
                                                       title="Remove"
                                                       style="text-decoration:none;line-height:1;">
                                                        &times;
                                                    </a>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-3 mb-4">
                                        <i class="fas fa-heart fa-3x text-muted mb-2"></i>
                                        <p class="text-muted">No interests added yet. Add some below to get personalised job matches!</p>
                                    </div>
                                <?php endif; ?>

                                <!-- Add interest form -->
                                <div class="mt-2">
                                    <h6><i class="fas fa-plus-circle"></i> Add an Interest</h6>
                                    <form method="post" action="<?= base_url('candidate/add-interest') ?>" class="d-flex gap-2">
                                        <?= csrf_field() ?>
                                        <input type="text" name="interest" class="form-control"
                                               placeholder="e.g. Web Development, Data Science, React, Remote…" required>
                                        <button type="submit" class="btn btn-success text-nowrap">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </form>
                                </div>

                                <!-- Suggestions -->
                                <div class="mt-4">
                                    <h6 class="text-muted"><i class="fas fa-lightbulb"></i> Popular interests — click to add</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php
                                        $suggestions = [
                                            'Web Development','Mobile Development','Data Science','Machine Learning',
                                            'DevOps','Cloud Computing','Cybersecurity','UI/UX Design',
                                            'Backend Development','Frontend Development','Full Stack','Remote',
                                            'Python','JavaScript','PHP','Java','React','Node.js',
                                        ];
                                        // $interests is a flat string array e.g. ['PHP', 'React', 'DevOps']
                                        $existingInterests = array_map('strtolower', $interests ?? []);
                                        foreach ($suggestions as $sug):
                                            if (!in_array(strtolower($sug), $existingInterests)):
                                        ?>
                                            <button type="button"
                                                    class="btn btn-outline-secondary btn-sm"
                                                    onclick="quickAddInterest('<?= esc($sug) ?>')">
                                                + <?= esc($sug) ?>
                                            </button>
                                        <?php
                                            endif;
                                        endforeach;
                                        ?>
                                    </div>
                                </div>

                                <!-- Hidden quick-add form -->
                                <form method="post" action="<?= base_url('candidate/add-interest') ?>" id="quickInterestForm" style="display:none;">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="interest" id="quickInterestValue">
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="profile-section mb-4" id="experience">
                        <div class="card shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-briefcase"></i> Work Experience</h5>
                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addExperienceModal"><i class="fas fa-plus"></i> Add</button>
                            </div>
                            <div class="card-body">
                                <?php if (session()->getFlashdata('success')): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?= session()->getFlashdata('success') ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                <?php if (session()->getFlashdata('error')): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?= session()->getFlashdata('error') ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($workExperiences)): ?>
                                    <?php foreach($workExperiences as $exp): ?>
                                    <div class="experience-item border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="mb-1"><?= esc($exp['job_title']) ?></h6>
                                                <p class="mb-1 text-muted"><i class="fas fa-building"></i> <?= esc($exp['company_name']) ?> • <?= esc($exp['employment_type']) ?></p>
                                                <p class="mb-1 text-muted"><i class="fas fa-calendar"></i> <?= date('M Y', strtotime($exp['start_date'])) ?> - <?= $exp['is_current'] ? 'Present' : date('M Y', strtotime($exp['end_date'])) ?></p>
                                                <?php if($exp['location']): ?><p class="mb-1 text-muted"><i class="fas fa-map-marker-alt"></i> <?= esc($exp['location']) ?></p><?php endif; ?>
                                                <?php if($exp['description']): ?><p class="mt-2"><?= nl2br(esc($exp['description'])) ?></p><?php endif; ?>
                                            </div>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary me-1" onclick='editExperience(<?= json_encode($exp) ?>)'><i class="fas fa-edit"></i></button>
                                                <a href="<?= base_url('candidate/delete-work-experience/'.$exp['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this experience?')"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted text-center py-4">No work experience added yet</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="profile-section mb-4" id="education">
                        <div class="card shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-graduation-cap"></i> Education</h5>
                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addEducationModal"><i class="fas fa-plus"></i> Add</button>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($education)): ?>
                                    <?php foreach($education as $edu): ?>
                                    <div class="education-item border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="mb-1"><?= esc($edu['degree']) ?></h6>
                                                <p class="mb-1 text-muted"><i class="fas fa-university"></i> <?= esc($edu['institution']) ?></p>
                                                <p class="mb-1 text-muted"><i class="fas fa-book"></i> <?= esc($edu['field_of_study']) ?></p>
                                                <p class="mb-1 text-muted"><i class="fas fa-calendar"></i> <?= esc($edu['start_year']) ?> - <?= esc($edu['end_year']) ?></p>
                                                <?php if($edu['grade']): ?><p class="mb-1 text-muted"><i class="fas fa-award"></i> Grade: <?= esc($edu['grade']) ?></p><?php endif; ?>
                                            </div>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary me-1" onclick='editEducation(<?= json_encode($edu) ?>)'><i class="fas fa-edit"></i></button>
                                                <a href="<?= base_url('candidate/delete-education/'.$edu['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this education?')"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted text-center py-4">No education added yet</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="profile-section mb-4" id="certifications">
                        <div class="card shadow-sm">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-certificate"></i> Certifications</h5>
                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addCertificationModal"><i class="fas fa-plus"></i> Add</button>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($certifications)): ?>
                                    <?php foreach($certifications as $cert): ?>
                                    <div class="certification-item border-bottom pb-3 mb-3">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="mb-1"><?= esc($cert['certification_name']) ?></h6>
                                                <p class="mb-1 text-muted"><i class="fas fa-building"></i> <?= esc($cert['issuing_organization']) ?></p>
                                                <p class="mb-1 text-muted"><i class="fas fa-calendar"></i> Issued: <?= date('M Y', strtotime($cert['issue_date'])) ?><?= $cert['expiry_date'] ? ' • Expires: '.date('M Y', strtotime($cert['expiry_date'])) : '' ?></p>
                                                <?php if($cert['credential_id']): ?><p class="mb-1 text-muted"><i class="fas fa-id-card"></i> ID: <?= esc($cert['credential_id']) ?></p><?php endif; ?>
                                                <?php if($cert['credential_url']): ?><p class="mb-1"><a href="<?= esc($cert['credential_url']) ?>" target="_blank"><i class="fas fa-external-link-alt"></i> View Credential</a></p><?php endif; ?>
                                            </div>
                                            <div>
                                                <button class="btn btn-sm btn-outline-primary me-1" onclick='editCertification(<?= json_encode($cert) ?>)'><i class="fas fa-edit"></i></button>
                                                <a href="<?= base_url('candidate/delete-certification/'.$cert['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this certification?')"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted text-center py-4">No certifications added yet</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>

<!-- Add Work Experience Modal -->
<div class="modal fade" id="addExperienceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Work Experience</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="post" action="<?= base_url('candidate/add-work-experience') ?>" id="workExpForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="exp_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Job Title *</label>
                            <input type="text" name="job_title" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Company Name *</label>
                            <input type="text" name="company_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Employment Type</label>
                            <select name="employment_type" class="form-control">
                                <option>Full-time</option>
                                <option>Part-time</option>
                                <option>Contract</option>
                                <option>Freelance</option>
                                <option>Internship</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Start Date *</label>
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" id="endDate">
                            <div class="form-check mt-2">
                                <input type="checkbox" name="is_current" value="1" class="form-check-input" id="isCurrent" onchange="document.getElementById('endDate').disabled=this.checked">
                                <label class="form-check-label" for="isCurrent">Currently working here</label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" placeholder="Describe your responsibilities and achievements..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Education Modal -->
<div class="modal fade" id="addEducationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Education</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="post" action="<?= base_url('candidate/add-education') ?>" id="educationForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="edu_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Degree *</label>
                        <input type="text" name="degree" class="form-control" placeholder="e.g., Bachelor of Technology" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Field of Study *</label>
                        <input type="text" name="field_of_study" class="form-control" placeholder="e.g., Computer Science" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Institution *</label>
                        <input type="text" name="institution" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Start Year *</label>
                            <input type="number" name="start_year" class="form-control" min="1950" max="2030" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">End Year *</label>
                            <input type="number" name="end_year" class="form-control" min="1950" max="2030" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Grade/CGPA</label>
                        <input type="text" name="grade" class="form-control" placeholder="e.g., 8.5 CGPA">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Certification Modal -->
<div class="modal fade" id="addCertificationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Certification</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="post" action="<?= base_url('candidate/add-certification') ?>" id="certificationForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="cert_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Certification Name *</label>
                        <input type="text" name="certification_name" class="form-control" placeholder="e.g., AWS Certified Developer" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Issuing Organization *</label>
                        <input type="text" name="issuing_organization" class="form-control" placeholder="e.g., Amazon Web Services" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Issue Date *</label>
                            <input type="date" name="issue_date" class="form-control" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Expiry Date</label>
                            <input type="date" name="expiry_date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Credential ID</label>
                        <input type="text" name="credential_id" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Credential URL</label>
                        <input type="url" name="credential_url" class="form-control" placeholder="https://...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= view('Layouts/candidate_footer') ?>





