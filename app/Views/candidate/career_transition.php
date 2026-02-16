<?= view('Layouts/candidate_header', ['title' => 'Career Transition AI']) ?>

<div class="career-transition-wrapper">
<div class="container mt-5 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>🚀 Career Transition AI</h2>
        <a href="<?= base_url('career-transition/history') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-history"></i> View History
        </a>
    </div>
    
    <?php if (!$transition): ?>
    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-warning alert-dismissible fade show mt-4" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mt-4" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php endif; ?>
    
    <div class="card mt-4">
        <div class="card-body">
            <h5>Start Your Career Transition Journey</h5>
            <p class="text-muted">We'll analyze the gap between your current skills and target role, then create a personalized learning path.</p>
            
            <div class="alert alert-info">
                <i class="fas fa-lightbulb"></i> <strong>Smart Tip:</strong> If you've explored this career path before, we'll instantly restore your previous course - no AI generation needed!
            </div>
            
            <form action="<?= base_url('career-transition/create') ?>" method="post" id="transitionForm">
                <div class="mb-3">
                    <label>Current Role</label>
                    <input type="text" name="current_role" class="form-control" value="<?= $currentRole ?? '' ?>" placeholder="e.g., PHP Developer" required>
                    <small class="text-muted">Auto-detected from your profile</small>
                </div>
                <div class="mb-3">
                    <label>Target Role (Job you want to transition to)</label>
                    <input list="role-suggestions" type="text" name="target_role" class="form-control" value="<?= $targetRole ?? '' ?>" placeholder="e.g., Next.js Developer" required>
                    <datalist id="role-suggestions">
                        <option value="Next.js Developer">
                        <option value="React Developer">
                        <option value="DevOps Engineer">
                        <option value="DevOps Developer">
                        <option value="Data Scientist">
                        <option value="Data Analyst">
                        <option value="Full Stack Developer">
                        <option value="Frontend Developer">
                        <option value="Backend Developer">
                        <option value="Python Developer">
                        <option value="Java Developer">
                        <option value="Node.js Developer">
                        <option value="Cloud Engineer">
                        <option value="Machine Learning Engineer">
                    </datalist>
                    <small class="text-muted">Select from suggestions or type your own</small>
                </div>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <span id="btnText">🚀 Generate Personalized Roadmap</span>
                    <span id="btnLoading" style="display:none;">
                        <span class="spinner-border spinner-border-sm" role="status"></span>
                        Generating AI course... (60-90 seconds)
                    </span>
                </button>
            </form>
            <script>
            document.getElementById('transitionForm').addEventListener('submit', function() {
                document.getElementById('btnText').style.display = 'none';
                document.getElementById('btnLoading').style.display = 'inline-block';
                document.getElementById('submitBtn').disabled = true;
            });
            </script>
        </div>
    </div>
    
    <?php else: ?>
    <!-- Active transition with completed content -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <h6>Career Path</h6>
                    <h5><?= $transition['current_role'] ?></h5>
                    <div style="font-size: 24px; margin: 10px 0;">↓</div>
                    <h5 class="text-success"><?= $transition['target_role'] ?></h5>
                    
                    <?php if (isset($transition['reactivation_count']) && $transition['reactivation_count'] > 0): ?>
                        <div class="badge badge-info mt-2">
                            <i class="fas fa-redo"></i> Reused <?= $transition['reactivation_count'] ?> time(s)
                        </div>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('career-transition/course') ?>" class="btn btn-success btn-sm mt-3 d-block">
                        📖 View Full Course
                    </a>
                    <a href="<?= base_url('career-transition/download-pdf') ?>" class="btn btn-info btn-sm mt-2 d-block">
                        <i class="fas fa-file-pdf"></i> Download PDF
                    </a>
                    <button class="btn btn-warning btn-sm mt-2 d-block" onclick="if(confirm('Save current path to history and start a new one? Your progress will be preserved.')) window.location.href='<?= base_url('career-transition/reset') ?>'">
                        🔄 Change Career Path
                    </button>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h6>Skill Gaps</h6>
                    <ul>
                        <?php 
                        $skillGaps = json_decode($transition['skill_gaps'], true);
                        if ($skillGaps && count($skillGaps) > 0):
                            foreach ($skillGaps as $skill): ?>
                            <li><?= $skill ?></li>
                        <?php endforeach;
                        else: ?>
                            <li class="text-muted">Analyzing skills...</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <h5>Daily Learning Tasks (5-10 min each)</h5>
            <p class="text-muted">Each task corresponds to a specific module and lesson in your course.</p>
            <?php if (count($tasks) > 0): ?>
                <?php foreach ($tasks as $task): ?>
                <div class="card task-card mb-3 <?= $task['is_completed'] ? 'task-completed' : '' ?>">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div class="flex-grow-1">
                                <h6>Day <?= $task['day_number'] ?>: <?= $task['task_title'] ?></h6>
                                <p class="mb-1"><?= $task['task_description'] ?></p>
                                <small class="text-muted">⏱️ <?= $task['duration_minutes'] ?> minutes</small>
                                <?php if (!empty($task['module_number'])): ?>
                                    <span class="badge badge-info ml-2">
                                        Module <?= $task['module_number'] ?>
                                        <?= !empty($task['lesson_number']) ? ' - Lesson ' . $task['lesson_number'] : '' ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div>
                                <?php if (!$task['is_completed']): ?>
                                <button class="btn btn-sm btn-success" onclick="completeTask(<?= $task['id'] ?>)">
                                    ✓ Complete
                                </button>
                                <?php else: ?>
                                <span class="badge badge-success">✓ Done</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info">
                    <p class="mb-0">🔄 Tasks are being generated. Please refresh the page in a moment.</p>
                </div>
                <script>
                // Auto-refresh if no tasks yet
                setTimeout(function() { location.reload(); }, 5000);
                </script>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>
</div>

<style>
    .career-transition-wrapper {
        min-height: calc(100vh - 200px);
    }
    .task-card { 
        border-left: 4px solid #007bff; 
        transition: all 0.3s ease;
    }
    .task-completed { 
        opacity: 0.6; 
        border-left-color: #28a745;
    }
    .task-completed h6,
    .task-completed p {
        text-decoration: line-through;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

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
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to mark task as complete. Please try again.');
        });
    }
</script>

<?= view('Layouts/candidate_footer') ?>