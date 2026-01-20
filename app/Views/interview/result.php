<?= view('layouts/candidate_header', ['title' => 'Interview Result']) ?>

<div class="container mt-5">
    <div class="card">
        <div class="card-body text-center">
            <h3>AI Interview Result</h3>

            <p><strong>Technical Score:</strong> <?= $technical_score ?>%</p>
            <p><strong>Communication Score:</strong> <?= $communication_score ?>%</p>
            <p><strong>Overall Rating:</strong> <?= $overall_rating ?>%</p>

            <h4 class="mt-3">
                <?= strtoupper($ai_decision) ?>
            </h4>

            <p><?= $ai_feedback['decision_reasoning'] ?></p>
        </div>
    </div>
</div>

<?= view('layouts/candidate_footer') ?>
