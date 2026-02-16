<?= view('Layouts/candidate_header', ['title' => 'Career Transition History']) ?>

<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12">
            <h2>
                <i class="fas fa-history"></i> Your Career Transition History
            </h2>
            <p class="text-muted">View and reactivate your previous learning paths</p>
            
            <a href="<?= base_url('career-transition') ?>" class="btn btn-outline-primary mb-4">
                <i class="fas fa-arrow-left"></i> Back to Current Path
            </a>
        </div>
    </div>

    <?php if (empty($transitions)): ?>
        <div class="row">
            <div class="col-12">
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                        <h5>No Career Transitions Yet</h5>
                        <p class="text-muted">Start your first career transition to see it here</p>
                        <a href="<?= base_url('career-transition') ?>" class="btn btn-primary">
                            Start Career Transition
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($transitions as $transition): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100 <?= $transition['status'] === 'active' ? 'border-success' : '' ?>">
                    <div class="card-header <?= $transition['status'] === 'active' ? 'bg-success text-white' : 'bg-light' ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <?php if ($transition['status'] === 'active'): ?>
                                    <i class="fas fa-check-circle"></i> Active Path
                                <?php else: ?>
                                    <i class="fas fa-archive"></i> Saved Path
                                <?php endif; ?>
                            </h5>
                            <span class="badge badge-<?= $transition['status'] === 'active' ? 'light' : 'secondary' ?>">
                                Created: <?= date('M d, Y', strtotime($transition['created_at'])) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <!-- Career Path -->
                        <div class="text-center mb-3">
                            <h6 class="text-primary"><?= esc($transition['current_role']) ?></h6>
                            <i class="fas fa-arrow-down fa-2x my-2 text-muted"></i>
                            <h6 class="text-success"><?= esc($transition['target_role']) ?></h6>
                        </div>

                        <!-- Skill Gaps -->
                        <div class="mt-3">
                            <h6><i class="fas fa-tasks"></i> Skill Gaps:</h6>
                            <ul class="small mb-0">
                                <?php 
                                $skillGaps = json_decode($transition['skill_gaps'], true);
                                if ($skillGaps && is_array($skillGaps)):
                                    foreach (array_slice($skillGaps, 0, 3) as $skill): ?>
                                        <li><?= esc($skill) ?></li>
                                    <?php endforeach;
                                    if (count($skillGaps) > 3): ?>
                                        <li class="text-muted">+ <?= count($skillGaps) - 3 ?> more</li>
                                    <?php endif;
                                else: ?>
                                    <li class="text-muted">No skill gaps recorded</li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <!-- Statistics -->
                        <div class="mt-3">
                            <div class="row text-center small">
                                <div class="col-6">
                                    <i class="fas fa-redo text-info"></i>
                                    <div><strong><?= $transition['reactivation_count'] ?? 0 ?></strong></div>
                                    <div class="text-muted">Times Reused</div>
                                </div>
                                <div class="col-6">
                                    <?php if ($transition['status'] === 'inactive' && !empty($transition['deactivated_at'])): ?>
                                        <i class="fas fa-calendar-times text-warning"></i>
                                        <div><strong><?= date('M d, Y', strtotime($transition['deactivated_at'])) ?></strong></div>
                                        <div class="text-muted">Deactivated</div>
                                    <?php elseif (!empty($transition['reactivated_at'])): ?>
                                        <i class="fas fa-calendar-check text-success"></i>
                                        <div><strong><?= date('M d, Y', strtotime($transition['reactivated_at'])) ?></strong></div>
                                        <div class="text-muted">Last Active</div>
                                    <?php else: ?>
                                        <i class="fas fa-calendar-plus text-primary"></i>
                                        <div><strong><?= date('M d, Y', strtotime($transition['created_at'])) ?></strong></div>
                                        <div class="text-muted">Created</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white">
                        <?php if ($transition['status'] === 'active'): ?>
                            <button class="btn btn-success btn-block" disabled>
                                <i class="fas fa-check"></i> Currently Active
                            </button>
                        <?php else: ?>
                            <a href="<?= base_url('career-transition/reactivate/' . $transition['id']) ?>" 
                               class="btn btn-primary btn-block"
                               onclick="return confirm('Reactivate this career path? Your current active path will be saved to history.')">
                                <i class="fas fa-play"></i> Reactivate This Path
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Info Box -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <h5><i class="fas fa-info-circle"></i> How It Works</h5>
                    <ul class="mb-0">
                        <li><strong>Reactivate:</strong> Click "Reactivate This Path" to resume a previous learning journey</li>
                        <li><strong>No API Calls:</strong> Reactivating uses your saved course content - instant and free!</li>
                        <li><strong>Fresh Start:</strong> All task progress is reset when you reactivate a path</li>
                        <li><strong>Multiple Paths:</strong> You can switch between different career paths anytime</li>
                        <li><strong>History Saved:</strong> All your transitions are preserved forever</li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.card.border-success {
    border-width: 2px;
}
</style>

<?= view('Layouts/candidate_footer') ?>