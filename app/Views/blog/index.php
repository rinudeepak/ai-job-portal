<?php $posts = $posts ?? []; ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>HireMatrix Blog</title>
    <meta name="description" content="Career advice, hiring insights, and job search guidance from HireMatrix.">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/custom-bs.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/fontawesome-all.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/hirematrix-style.css?v=' . @filemtime(FCPATH . 'jobboard/css/hirematrix-style.css')) ?>">
    <link rel="stylesheet" href="<?= base_url('jobboard/css/responsive.css?v=' . @filemtime(FCPATH . 'jobboard/css/responsive.css')) ?>">
</head>
<?= view('Layouts/public_header', ['body_class' => 'blog-page']) ?>

<section class="py-5" style="background: linear-gradient(135deg, #eff6ff 0%, #ffffff 55%, #fef3c7 100%);">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <div class="ai-badge justify-content-center mb-3">
                    <i class="fas fa-newspaper"></i>
                    HireMatrix Blog
                </div>
                <h1 class="section-title mb-3">Career advice and hiring insights</h1>
                <p class="section-subtitle">Articles managed by admin to help candidates grow and recruiters hire smarter.</p>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <?php if (!empty($posts)): ?>
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-6 col-xl-4">
                        <article class="job-card h-100">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="badge badge-primary"><?= !empty($post['featured']) ? 'Featured' : 'Blog' ?></span>
                                <span class="text-muted small"><?= esc(date('M d, Y', strtotime((string) ($post['published_at'] ?: $post['created_at'])))) ?></span>
                            </div>
                            <h2 class="job-card-title" style="font-size: 1.35rem; min-height: 3.2rem;"><?= esc($post['title']) ?></h2>
                            <div class="mb-3 small text-muted">
                                <i class="fas fa-user-edit me-1"></i> By <?= esc($post['author_name'] ?? 'HireMatrix Team') ?>
                            </div>
                            <p class="text-muted mb-4"><?= esc(strip_tags((string) $post['excerpt'])) ?></p>
                            <a href="<?= base_url('blog/' . $post['slug']) ?>" class="view-details mt-auto">Read Article &rarr;</a>
                        </article>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="job-card text-center">
                        <h2 class="job-card-title">Blog coming soon</h2>
                        <p class="text-muted mb-0">Admin can publish articles here once the `blog_posts` table is added and posts are created.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?= view('Layouts/public_footer') ?>
</body>
</html>
