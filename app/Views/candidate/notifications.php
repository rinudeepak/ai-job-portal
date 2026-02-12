<?= view('Layouts/candidate_header', ['title' => 'Notifications']) ?>

<section class="contact-section pt-5">
    <div class="container">
        <div class="row">
            <div class="col-12 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="contact-title">All Notifications</h2>
                    <?php if ($unread_count > 0): ?>
                        <a href="<?= base_url('notifications/mark-all-read') ?>" class="btn btn-primary">
                            <i class="fas fa-check-double"></i> Mark All as Read
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-8 offset-lg-2">
                <?php if (empty($notifications)): ?>
                    <div class="card text-center">
                        <div class="card-body py-5">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <h5>No Notifications</h5>
                            <p class="text-muted">You're all caught up!</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?php foreach ($notifications as $notification): ?>
                        <?php $config = model('NotificationModel')->getNotificationConfig($notification['type']); ?>
                        
                        <div class="card mb-3 <?= $notification['is_read'] ? '' : 'border-primary' ?>">
                            <div class="card-body">
                                <div class="d-flex">
                                    <div class="notification-icon <?= $config['color'] ?> mr-3">
                                        <i class="<?= $config['icon'] ?>"></i>
                                    </div>
                                    
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <h5 class="mb-1">
                                                <?= esc($notification['title']) ?>
                                                <?php if (!$notification['is_read']): ?>
                                                    <span class="badge badge-primary">New</span>
                                                <?php endif; ?>
                                            </h5>
                                            <small class="text-muted">
                                                <?= time_ago($notification['created_at']) ?>
                                            </small>
                                        </div>
                                        
                                        <p class="mb-2"><?= esc($notification['message']) ?></p>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <?php if ($notification['action_link']): ?>
                                                <a href="<?= base_url('notifications/mark-read/' . $notification['id']) ?>" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    Take Action <i class="fas fa-arrow-right"></i>
                                                </a>
                                            <?php else: ?>
                                                <span></span>
                                            <?php endif; ?>
                                            
                                            <div>
                                                <?php if (!$notification['is_read']): ?>
                                                    <a href="<?= base_url('notifications/mark-read/' . $notification['id']) ?>" 
                                                       class="btn btn-sm btn-link">
                                                        <i class="fas fa-check"></i> Mark as Read
                                                    </a>
                                                <?php endif; ?>
                                                <a href="<?= base_url('notifications/delete/' . $notification['id']) ?>" 
                                                   class="btn btn-sm btn-link text-danger"
                                                   onclick="return confirm('Delete this notification?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?= view('Layouts/candidate_footer') ?>
