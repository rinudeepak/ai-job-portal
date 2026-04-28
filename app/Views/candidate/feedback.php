<?= view('Layouts/candidate_header', ['title' => 'Feedback']) ?>

<div class="book-slot-jobboard">
    <div class="container">

        <!-- HEADER -->
        <div class="page-board-header page-board-header-tight">
            <div class="page-board-copy">
                <span class="page-board-kicker">
                    <i class="fas fa-comment-dots"></i> Feedback
                </span>
                <h1 class="page-board-title">Share Your Experience</h1>
                <p class="page-board-subtitle">
                    Your feedback helps us improve the platform experience.
                </p>
            </div>
        </div>

        <!-- SUCCESS -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <!-- CARD -->
        <div class="feedback-wrapper">

            <form method="post" action="<?= base_url('feedback/save') ?>" class="feedback-card">
                <?= csrf_field() ?>

                <!-- USER INFO -->
                <div class="user-info mb-4">
                    <div>
                        <strong><?= esc($user['name']) ?></strong><br>
                        <small class="text-muted"><?= esc($user['email']) ?></small>
                    </div>
                    <span class="badge bg-primary"><?= ucfirst($user['role']) ?></span>
                </div>

                <!-- RATING -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Rating</label>

                    <div class="rating-stars">
                        <?php for($i=5;$i>=1;$i--): ?>
                            <input type="radio" name="rating" id="star<?= $i ?>" value="<?= $i ?>" required>
                            <label for="star<?= $i ?>">★</label>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- MESSAGE -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">Your Feedback</label>
                    <textarea name="message" rows="4" class="form-control"
                        placeholder="Write your feedback..." required></textarea>
                </div>

                <!-- SUBMIT -->
                <div class="text-center">
                    <button type="submit" class="btn submit-btn">
                        <i class="fas fa-paper-plane me-2"></i>Submit Feedback
                    </button>
                </div>

            </form>

        </div>

    </div>
</div>

<?= view('Layouts/candidate_footer') ?>
<style>
.feedback-wrapper {
    display: flex;
    justify-content: center;
    margin-top: 30px;
}

.feedback-card {
    width: 100%;
    max-width: 600px;
    background: #fff;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    border: 1px solid #f1f1f1;
}

/* User info */
.user-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f9fafb;
    padding: 12px 15px;
    border-radius: 10px;
}

/* Input */
.form-control {
    border-radius: 10px;
    padding: 12px;
    border: 1px solid #e5e7eb;
}

.form-control:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 2px rgba(79,70,229,0.1);
}

/* ⭐ Rating */
.rating-stars {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating-stars input {
    display: none;
}

.rating-stars label {
    font-size: 28px;
    color: #d1d5db;
    cursor: pointer;
    transition: 0.2s;
}

.rating-stars input:checked ~ label,
.rating-stars label:hover,
.rating-stars label:hover ~ label {
    color: #fbbf24;
}

/* Button */
.submit-btn {
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: #fff;
    padding: 12px 30px;
    border-radius: 10px;
    border: none;
    font-weight: 500;
    transition: 0.3s;
}

.submit-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(79,70,229,0.25);
}
    </style>