<?php
$resolveAssetUrl = static function (string $path): string {
    $path = trim($path);
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path) || str_starts_with($path, '//')) {
        return $path;
    }
    return base_url(ltrim($path, '/'));
};
?>
<section class="site-section bg-light pt-0">
    <div class="container">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="section-title mb-3">Latest Career Insights</h2>
                <p class="lead">Stay updated with our expert advice and industry trends.</p>
            </div>
        </div>
        <div class="row g-4">
            <?php if (!empty($blogPosts)): ?>
                <?php foreach ($blogPosts as $post): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="job-card h-100 dashboard-card">
                            <div class="job-card-icon">
                                <?php if (!empty($post['cover_image'])): ?>
                                    <img src="<?= esc($resolveAssetUrl($post['cover_image'])) ?>" alt="<?= esc($post['title']) ?>">
                                <?php else: ?>
                                    <span><i class="fas fa-newspaper"></i></span>
                                <?php endif; ?>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-3 mt-2">
                                <span class="badge badge-primary"><?= !empty($post['featured']) ? 'Featured' : 'Blog' ?></span>
                                <span class="text-muted small"><?= esc(date('M d, Y', strtotime((string) ($post['published_at'] ?: $post['created_at'])))) ?></span>
                            </div>
                            <h3 class="job-card-title"><?= esc($post['title']) ?></h3>
                            <div class="mb-2 small text-muted">
                                <i class="fas fa-user-edit me-1"></i> By <?= esc($post['author_name'] ?? 'HireMatrix Team') ?>
                            </div>
                            <p class="text-muted mb-4"><?= esc(strip_tags((string) $post['excerpt'])) ?></p>
                            <a href="<?= base_url('blog/' . $post['slug']) ?>" class="view-details mt-auto">Read Article &rarr;</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="job-card text-center">
                        <h3 class="job-card-title">No blog posts available yet.</h3>
                        <p class="text-muted mb-0">Check back soon for career advice and hiring insights!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>