<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Modules - Offline Ready</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background: #f5f7fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .offline-badge { position: fixed; top: 20px; right: 20px; z-index: 1000; padding: 10px 20px; border-radius: 25px; font-weight: 600; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .offline-badge.online { background: #28a745; color: white; }
        .offline-badge.offline { background: #dc3545; color: white; }
        .header-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px; padding: 30px; margin-bottom: 30px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .download-section { background: rgba(255,255,255,0.15); border-radius: 10px; padding: 20px; margin-top: 20px; }
        .download-btn { background: white; color: #667eea; border: none; padding: 12px 30px; border-radius: 25px; font-weight: 600; transition: all 0.3s; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }
        .download-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,0.3); background: #f8f9fa; color: #667eea; }
        .download-btn i { margin-right: 8px; }
        .module-card { background: white; border-radius: 12px; padding: 25px; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.3s; cursor: pointer; border-left: 5px solid #667eea; }
        .module-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        .module-number { background: #667eea; color: white; width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold; margin-right: 20px; }
        .module-duration { background: #e3f2fd; color: #1976d2; padding: 5px 15px; border-radius: 20px; font-size: 14px; display: inline-block; }
        .btn-back { background: rgba(255,255,255,0.2); color: white; border: 2px solid white; }
        .btn-back:hover { background: white; color: #667eea; }
        .download-info { font-size: 14px; margin-top: 10px; opacity: 0.9; }
        .download-spinner { display: none; }
        .download-spinner.active { display: inline-block; }
    </style>
</head>
<body>
    <div class="offline-badge online" id="offlineStatus">📡 Online</div>
    
    <div class="container mt-5 mb-5">
        <div class="header-card">
            <div class="d-flex justify-content-between align-items-start flex-wrap">
                <div class="flex-grow-1">
                    <h2>📚 Your Learning Journey</h2>
                    <h4><?= esc($transition['current_role']) ?> → <?= esc($transition['target_role']) ?></h4>
                    <p class="mb-0 mt-2">Click on any module to start learning. Content is saved for offline access.</p>
                    
                    <div class="download-section">
                        <h5><i class="fas fa-download"></i> Download for Offline Study</h5>
                        <p class="download-info mb-3">Download your complete course as a professionally formatted PDF with all modules, lessons, resources, and exercises. Perfect for offline studying or printing.</p>
                        <button class="download-btn" onclick="downloadPDF()">
                            <i class="fas fa-file-pdf"></i>
                            <span id="downloadText">Download Complete Course PDF</span>
                            <span class="download-spinner" id="downloadSpinner">
                                <i class="fas fa-spinner fa-spin"></i> Generating PDF...
                            </span>
                        </button>
                    </div>
                </div>
                <div class="mt-3 mt-md-0">
                    <a href="<?= base_url('career-transition') ?>" class="btn btn-back">← Back</a>
                </div>
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
                badge.textContent = '🔴 Offline';
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

        // Download PDF
        function downloadPDF() {
            const downloadText = document.getElementById('downloadText');
            const downloadSpinner = document.getElementById('downloadSpinner');
            
            // Show loading state
            downloadText.style.display = 'none';
            downloadSpinner.classList.add('active');
            
            // Redirect to download
            window.location.href = '<?= base_url('career-transition/download-pdf') ?>';
            
            // Reset button after delay (in case download doesn't start)
            setTimeout(() => {
                downloadText.style.display = 'inline';
                downloadSpinner.classList.remove('active');
            }, 3000);
        }

        // Service Worker for offline support
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('<?= base_url('sw.js') ?>').catch(() => {});
        }
    </script>
</body>
</html>