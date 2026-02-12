<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Modules - Offline Ready</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <style>
        body { background: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .offline-badge { position: fixed; top: 20px; right: 20px; z-index: 1000; padding: 10px 20px; border-radius: 25px; font-weight: 600; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .offline-badge.online { background: #28a745; color: white; }
        .offline-badge.offline { background: #dc3545; color: white; }
        .header-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .module-card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.3s; cursor: pointer; border-left: 5px solid #667eea; }
        .module-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        .module-number { background: #667eea; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; margin-right: 20px; }
        .module-duration { background: #e3f2fd; color: #1976d2; padding: 5px 15px; border-radius: 20px; font-size: 14px; display: inline-block; }
        .btn-back { background: rgba(255,255,255,0.2); color: white; border: 2px solid white; }
        .btn-back:hover { background: white; color: #667eea; }
    </style>
</head>
<body>
    <div class="offline-badge online" id="offlineStatus">📡 Online</div>
    
    <div class="container mt-5 mb-5">
        <div class="header-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2>📚 Your Learning Journey</h2>
                    <h4><?= esc($transition['current_role']) ?> → <?= esc($transition['target_role']) ?></h4>
                    <p class="mb-0 mt-2">Click on any module to start learning. Content is saved for offline access.</p>
                </div>
                <a href="<?= base_url('career-transition') ?>" class="btn btn-back">← Back</a>
            </div>
        </div>

        <?php if (empty($modules)): ?>
        <div class="alert alert-info">
            <h5>No course content available yet.</h5>
            <p>Please generate your career transition roadmap first.</p>
        </div>
        <?php else: ?>
        
        <?php foreach ($modules as $module): ?>
        <div class="module-card" onclick="loadModule(<?= $module['id'] ?>)">
            <div class="d-flex align-items-start">
                <div class="module-number"><?= $module['module_number'] ?></div>
                <div class="flex-grow-1">
                    <h4 class="mb-2"><?= esc($module['title']) ?></h4>
                    <p class="text-muted mb-3"><?= esc($module['description']) ?></p>
                    <span class="module-duration">⏱️ <?= $module['duration_weeks'] ?> weeks</span>
                </div>
                <div class="text-right">
                    <span class="badge badge-primary" style="font-size: 16px;">→</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

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
                badge.textContent = '📴 Offline';
                badge.className = 'offline-badge offline';
            }
        }
        
        window.addEventListener('online', updateOfflineStatus);
        window.addEventListener('offline', updateOfflineStatus);
        updateOfflineStatus();

        // Load module
        function loadModule(moduleId) {
            window.location.href = '<?= base_url('career-transition/module/') ?>' + moduleId;
        }

        // Service Worker for offline support
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('<?= base_url('sw.js') ?>').catch(() => {});
        }
    </script>
</body>
</html>
