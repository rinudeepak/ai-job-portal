<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= esc($post['title']) ?> | HireMatrix Blog</title>
    <link rel="stylesheet" href="<?= base_url('jobboard/css/custom-bs.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/fontawesome-all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/hirematrix-style.css') ?>">
</head>
<?= view('Layouts/public_header', ['body_class' => 'blog-detail-page']) ?>

<?php
$resolveAssetUrl = static function (string $path): string {
    $path = trim($path);
    if ($path === '') return '';
    if (preg_match('#^https?://#i', $path) || str_starts_with($path, '//')) return $path;
    return base_url(ltrim($path, '/'));
};
?>

<section class="py-5" style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 55%, #fef3c7 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('blog') ?>" class="text-decoration-none">Blog</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Article</li>
                    </ol>
                </nav>
                <h1 class="display-4 fw-bold mb-3"><?= esc($post['title']) ?></h1>
                <div class="d-flex align-items-center text-muted mb-4">
                    <div class="me-4">
                        <i class="fas fa-user-edit me-1"></i> By <strong><?= esc($post['author_name'] ?? 'HireMatrix Team') ?></strong>
                    </div>
                    <div>
                        <i class="fas fa-calendar-alt me-1"></i> <?= esc(date('M d, Y', strtotime((string) ($post['published_at'] ?: $post['created_at'])))) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (!empty($post['cover_image'])): ?>
                    <div class="mb-5">
                        <img src="<?= esc($resolveAssetUrl($post['cover_image'])) ?>" class="img-fluid rounded shadow" alt="<?= esc($post['title']) ?>">
                    </div>
                <?php endif; ?>

                <div class="blog-content mb-5 fs-5">
                    <!-- Outputting TinyMCE HTML: esc() is removed here so tags render correctly -->
                    <?= $post['content'] ?>
                </div>

                <hr class="my-5">
                
                <?php if (!empty($recentPosts)): ?>
                    <div class="mt-5">
                        <h3 class="fw-bold mb-4">Recent Articles</h3>
                        <div class="row g-4">
                            <?php foreach ($recentPosts as $recent): ?>
                                <div class="col-md-4">
                                    <div class="h-100 p-3 border rounded shadow-sm bg-white">
                                        <div class="text-muted small mb-2"><?= esc(date('M d, Y', strtotime((string) ($recent['published_at'] ?: $recent['created_at'])))) ?></div>
                                        <h5 class="fw-bold mb-3" style="font-size: 1.1rem;">
                                            <a href="<?= base_url('blog/' . $recent['slug']) ?>" class="text-dark text-decoration-none">
                                                <?= esc($recent['title']) ?>
                                            </a>
                                        </h5>
                                        <a href="<?= base_url('blog/' . $recent['slug']) ?>" class="small text-primary text-decoration-none fw-bold">Read More &rarr;</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?= view('Layouts/public_footer') ?>
</body>
</html>