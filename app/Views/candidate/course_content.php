<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($module['title']) ?> - Offline Ready</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .offline-badge { position: fixed; top: 20px; right: 20px; z-index: 1000; padding: 10px 20px; border-radius: 25px; font-weight: 600; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .offline-badge.online { background: #28a745; color: white; }
        .offline-badge.offline { background: #dc3545; color: white; }
        .module-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .lesson-card { background: white; border-radius: 12px; padding: 30px; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .lesson-number { background: #667eea; color: white; width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 15px; }
        .lesson-content { background: #f8f9fa; padding: 25px; border-radius: 10px; line-height: 1.8; margin: 20px 0; white-space: pre-line; }
        .resource-link { display: inline-block; background: #007bff; color: white; padding: 10px 20px; border-radius: 8px; margin: 8px 8px 8px 0; text-decoration: none; transition: all 0.3s; }
        .resource-link:hover { background: #0056b3; color: white; text-decoration: none; transform: translateY(-2px); }
        .exercise-item { background: #fff3cd; padding: 15px; margin: 10px 0; border-radius: 8px; border-left: 4px solid #ffc107; }
        .section-title { color: #667eea; font-weight: 600; margin-top: 25px; margin-bottom: 15px; }
        .btn-back { background: rgba(102, 126, 234, 0.1); color: #667eea; border: 2px solid #667eea; }
        .btn-back:hover { background: #667eea; color: white; }
    </style>
</head>
<body>
    <div class="offline-badge online" id="offlineStatus">📡 Online</div>
    
    <div class="container mt-5 mb-5">
        <div class="module-header">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="text-uppercase" style="opacity: 0.8;">Module <?= $module['module_number'] ?></h6>
                    <h2><?= esc($module['title']) ?></h2>
                    <p class="mb-0 mt-2"><?= esc($module['description']) ?></p>
                    <span class="badge badge-light mt-3">⏱️ <?= $module['duration_weeks'] ?> weeks</span>
                </div>
                <a href="<?= base_url('career-transition/course') ?>" class="btn btn-light">← All Modules</a>
            </div>
        </div>

        <?php if (empty($lessons)): ?>
        <div class="alert alert-warning">No lessons available for this module.</div>
        <?php else: ?>
        
        <?php foreach ($lessons as $lesson): ?>
        <div class="lesson-card">
            <div class="d-flex align-items-center mb-3">
                <span class="lesson-number"><?= $lesson['lesson_number'] ?></span>
                <h3 class="mb-0"><?= esc($lesson['title']) ?></h3>
            </div>
            
            <div class="lesson-content">
                <?= nl2br(esc($lesson['content'])) ?>
            </div>

            <div class="mt-4">
                <h5 class="section-title">📚 Learning Resources</h5>
                <div>
                    <?php 
                    $resources = is_string($lesson['resources']) ? json_decode($lesson['resources'], true) : $lesson['resources'];
                    if (!empty($resources) && is_array($resources)):
                        foreach ($resources as $resource): 
                            if (filter_var($resource, FILTER_VALIDATE_URL)): ?>
                                <a href="<?= esc($resource) ?>" target="_blank" class="resource-link">🔗 <?= esc(parse_url($resource, PHP_URL_HOST)) ?></a>
                            <?php else: ?>
                                <span class="resource-link" style="background: #6c757d;"><?= esc($resource) ?></span>
                            <?php endif;
                        endforeach;
                    else: ?>
                        <p class="text-muted">No additional resources for this lesson.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mt-4">
                <h5 class="section-title">✏️ Practice Exercises</h5>
                <?php 
                $exercises = is_string($lesson['exercises']) ? json_decode($lesson['exercises'], true) : $lesson['exercises'];
                if (!empty($exercises) && is_array($exercises)):
                    foreach ($exercises as $index => $exercise): ?>
                        <div class="exercise-item">
                            <strong>Exercise <?= $index + 1 ?>:</strong> <?= esc($exercise) ?>
                        </div>
                    <?php endforeach;
                else: ?>
                    <p class="text-muted">No exercises for this lesson.</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="text-center mt-4">
            <a href="<?= base_url('career-transition/course') ?>" class="btn btn-back btn-lg">← Back to All Modules</a>
        </div>

        <?php endif; ?>
    </div>

    <script>
        // Offline detection
        function updateOfflineStatus() {
            const badge = document.getElementById('offlineStatus');
            if (navigator.onLine) {
                badge.textContent = '📡 Online';
                badge.className = 'offline-badge online';
            } else {
                badge.textContent = '📴 Offline Mode';
                badge.className = 'offline-badge offline';
            }
        }
        
        window.addEventListener('online', updateOfflineStatus);
        window.addEventListener('offline', updateOfflineStatus);
        updateOfflineStatus();

        // Cache content for offline access
        if ('caches' in window) {
            caches.open('course-content-v1').then(cache => {
                cache.add(window.location.href);
            });
        }
    </script>
</body>
</html>
