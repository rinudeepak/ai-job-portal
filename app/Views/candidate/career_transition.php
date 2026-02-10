<?= view('Layouts/candidate_header', ['title' => 'Career Transition AI']) ?>

<div class="container mt-5 mb-5">
    <h2>🚀 Career Transition AI</h2>
    
    <?php if (!$transition): ?>
    <div class="card mt-4">
        <div class="card-body">
            <h5>Start Your Career Transition Journey</h5>
            <p class="text-muted">We'll analyze the gap between your current skills and target role, then create a personalized learning path.</p>
            <form action="<?= base_url('career-transition/create') ?>" method="post">
                <div class="mb-3">
                    <label>Current Role</label>
                    <input type="text" name="current_role" class="form-control" value="<?= $currentRole ?? '' ?>" placeholder="e.g., PHP Developer" required>
                    <small class="text-muted">Auto-detected from your profile</small>
                </div>
                <div class="mb-3">
                    <label>Target Role (Job you want to transition to)</label>
                    <input type="text" name="target_role" class="form-control" placeholder="e.g., Next.js Developer" required>
                </div>
                <button type="submit" class="btn btn-primary">🚀 Generate Personalized Roadmap</button>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6>Progress</h6>
                    <h2><?= $transition['progress_percentage'] ?>%</h2>
                    <p class="text-muted"><?= $transition['current_role'] ?> → <?= $transition['target_role'] ?></p>
                    <a href="<?= base_url('career-transition/course') ?>" class="btn btn-success btn-sm mt-2">📖 View Full Course</a>
                    <button class="btn btn-warning btn-sm mt-2" onclick="if(confirm('Are you sure you want to change your career path? Your current progress will be lost.')) window.location.href='<?= base_url('career-transition/reset') ?>'">🔄 Change Career Path</button>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h6>Skill Gaps</h6>
                    <ul>
                        <?php foreach (json_decode($transition['skill_gaps'], true) as $skill): ?>
                        <li><?= $skill ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <h5>Daily Tasks (5-10 min each)</h5>
            <?php foreach ($tasks as $task): ?>
            <div class="card task-card mb-3 <?= $task['is_completed'] ? 'task-completed' : '' ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6>Day <?= $task['day_number'] ?>: <?= $task['task_title'] ?></h6>
                            <p class="mb-0"><?= $task['task_description'] ?></p>
                            <small class="text-muted"><?= $task['duration_minutes'] ?> minutes</small>
                        </div>
                        <?php if (!$task['is_completed']): ?>
                        <button class="btn btn-sm btn-success" onclick="completeTask(<?= $task['id'] ?>)">✓ Complete</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
    .task-card { border-left: 4px solid #007bff; }
    .task-completed { opacity: 0.6; text-decoration: line-through; }
</style>

<script>
    function completeTask(taskId) {
        fetch('<?= base_url('career-transition/complete/') ?>' + taskId, { 
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) location.reload();
        });
    }
</script>

<?= view('layouts/candidate_footer') ?>
