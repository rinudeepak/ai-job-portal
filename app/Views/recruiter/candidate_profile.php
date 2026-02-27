<?= view('Layouts/recruiter_header', ['title' => 'Candidate Profile']) ?>

<div class="container mt-5 mb-5">
    <?php
    $applicationId = (int) (service('request')->getGet('application_id') ?? 0);
    $jobId = (int) (service('request')->getGet('job_id') ?? 0);
    $showContact = (int) (service('request')->getGet('show_contact') ?? 0) === 1;
    $contactViewUrl = base_url('recruiter/candidate/' . $candidate['id'] . '/view-contact')
        . '?application_id=' . $applicationId
        . '&job_id=' . $jobId;
    $messages = $messages ?? [];
    $recruiterNote = $recruiterNote ?? null;
    ?>
    <style>
        .candidate-summary-card .candidate-name {
            margin-bottom: 12px;
            font-weight: 700;
        }
        .candidate-summary-card .candidate-meta {
            margin-bottom: 14px;
            color: #6c757d;
            font-size: 0.95rem;
        }
        .candidate-summary-card .candidate-meta p {
            margin-bottom: 6px;
        }
        .candidate-summary-actions {
            margin-top: 12px;
        }
        .candidate-summary-actions .btn {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 10px;
            border-radius: 8px;
            font-weight: 600;
        }
        .candidate-summary-actions .btn:last-child {
            margin-bottom: 0;
        }
    </style>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm candidate-summary-card">
                <div class="card-body text-center">
                    <?php if (!empty($candidate['profile_photo'])): ?>
                        <img src="<?= base_url($candidate['profile_photo']) ?>" alt="Profile" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;">
                    <?php else: ?>
                        <div class="rounded-circle mx-auto mb-3" style="width: 120px; height: 120px; border: 1px solid #dee2e6; background: transparent;"></div>
                    <?php endif; ?>
                    <h4 class="candidate-name"><?= esc($candidate['name']) ?></h4>
                    <div class="candidate-meta">
                        <?php if ($showContact): ?>
                            <p><i class="fas fa-envelope"></i> <?= esc($candidate['email']) ?></p>
                            <?php if ($candidate['phone']): ?>
                                <p><i class="fas fa-phone"></i> <?= esc($candidate['phone']) ?></p>
                            <?php endif; ?>
                        <?php endif; ?>
                        <?php if ($candidate['location']): ?>
                            <p><i class="fas fa-map-marker-alt"></i> <?= esc($candidate['location']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php
                    $resumeUrl = base_url('recruiter/candidate/' . $candidate['id'] . '/download-resume');
                    $resumeUrl .= '?application_id=' . $applicationId . '&job_id=' . $jobId;
                    ?>
                    <div class="candidate-summary-actions">
                        <?php if (!$showContact): ?>
                            <a href="<?= $contactViewUrl ?>" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-address-card"></i> View Contact
                            </a>
                        <?php endif; ?>
                        <?php if($candidate['resume_path']): ?>
                            <a href="<?= $resumeUrl ?>" class="btn btn-primary btn-sm"><i class="fas fa-download"></i> Download Resume</a>
                        <?php endif; ?>
                    </div>
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

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6><i class="fas fa-sticky-note"></i> Recruiter Notes & Tags</h6>
                    <?php if (!empty($recruiterNote['tags'])): ?>
                        <div class="mb-2">
                            <?php foreach (explode(',', (string) $recruiterNote['tags']) as $tag): ?>
                                <?php $trimmedTag = trim($tag); ?>
                                <?php if ($trimmedTag !== ''): ?>
                                    <span class="badge badge-light border mr-1 mb-1"><?= esc($trimmedTag) ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" action="<?= base_url('recruiter/candidate/' . $candidate['id'] . '/save-notes') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="application_id" value="<?= $applicationId ?>">
                        <input type="hidden" name="job_id" value="<?= $jobId ?>">
                        <input type="hidden" name="show_contact" value="<?= $showContact ? 1 : 0 ?>">
                        <div class="form-group mb-2">
                            <label class="small text-muted">Tags (comma separated)</label>
                            <input type="text" name="tags" class="form-control" maxlength="255" value="<?= esc($recruiterNote['tags'] ?? '') ?>" placeholder="e.g. Strong communication, Backend, Immediate joiner">
                        </div>
                        <div class="form-group mb-2">
                            <label class="small text-muted">Private Notes</label>
                            <textarea name="notes" class="form-control" rows="4" maxlength="5000" placeholder="Add private notes for this candidate..."><?= esc($recruiterNote['notes'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-dark">
                            <i class="fas fa-save mr-1"></i> Save Notes
                        </button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6><i class="icon-mail_outline"></i> Message Candidate</h6>
                    <?php if (!empty($messages)): ?>
                        <div class="mb-2 p-2 border rounded" style="max-height: 180px; overflow-y: auto;">
                            <?php foreach (array_slice($messages, -8) as $msg): ?>
                                <?php $isRecruiterMsg = ($msg['sender_role'] ?? '') === 'recruiter'; ?>
                                <div class="mb-2">
                                    <small class="text-muted d-block">
                                        <?= $isRecruiterMsg ? 'You' : esc($candidate['name']) ?> • <?= date('M d, h:i A', strtotime($msg['created_at'])) ?>
                                    </small>
                                    <div><?= nl2br(esc($msg['message'] ?? '')) ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" action="<?= base_url('recruiter/candidate/' . $candidate['id'] . '/send-message') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="application_id" value="<?= $applicationId ?>">
                        <input type="hidden" name="job_id" value="<?= $jobId ?>">
                        <input type="hidden" name="show_contact" value="<?= $showContact ? 1 : 0 ?>">
                        <div class="form-group mb-2">
                            <textarea name="message" class="form-control" rows="4" maxlength="1000" placeholder="Write a message to candidate..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="icon-send mr-1"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
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





