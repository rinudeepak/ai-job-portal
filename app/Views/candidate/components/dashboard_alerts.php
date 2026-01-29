<?php if (!empty($notifications)): ?>
    <section class="featured-job-area feature-padding">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="section-tittle text-center mb-4">
                        <h2><i class="fas fa-exclamation-triangle"></i> Pending Actions</h2>
                        <p>Complete these tasks to move forward with your applications</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php 
                // Group notifications by type for better display
                $grouped = [];
                foreach ($notifications as $notification) {
                    $grouped[$notification['type']][] = $notification;
                }
                
                foreach ($grouped as $type => $items):
                    $config = model('NotificationModel')->getNotificationConfig($type);
                ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="alert-card alert-<?= $config['color'] ?>">
                            <div class="alert-icon">
                                <i class="<?= $config['icon'] ?> fa-2x"></i>
                            </div>
                            <div class="alert-content">
                                <h5><?= $config['title'] ?></h5>
                                <p><?= $items[0]['message'] ?></p>
                                <?php if (count($items) > 1): ?>
                                    <span class="badge badge-<?= $config['color'] ?>">
                                        <?= count($items) ?> pending
                                    </span>
                                <?php endif; ?>
                                <?php if ($items[0]['action_link']): ?>
                                    <a href="<?= base_url('notifications/mark-read/' . $items[0]['id']) ?>" 
                                       class="btn btn-sm btn-<?= $config['color'] ?> mt-2">
                                        Take Action <i class="fas fa-arrow-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

<style>
.alert-card {
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    background: white;
    transition: transform 0.2s;
    height: 100%;
}

.alert-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.alert-card.alert-warning {
    border-left-color: #ffc107;
    background: linear-gradient(135deg, #fff8e1 0%, #fffef7 100%);
}

.alert-card.alert-info {
    border-left-color: #17a2b8;
    background: linear-gradient(135deg, #e3f2fd 0%, #f8fdff 100%);
}

.alert-card.alert-danger {
    border-left-color: #dc3545;
    background: linear-gradient(135deg, #ffebee 0%, #fff5f5 100%);
}

.alert-card.alert-success {
    border-left-color: #28a745;
    background: linear-gradient(135deg, #e8f5e9 0%, #f8fff8 100%);
}

.alert-icon {
    text-align: center;
    margin-bottom: 15px;
}

.alert-card.alert-warning .alert-icon {
    color: #ffc107;
}

.alert-card.alert-info .alert-icon {
    color: #17a2b8;
}

.alert-card.alert-danger .alert-icon {
    color: #dc3545;
}

.alert-card.alert-success .alert-icon {
    color: #28a745;
}

.alert-content h5 {
    font-weight: 600;
    margin-bottom: 10px;
}

.alert-content p {
    color: #666;
    font-size: 14px;
    margin-bottom: 10px;
}
</style>
