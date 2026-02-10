<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline Course Content</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .offline-indicator { position: fixed; top: 10px; right: 10px; background: #28a745; color: white; padding: 10px 15px; border-radius: 5px; font-size: 14px; z-index: 1000; }
        .offline-indicator.offline { background: #dc3545; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; flex-wrap: wrap; gap: 15px; }
        .header h2 { font-size: 24px; color: #333; }
        .btn { padding: 10px 20px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px; display: inline-block; border: none; cursor: pointer; }
        .btn:hover { background: #5a6268; }
        .alert { padding: 15px; background: #d1ecf1; border: 1px solid #bee5eb; border-radius: 5px; margin-bottom: 20px; color: #0c5460; }
        .card { background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; overflow: hidden; }
        .card-header { background: #28a745; color: white; padding: 20px; }
        .card-header h4 { margin-bottom: 10px; font-size: 20px; }
        .card-header p { margin: 0; opacity: 0.9; }
        .card-body { padding: 20px; }
        .module-card { border-left: 5px solid #28a745; }
        .lesson-content { background: #f8f9fa; padding: 20px; border-radius: 8px; white-space: pre-line; line-height: 1.6; margin-bottom: 15px; }
        .resource-badge { background: #007bff; color: white; padding: 8px 12px; border-radius: 5px; margin: 5px; display: inline-block; text-decoration: none; font-size: 14px; }
        .resource-badge:hover { background: #0056b3; }
        .exercise-item { background: #fff3cd; padding: 12px; margin: 8px 0; border-radius: 5px; border-left: 3px solid #ffc107; }
        h5 { color: #007bff; margin: 20px 0 10px 0; }
        h6 { margin: 15px 0 10px 0; color: #333; }
        hr { border: none; border-top: 1px solid #dee2e6; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="offline-indicator" id="status">📚 Offline Ready</div>
    <div class="container">
        <div class="header">
            <h2>📖 Complete Course: <?= $transition['current_role'] ?? '' ?> → <?= $transition['target_role'] ?? '' ?></h2>
            <a href="<?= base_url('career-transition') ?>" class="btn">← Back to Tasks</a>
        </div>

        <?php if (empty($modules)): ?>
        <div class="alert">No course content available. Generate a roadmap first.</div>
        <?php else: ?>
        
        <?php foreach ($modules as $module): ?>
        <div class="card module-card">
            <div class="card-header">
                <h4>Module <?= $module['module_number'] ?>: <?= $module['title'] ?></h4>
                <p><?= $module['description'] ?> (<?= $module['duration_weeks'] ?> weeks)</p>
            </div>
            <div class="card-body">
                <?php foreach ($module['lessons'] as $lesson): ?>
                <div>
                    <h5>Lesson <?= $lesson['lesson_number'] ?>: <?= $lesson['title'] ?></h5>
                    
                    <div class="lesson-content">
                        <?= $lesson['content'] ?>
                    </div>

                    <div>
                        <h6>📚 Resources:</h6>
                        <?php 
                        $resources = json_decode($lesson['resources'], true) ?? [];
                        foreach ($resources as $resource): 
                            if (strpos($resource, 'http') === 0): ?>
                                <a href="<?= $resource ?>" target="_blank" class="resource-badge"><?= $resource ?></a>
                            <?php else: ?>
                                <span class="resource-badge"><?= $resource ?></span>
                            <?php endif;
                        endforeach; ?>
                    </div>

                    <div>
                        <h6>✏️ Exercises:</h6>
                        <?php 
                        $exercises = json_decode($lesson['exercises'], true) ?? [];
                        foreach ($exercises as $exercise): ?>
                            <div class="exercise-item">✓ <?= $exercise ?></div>
                        <?php endforeach; ?>
                    </div>
                    <hr>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php endif; ?>
    </div>

    <script>
        const statusEl = document.getElementById('status');
        
        function updateStatus() {
            if (navigator.onLine) {
                statusEl.textContent = '📚 Offline Ready';
                statusEl.classList.remove('offline');
            } else {
                statusEl.textContent = '📴 Offline Mode';
                statusEl.classList.add('offline');
            }
        }
        
        window.addEventListener('online', updateStatus);
        window.addEventListener('offline', updateStatus);
        updateStatus();
    </script>
</body>
</html>
