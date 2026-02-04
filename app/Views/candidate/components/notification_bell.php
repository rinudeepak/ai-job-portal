<!-- Notification Bell Component -->
<div class="dropdown" style="position: relative; display: inline-block;">
    <a href="#" class="notification-bell" id="notificationBell" data-toggle="dropdown">
        <i class="fas fa-bell" style="font-size: 24px; color: #333;"></i>
        <?php if ($unread_count > 0): ?>
            <span class="notification-badge"><?= $unread_count > 9 ? '9+' : $unread_count ?></span>
        <?php endif; ?>
    </a>
    
    <div class="dropdown-menu dropdown-menu-right notification-dropdown" aria-labelledby="notificationBell">
        <div class="notification-header">
            <h6>Notifications (<?= $unread_count ?>)</h6>
            <?php if ($unread_count > 0): ?>
                <a href="<?= base_url('notifications/mark-all-read') ?>" class="mark-all-read">
                    Mark all as read
                </a>
            <?php endif; ?>
        </div>
        
        <div class="notification-list">
            <?php if (empty($notifications)): ?>
                <div class="notification-item text-center">
                    <p class="text-muted">No notifications</p>
                </div>
            <?php else: ?>
                <?php foreach (array_slice($notifications, 0, 5) as $notification): ?>
                    <?php $config = model('NotificationModel')->getNotificationConfig($notification['type']); ?>
                    <a href="<?= base_url('notifications/mark-read/' . $notification['id']) ?>" 
                       class="notification-item <?= $notification['is_read'] ? 'read' : 'unread' ?>">
                        <div class="notification-icon <?= $config['color'] ?>">
                            <i class="<?= $config['icon'] ?>"></i>
                        </div>
                        <div class="notification-content">
                            <h6><?= esc($notification['title']) ?></h6>
                            <p><?= esc($notification['message']) ?></p>
                            <small class="text-muted">
                                <?= time_ago($notification['created_at']) ?>
                            </small>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if (count($notifications) > 5): ?>
            <div class="notification-footer">
                <a href="<?= base_url('notifications') ?>">View All Notifications</a>
            </div>
        <?php endif; ?>
    </div>
</div>


