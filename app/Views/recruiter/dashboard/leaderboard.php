<?= view('layouts/recruiter_header', ['title' => 'Skill Leaderboard']) ?>

<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-trophy"></i> Candidate Skill Leaderboard</h2>
            <p class="text-muted">Rank candidates by technical and overall performance</p>
        </div>
        <div>
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <a href="<?= base_url('admin/dashboard/export-excel?type=leaderboard') ?>" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Export to Excel
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters & Sorting</h6>
        </div>
        <div class="card-body">
            <form method="get" action="<?= base_url('admin/dashboard/leaderboard') ?>" id="filterForm">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="sort_by">Sort By</label>
                            <select name="sort_by" id="sort_by" class="form-control">
                                <option value="technical_score" <?= ($filters['sort_by'] ?? '') === 'technical_score' ? 'selected' : '' ?>>
                                    Technical Score
                                </option>
                                <option value="overall_rating" <?= ($filters['sort_by'] ?? '') === 'overall_rating' ? 'selected' : '' ?>>
                                    Overall AI Rating
                                </option>
                                <option value="communication_score" <?= ($filters['sort_by'] ?? '') === 'communication_score' ? 'selected' : '' ?>>
                                    Communication Score
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="skill">Filter by Skill</label>
                            <select name="skill" id="skill" class="form-control">
                                <option value="">All Skills</option>
                                <?php foreach ($skills as $skill): ?>
                                    <option value="<?= esc($skill) ?>" <?= ($filters['skill'] ?? '') === $skill ? 'selected' : '' ?>>
                                        <?= esc($skill) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="job_id">Filter by Job</label>
                            <select name="job_id" id="job_id" class="form-control">
                                <option value="">All Jobs</option>
                                <?php foreach ($jobs as $job): ?>
                                    <option value="<?= $job['id'] ?>" <?= ($filters['job_id'] ?? '') == $job['id'] ? 'selected' : '' ?>>
                                        <?= esc($job['title']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-filter"></i> Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Current Filters Display -->
    <?php if (!empty($filters['skill']) || !empty($filters['job_id']) || !empty($filters['sort_by'])): ?>
        <div class="alert alert-info alert-dismissible fade show">
            <strong>Active Filters:</strong>
            <?php if (!empty($filters['sort_by'])): ?>
                <span class="badge badge-primary">Sort: <?= ucwords(str_replace('_', ' ', $filters['sort_by'])) ?></span>
            <?php endif; ?>
            <?php if (!empty($filters['skill'])): ?>
                <span class="badge badge-success">Skill: <?= esc($filters['skill']) ?></span>
            <?php endif; ?>
            <?php if (!empty($filters['job_id'])): ?>
                <span class="badge badge-info">Job Selected</span>
            <?php endif; ?>
            <a href="<?= base_url('admin/dashboard/leaderboard') ?>" class="btn btn-sm btn-outline-secondary ml-2">
                Clear All
            </a>
        </div>
    <?php endif; ?>

    <!-- Leaderboard -->
    <div class="card shadow">
        <div class="card-header py-3 bg-gradient-primary text-white">
            <h6 class="m-0 font-weight-bold">
                <i class="fas fa-crown"></i> Top Performers - 
                <?= ucwords(str_replace('_', ' ', $filters['sort_by'] ?? 'technical_score')) ?>
            </h6>
        </div>
        <div class="card-body">
            <?php if (empty($candidates)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-trophy fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No candidates found with AI interview scores</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover leaderboard-table">
                        <thead class="thead-dark">
                            <tr>
                                <th width="60">Rank</th>
                                <th>Candidate</th>
                                <th>Job Position</th>
                                <th class="text-center">Technical</th>
                                <th class="text-center">Communication</th>
                                <th class="text-center">Overall Rating</th>
                                <th class="text-center">Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($candidates as $candidate): ?>
                                <tr class="<?= $candidate['rank'] <= 3 ? 'top-performer' : '' ?>">
                                    <td class="rank-cell">
                                        <?php if ($candidate['rank'] === 1): ?>
                                            <span class="rank-badge gold">
                                                <i class="fas fa-crown"></i> 1
                                            </span>
                                        <?php elseif ($candidate['rank'] === 2): ?>
                                            <span class="rank-badge silver">
                                                <i class="fas fa-medal"></i> 2
                                            </span>
                                        <?php elseif ($candidate['rank'] === 3): ?>
                                            <span class="rank-badge bronze">
                                                <i class="fas fa-medal"></i> 3
                                            </span>
                                        <?php else: ?>
                                            <span class="rank-number"><?= $candidate['rank'] ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="candidate-info">
                                            <strong><?= esc($candidate['name']) ?></strong>
                                            <br>
                                            <small class="text-muted"><?= esc($candidate['email']) ?></small>
                                        </div>
                                    </td>
                                    <td><?= esc($candidate['job_title']) ?></td>
                                    <td class="text-center">
                                        <div class="score-display">
                                            <span class="score-value <?= $candidate['technical_score'] >= 80 ? 'text-success' : ($candidate['technical_score'] >= 60 ? 'text-warning' : 'text-danger') ?>">
                                                <?= number_format($candidate['technical_score'] ?? 0, 1) ?>
                                            </span>
                                            <div class="score-bar">
                                                <div class="score-fill" style="width: <?= ($candidate['technical_score'] ?? 0) ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="score-display">
                                            <span class="score-value <?= $candidate['communication_score'] >= 80 ? 'text-success' : ($candidate['communication_score'] >= 60 ? 'text-warning' : 'text-danger') ?>">
                                                <?= number_format($candidate['communication_score'] ?? 0, 1) ?>
                                            </span>
                                            <div class="score-bar">
                                                <div class="score-fill" style="width: <?= ($candidate['communication_score'] ?? 0) ?>%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="overall-rating">
                                            <span class="rating-badge <?= $candidate['ai_interview_score'] >= 80 ? 'badge-success' : ($candidate['ai_interview_score'] >= 60 ? 'badge-warning' : 'badge-danger') ?>">
                                                <?= number_format($candidate['ai_interview_score'] ?? 0, 1) ?>
                                            </span>
                                            <div class="rating-stars">
                                                <?php 
                                                $stars = round(($candidate['ai_interview_score'] ?? 0) / 20);
                                                for ($i = 1; $i <= 5; $i++): 
                                                ?>
                                                    <i class="fas fa-star <?= $i <= $stars ? 'text-warning' : 'text-muted' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <?php
                                        $statusColors = [
                                            'ai_interview_completed' => 'info',
                                            'shortlisted' => 'primary',
                                            'hr_interview_scheduled' => 'warning',
                                            'selected' => 'success',
                                            'rejected' => 'danger'
                                        ];
                                        $color = $statusColors[$candidate['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge badge-<?= $color ?>">
                                            <?= ucwords(str_replace('_', ' ', $candidate['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('admin/applications/view/' . $candidate['id']) ?>" 
                                               class="btn btn-sm btn-primary" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($candidate['status'] === 'ai_interview_completed'): ?>
                                                <a href="<?= base_url('admin/applications/shortlist/' . $candidate['id']) ?>" 
                                                   class="btn btn-sm btn-success" title="Shortlist">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    <?= $pager->links() ?>
                </div>

                <!-- Statistics Summary -->
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="text-muted">Average Technical Score</h5>
                                <h3 class="text-primary">
                                    <?php
                                    $avgTech = !empty($candidates) ? array_sum(array_column($candidates, 'technical_score')) / count($candidates) : 0;
                                    echo number_format($avgTech, 1);
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="text-muted">Average Communication Score</h5>
                                <h3 class="text-success">
                                    <?php
                                    $avgComm = !empty($candidates) ? array_sum(array_column($candidates, 'communication_score')) / count($candidates) : 0;
                                    echo number_format($avgComm, 1);
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h5 class="text-muted">Average Overall Rating</h5>
                                <h3 class="text-warning">
                                    <?php
                                    $avgOverall = !empty($candidates) ? array_sum(array_column($candidates, 'ai_interview_score')) / count($candidates) : 0;
                                    echo number_format($avgOverall, 1);
                                    ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Leaderboard Styles */
.leaderboard-table {
    font-size: 0.95rem;
}

.top-performer {
    background-color: #fff8e1;
}

.rank-cell {
    text-align: center;
    vertical-align: middle;
}

.rank-badge {
    display: inline-block;
    padding: 8px 12px;
    border-radius: 50%;
    font-weight: bold;
    font-size: 0.9rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.rank-badge.gold {
    background: linear-gradient(135deg, #ffd700, #ffed4e);
    color: #000;
}

.rank-badge.silver {
    background: linear-gradient(135deg, #c0c0c0, #e8e8e8);
    color: #000;
}

.rank-badge.bronze {
    background: linear-gradient(135deg, #cd7f32, #e09856);
    color: #fff;
}

.rank-number {
    display: inline-block;
    padding: 8px 12px;
    background: #f8f9fa;
    border-radius: 50%;
    font-weight: bold;
    color: #6c757d;
}

.candidate-info {
    line-height: 1.4;
}

.score-display {
    min-width: 80px;
}

.score-value {
    font-weight: bold;
    font-size: 1.1rem;
    display: block;
    margin-bottom: 4px;
}

.score-bar {
    height: 6px;
    background: #e9ecef;
    border-radius: 3px;
    overflow: hidden;
}

.score-fill {
    height: 100%;
    background: linear-gradient(to right, #4e73df, #224abe);
    transition: width 0.3s ease;
}

.overall-rating {
    text-align: center;
}

.rating-badge {
    font-size: 1.2rem;
    padding: 0.5rem 0.75rem;
    display: inline-block;
    margin-bottom: 4px;
}

.rating-stars {
    font-size: 0.8rem;
}

.bg-gradient-primary {
    background: linear-gradient(to right, #4e73df, #224abe);
}

.card {
    border: none;
    border-radius: 0.5rem;
}

.btn-group .btn {
    margin-right: 2px;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .leaderboard-table {
        font-size: 0.85rem;
    }
    
    .score-display {
        min-width: 60px;
    }
    
    .rank-badge {
        padding: 6px 10px;
        font-size: 0.8rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on select change
    const selects = document.querySelectorAll('#filterForm select');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            // Optional: Auto-submit on change
            // document.getElementById('filterForm').submit();
        });
    });
    
    // Animate score bars on load
    const scoreBars = document.querySelectorAll('.score-fill');
    scoreBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
});
</script>

<?= view('layouts/recruiter_footer') ?>