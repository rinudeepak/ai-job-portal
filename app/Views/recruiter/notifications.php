<?= view('Layouts/recruiter_header', ['title' => 'Notifications']) ?>

<div class="container py-5">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">All Notifications</h2>
                <?php if ($unread_count > 0): ?>
                    <a href="<?= base_url('notifications/mark-all-read') ?>" class="btn btn-primary">
                        <span class="icon-check mr-1"></span> Mark All as Read
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-8 offset-lg-2">
            <?php if (empty($notifications)): ?>
                <div class="card text-center">
                    <div class="card-body py-5">
                        <span class="icon-bell-slash text-muted mb-3 d-inline-block" style="font-size: 42px;"></span>
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
                                    <i class="<?= esc($config['icon']) ?>"></i>
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
                                            <a href="<?= base_url('notifications/mark-read/' . $notification['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                <?= esc($config['action_text'] ?? 'Open') ?> <span class="icon-arrow-right ml-1"></span>
                                            </a>
                                        <?php else: ?>
                                            <span></span>
                                        <?php endif; ?>

                                        <div>
                                            <?php if (!$notification['is_read']): ?>
                                                <a href="<?= base_url('notifications/mark-read/' . $notification['id']) ?>" class="btn btn-sm btn-link">
                                                    <span class="icon-check mr-1"></span> Mark as Read
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= base_url('notifications/delete/' . $notification['id']) ?>" class="btn btn-sm btn-link text-danger" onclick="return confirm('Delete this notification?')">
                                                <span class="icon-trash mr-1"></span> Delete
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

<?= view('Layouts/recruiter_footer') ?>
