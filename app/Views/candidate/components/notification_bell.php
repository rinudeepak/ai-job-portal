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

<style>
.notification-bell {
    position: relative;
    cursor: pointer;
}

.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 11px;
    font-weight: bold;
    min-width: 20px;
    text-align: center;
}

.notification-dropdown {
    width: 400px;
    max-height: 500px;
    overflow: hidden;
    padding: 0;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
}

.notification-header {
    padding: 15px;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8f9fa;
}

.notification-header h6 {
    margin: 0;
    font-weight: 600;
}

.mark-all-read {
    font-size: 12px;
    color: #007bff;
    text-decoration: none;
}

.notification-list {
    max-height: 400px;
    overflow-y: auto;
}

.notification-item {
    display: flex;
    padding: 15px;
    border-bottom: 1px solid #f0f0f0;
    text-decoration: none;
    color: inherit;
    transition: background 0.2s;
}

.notification-item:hover {
    background: #f8f9fa;
}

.notification-item.unread {
    background: #e3f2fd;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    flex-shrink: 0;
}

.notification-icon.warning {
    background: #fff3cd;
    color: #856404;
}

.notification-icon.info {
    background: #d1ecf1;
    color: #0c5460;
}

.notification-icon.danger {
    background: #f8d7da;
    color: #721c24;
}

.notification-icon.success {
    background: #d4edda;
    color: #155724;
}

.notification-content {
    flex: 1;
}

.notification-content h6 {
    margin: 0 0 5px 0;
    font-size: 14px;
    font-weight: 600;
}

.notification-content p {
    margin: 0 0 5px 0;
    font-size: 13px;
    color: #666;
}

.notification-footer {
    padding: 12px;
    text-align: center;
    border-top: 1px solid #e0e0e0;
    background: #f8f9fa;
}

.notification-footer a {
    font-size: 13px;
    font-weight: 600;
    color: #007bff;
    text-decoration: none;
}
</style>
