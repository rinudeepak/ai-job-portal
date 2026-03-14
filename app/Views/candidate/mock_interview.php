<?= view('Layouts/candidate_header', ['title' => 'Mock Interview']) ?>

<?php
$application = $application ?? [];
$mockInterview = $mockInterview ?? [];
$jobTitle = (string) ($application['job_title'] ?? 'Mock Interview');
$companyName = trim((string) ($application['company'] ?? ''));
$prepSource = (string) ($mockInterview['source'] ?? 'fallback');
$status = (string) ($application['status'] ?? '');
$aiPolicy = strtoupper((string) ($application['ai_interview_policy'] ?? 'REQUIRED_HARD'));
?>

<section class="section-hero overlay inner-page bg-image" style="background-image: url('<?= base_url('jobboard/images/hero_1.jpg') ?>');" id="home-section">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <h1 class="text-white font-weight-bold">Detailed Mock Interview</h1>
                <div class="custom-breadcrumbs">
                    <a href="<?= base_url('candidate/dashboard') ?>">Home</a>
                    <span class="mx-2 slash">/</span>
                    <a href="<?= base_url('candidate/applications') ?>">Applications</a>
                    <span class="mx-2 slash">/</span>
                    <span class="text-white"><strong><?= esc($jobTitle) ?></strong></span>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container py-5">
    <style>
        .mock-interview-shell {
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr);
            gap: 24px;
            align-items: start;
        }
        .mock-card,
        .mock-sidebar {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        }
        .mock-sidebar {
            position: sticky;
            top: 110px;
            padding: 22px;
        }
        .mock-main {
            display: grid;
            gap: 20px;
        }
        .mock-card {
            padding: 24px;
        }
        .mock-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            margin-bottom: 14px;
            border: 1px solid #bfdbfe;
            background: #eff6ff;
            color: #1d4ed8;
        }
        .mock-eyebrow.is-ai {
            border-color: #bbf7d0;
            background: #dcfce7;
            color: #166534;
        }
        .mock-page-title {
            font-size: 2rem;
            line-height: 1.15;
            margin-bottom: 8px;
            color: #111827;
        }
        .mock-submeta {
            color: #6b7280;
            margin-bottom: 14px;
        }
        .mock-intro {
            color: #475569;
            font-size: 1rem;
            line-height: 1.7;
            margin-bottom: 0;
        }
        .mock-chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .mock-chip {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            border-radius: 999px;
            border: 1px solid #bfdbfe;
            background: #f8fbff;
            color: #1d4ed8;
            font-size: 13px;
            font-weight: 700;
        }
        .mock-section-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 14px;
        }
        .mock-round + .mock-round {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #eef2f7;
        }
        .mock-round-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
        }
        .mock-round-objective {
            color: #64748b;
            margin-bottom: 14px;
        }
        .mock-question {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 16px;
            background: #fbfdff;
        }
        .mock-question + .mock-question {
            margin-top: 12px;
        }
        .mock-question-title {
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
        }
        .mock-question-meta {
            display: grid;
            gap: 8px;
            color: #475569;
            font-size: .95rem;
        }
        .mock-note-list {
            margin: 0;
            padding-left: 18px;
            color: #475569;
        }
        .mock-note-list li + li {
            margin-top: 8px;
        }
        .mock-sidebar h3 {
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 12px;
            color: #111827;
        }
        .mock-sidebar-meta {
            display: grid;
            gap: 12px;
            margin-bottom: 20px;
            color: #475569;
        }
        .mock-sidebar-meta strong {
            display: block;
            color: #111827;
            font-size: .82rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 4px;
        }
        .mock-sidebar-actions {
            display: grid;
            gap: 10px;
        }
        @media (max-width: 991.98px) {
            .mock-interview-shell {
                grid-template-columns: 1fr;
            }
            .mock-sidebar {
                position: static;
            }
        }
    </style>

    <div class="mock-interview-shell">
        <aside class="mock-sidebar">
            <h3>Application Context</h3>
            <div class="mock-sidebar-meta">
                <div>
                    <strong>Role</strong>
                    <?= esc($jobTitle) ?>
                </div>
                <?php if ($companyName !== ''): ?>
                    <div>
                        <strong>Company</strong>
                        <?= esc($companyName) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <strong>Status</strong>
                    <?= esc(ucwords(str_replace('_', ' ', $status))) ?>
                </div>
                <?php if (!empty($application['resume_version_title'])): ?>
                    <div>
                        <strong>Resume Used</strong>
                        <?= esc($application['resume_version_title']) ?>
                    </div>
                <?php endif; ?>
                <div>
                    <strong>Interview Flow</strong>
                    <?= $aiPolicy === 'OFF' ? 'Recruiter / HR flow' : 'AI-assisted screening flow' ?>
                </div>
            </div>

            <div class="mock-sidebar-actions">
                <a href="<?= base_url('candidate/applications') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back to Applications
                </a>
                <?php if ($status === 'applied' && $aiPolicy !== 'OFF'): ?>
                    <a href="<?= base_url('interview/start/' . (int) $application['id']) ?>" class="btn btn-success btn-sm">
                        <i class="fas fa-video"></i> Start AI Interview
                    </a>
                <?php elseif ($status === 'shortlisted'): ?>
                    <a href="<?= base_url('candidate/book-slot/' . (int) $application['id']) ?>" class="btn btn-warning btn-sm">
                        <i class="fas fa-calendar-plus"></i> Book Interview Slot
                    </a>
                <?php endif; ?>
            </div>
        </aside>

        <div class="mock-main">
            <section class="mock-card">
                <span class="mock-eyebrow <?= $prepSource === 'ai' ? 'is-ai' : '' ?>">
                    <?= $prepSource === 'ai' ? 'AI-generated mock interview' : 'Structured fallback mock interview' ?>
                </span>
                <h2 class="mock-page-title"><?= esc($mockInterview['title'] ?? ($jobTitle . ' Mock Interview')) ?></h2>
                <div class="mock-submeta">
                    <?= esc($jobTitle) ?>
                    <?php if ($companyName !== ''): ?>
                        <span class="mx-2">&bull;</span><?= esc($companyName) ?>
                    <?php endif; ?>
                </div>
                <p class="mock-intro"><?= esc($mockInterview['intro'] ?? 'Use this page to rehearse the most likely questions and answer patterns for the next interview step.') ?></p>
            </section>

            <?php if (!empty($mockInterview['focus_skills'])): ?>
                <section class="mock-card">
                    <div class="mock-section-title">Focus Skills</div>
                    <div class="mock-chip-list">
                        <?php foreach ((array) $mockInterview['focus_skills'] as $skill): ?>
                            <span class="mock-chip"><?= esc($skill) ?></span>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <section class="mock-card">
                <div class="mock-section-title">Mock Interview Rounds</div>
                <?php foreach ((array) ($mockInterview['rounds'] ?? []) as $round): ?>
                    <div class="mock-round">
                        <div class="mock-round-title"><?= esc($round['name'] ?? 'Interview Round') ?></div>
                        <?php if (!empty($round['objective'])): ?>
                            <div class="mock-round-objective"><?= esc($round['objective']) ?></div>
                        <?php endif; ?>

                        <?php foreach ((array) ($round['questions'] ?? []) as $question): ?>
                            <div class="mock-question">
                                <div class="mock-question-title"><?= esc($question['question'] ?? '') ?></div>
                                <div class="mock-question-meta">
                                    <?php if (!empty($question['why_it_matters'])): ?>
                                        <div><strong>Why it matters:</strong> <?= esc($question['why_it_matters']) ?></div>
                                    <?php endif; ?>
                                    <?php if (!empty($question['answer_tip'])): ?>
                                        <div><strong>Answer tip:</strong> <?= esc($question['answer_tip']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </section>

            <div class="row">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <section class="mock-card h-100">
                        <div class="mock-section-title">Answer Framework</div>
                        <ul class="mock-note-list">
                            <?php foreach ((array) ($mockInterview['answer_framework'] ?? []) as $item): ?>
                                <li><?= esc($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                </div>
                <div class="col-lg-6">
                    <section class="mock-card h-100">
                        <div class="mock-section-title">What Interviewers Will Evaluate</div>
                        <ul class="mock-note-list">
                            <?php foreach ((array) ($mockInterview['evaluation_points'] ?? []) as $item): ?>
                                <li><?= esc($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                </div>
            </div>

            <section class="mock-card">
                <div class="mock-section-title">Final Rehearsal Checklist</div>
                <ul class="mock-note-list">
                    <?php foreach ((array) ($mockInterview['final_checklist'] ?? []) as $item): ?>
                        <li><?= esc($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        </div>
    </div>
</div>

<?= view('Layouts/candidate_footer') ?>
