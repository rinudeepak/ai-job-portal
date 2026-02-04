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


