<?= view('Layouts/recruiter_header', ['title' => 'Candidate Profile']) ?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <?php if (!empty($candidate['profile_photo'])): ?>
                        <img src="<?= base_url($candidate['profile_photo']) ?>" alt="Profile" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                    <?php else: ?>
                        <img src="<?= base_url('jobboard/images/default-avatar.png') ?>" alt="Profile" class="rounded-circle mb-3" width="120" height="120">
                    <?php endif; ?>
                    <h4><?= esc($candidate['name']) ?></h4>
                    <p class="text-muted"><?= esc($candidate['email']) ?></p>
                    <?php if($candidate['phone']): ?><p><i class="fas fa-phone"></i> <?= esc($candidate['phone']) ?></p><?php endif; ?>
                    <?php if($candidate['location']): ?><p><i class="fas fa-map-marker-alt"></i> <?= esc($candidate['location']) ?></p><?php endif; ?>
                    <?php if($candidate['resume_path']): ?>
                        <a href="<?= base_url('writable/'.$candidate['resume_path']) ?>" class="btn btn-primary btn-sm mt-2" download><i class="fas fa-download"></i> Download Resume</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if($candidate['bio']): ?>
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-info-circle"></i> About</h6>
                    <p><?= nl2br(esc($candidate['bio'])) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="col-lg-8">
            <!-- Skills -->
            <?php if (!empty($skills['skill_name']) || !empty($github['languages_used'])): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5><i class="fas fa-code"></i> Skills & Technologies</h5>
                    <?php if (!empty($skills['skill_name'])): ?>
                        <h6 class="mt-3">Resume Skills</h6>
                        <div>
                            <?php foreach(explode(',', $skills['skill_name']) as $skill): ?>
                                <span class="badge bg-primary me-1 mb-1"><?= esc(trim($skill)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($github['languages_used'])): ?>
                        <h6 class="mt-3">GitHub Languages</h6>
                        <div>
                            <?php foreach(explode(',', $github['languages_used']) as $lang): ?>
                                <span class="badge bg-success me-1 mb-1"><?= esc(trim($lang)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Work Experience -->
            <?php if (!empty($workExperiences)): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5><i class="fas fa-briefcase"></i> Work Experience</h5>
                    <?php foreach($workExperiences as $exp): ?>
                    <div class="border-bottom pb-3 mb-3">
                        <h6 class="mb-1"><?= esc($exp['job_title']) ?></h6>
                        <p class="mb-1 text-muted"><i class="fas fa-building"></i> <?= esc($exp['company_name']) ?> • <?= esc($exp['employment_type']) ?></p>
                        <p class="mb-1 text-muted"><i class="fas fa-calendar"></i> <?= date('M Y', strtotime($exp['start_date'])) ?> - <?= $exp['is_current'] ? 'Present' : date('M Y', strtotime($exp['end_date'])) ?></p>
                        <?php if($exp['location']): ?><p class="mb-1 text-muted"><i class="fas fa-map-marker-alt"></i> <?= esc($exp['location']) ?></p><?php endif; ?>
                        <?php if($exp['description']): ?><p class="mt-2"><?= nl2br(esc($exp['description'])) ?></p><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Education -->
            <?php if (!empty($education)): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5><i class="fas fa-graduation-cap"></i> Education</h5>
                    <?php foreach($education as $edu): ?>
                    <div class="border-bottom pb-3 mb-3">
                        <h6 class="mb-1"><?= esc($edu['degree']) ?></h6>
                        <p class="mb-1 text-muted"><i class="fas fa-university"></i> <?= esc($edu['institution']) ?></p>
                        <p class="mb-1 text-muted"><i class="fas fa-book"></i> <?= esc($edu['field_of_study']) ?></p>
                        <p class="mb-1 text-muted"><i class="fas fa-calendar"></i> <?= esc($edu['start_year']) ?> - <?= esc($edu['end_year']) ?></p>
                        <?php if($edu['grade']): ?><p class="mb-1 text-muted"><i class="fas fa-award"></i> Grade: <?= esc($edu['grade']) ?></p><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Certifications -->
            <?php if (!empty($certifications)): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5><i class="fas fa-certificate"></i> Certifications</h5>
                    <?php foreach($certifications as $cert): ?>
                    <div class="border-bottom pb-3 mb-3">
                        <h6 class="mb-1"><?= esc($cert['certification_name']) ?></h6>
                        <p class="mb-1 text-muted"><i class="fas fa-building"></i> <?= esc($cert['issuing_organization']) ?></p>
                        <p class="mb-1 text-muted"><i class="fas fa-calendar"></i> Issued: <?= date('M Y', strtotime($cert['issue_date'])) ?><?= $cert['expiry_date'] ? ' • Expires: '.date('M Y', strtotime($cert['expiry_date'])) : '' ?></p>
                        <?php if($cert['credential_id']): ?><p class="mb-1 text-muted"><i class="fas fa-id-card"></i> ID: <?= esc($cert['credential_id']) ?></p><?php endif; ?>
                        <?php if($cert['credential_url']): ?><p class="mb-1"><a href="<?= esc($cert['credential_url']) ?>" target="_blank"><i class="fas fa-external-link-alt"></i> View Credential</a></p><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- GitHub Stats -->
            <?php if (!empty($github['github_username'])): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <h5><i class="fab fa-github"></i> GitHub Profile</h5>
                    <p><a href="https://github.com/<?= esc($github['github_username']) ?>" target="_blank">@<?= esc($github['github_username']) ?></a></p>
                    <div class="row text-center">
                        <div class="col-4">
                            <strong><?= esc($github['repo_count']) ?></strong><br>
                            <small>Repositories</small>
                        </div>
                        <div class="col-4">
                            <strong><?= esc($github['commit_count']) ?></strong><br>
                            <small>Commits</small>
                        </div>
                        <div class="col-4">
                            <strong><?= esc($github['github_score']) ?>/10</strong><br>
                            <small>Score</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= view('Layouts/recruiter_footer') ?>





