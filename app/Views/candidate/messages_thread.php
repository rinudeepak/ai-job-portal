<?= view('Layouts/candidate_header', ['title' => 'Messages']) ?>
<?php
$messages = $messages ?? [];
$recruiter = $recruiter ?? [];
$recruiterId = (int) ($recruiterId ?? 0);
$applicationId = (int) ($applicationId ?? 0);
?>

<div class="applications-jobboard">
    <section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1 class="text-white font-weight-bold">Messages</h1>
                    <div class="custom-breadcrumbs">
                        <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                        <span class="mx-2 slash">/</span>
                        <a href="<?= base_url('notifications') ?>">Notifications</a>
                        <span class="mx-2 slash">/</span>
                        <span class="text-white"><strong><?= esc($recruiter['name'] ?? 'Recruiter') ?></strong></span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="container content-wrap pb-5">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Conversation with <?= esc($recruiter['name'] ?? 'Recruiter') ?></h5>
            </div>
            <div class="card-body" style="max-height: 420px; overflow-y: auto;">
                <?php if (empty($messages)): ?>
                    <p class="text-muted mb-0">No messages yet.</p>
                <?php else: ?>
                    <?php foreach ($messages as $item): ?>
                        <?php $fromCandidate = ($item['sender_role'] ?? '') === 'candidate'; ?>
                        <div class="mb-3 d-flex <?= $fromCandidate ? 'justify-content-end' : 'justify-content-start' ?>">
                            <div style="max-width: 78%; padding: 10px 12px; border-radius: 10px; <?= $fromCandidate ? 'background:#dbeafe;' : 'background:#f3f4f6;' ?>">
                                <div style="font-size: 12px; color:#6b7280; margin-bottom:4px;">
                                    <?= $fromCandidate ? 'You' : esc($recruiter['name'] ?? 'Recruiter') ?> • <?= date('M d, Y h:i A', strtotime($item['created_at'])) ?>
                                </div>
                                <div><?= nl2br(esc($item['message'] ?? '')) ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-white">
                <form method="post" action="<?= base_url('candidate/messages/' . $recruiterId . '/reply') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="application_id" value="<?= $applicationId ?>">
                    <div class="form-group mb-2">
                        <textarea name="message" class="form-control" rows="3" maxlength="1000" placeholder="Write your reply..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Reply</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('Layouts/candidate_footer') ?>

