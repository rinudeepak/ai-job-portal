<?= view('Layouts/candidate_header', ['title' => 'AI Career Mentor']) ?>

<div class="career-transition-jobboard">
    <section class="career-transition-content">
        <div class="container">
            <div class="page-board-header page-board-header-tight">
                <div class="page-board-copy">
                    <span class="page-board-kicker"><i class="fas fa-crown"></i> <?= esc($subscription['plan_name']) ?></span>
                    <h1 class="page-board-title">AI Career Mentor</h1>
                    <p class="page-board-subtitle">Your personal AI career coach — active until <?= date('M d, Y', strtotime($subscription['end_date'])) ?></p>
                </div>
                <div class="page-board-actions">
                    <a href="<?= base_url('premium-mentor/plans') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-up mr-1"></i> Upgrade Plan
                    </a>
                </div>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            <?php endif; ?>

            <div class="career-transition-simple-layout">

                <!-- Chat Panel -->
                <div class="career-transition-card dashboard-panel">
                    <div class="panel-header">
                        <h2 class="section-title mb-1"><i class="fas fa-robot mr-2"></i>Chat with Your AI Mentor</h2>
                        <p class="section-subtitle mb-0">
                            <?php if ($subscription['chat_limit']): ?>
                                <?= $usage_today ?>/<?= $subscription['chat_limit'] ?> chats used today
                            <?php else: ?>
                                Unlimited chats available
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="panel-body">

                        <!-- Quick Actions -->
                        <div class="mb-3 d-flex flex-wrap gap-2">
                            <button class="btn btn-sm btn-outline-primary" onclick="quickChat('Help me create a career plan to become a <?= esc($active_sessions[0]['target_role'] ?? 'Software Engineer') ?>')">
                                <i class="fas fa-route mr-1"></i> Career Plan
                            </button>
                            <button class="btn btn-sm btn-outline-success" onclick="quickChat('Do a skill gap analysis for my target role')">
                                <i class="fas fa-chart-bar mr-1"></i> Skill Gap
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="quickChat('Help me prepare for interviews')">
                                <i class="fas fa-microphone mr-1"></i> Interview Prep
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick="quickChat('Review and optimize my resume')">
                                <i class="fas fa-file-alt mr-1"></i> Resume Review
                            </button>
                            <button class="btn btn-sm btn-outline-danger" onclick="quickChat('Give me salary negotiation tips')">
                                <i class="fas fa-dollar-sign mr-1"></i> Salary Tips
                            </button>
                        </div>

                        <!-- Chat Messages -->
                        <div id="chat-messages" style="height: 400px; overflow-y: auto; background: #f8f9fa; border-radius: 8px; padding: 16px; margin-bottom: 16px;">
                            <div class="chat-bot-msg mb-3">
                                <div style="background: white; border: 1px solid #dee2e6; padding: 12px 16px; border-radius: 18px 18px 18px 4px; display: inline-block; max-width: 85%;">
                                    <strong>AI Career Mentor:</strong> Welcome! 🚀 I'm your personal AI career coach.<br><br>
                                    I can help you with career planning, skill development, interview preparation, resume optimization, and salary negotiation.<br><br>
                                    <strong>What would you like to work on today?</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Input -->
                        <div class="d-flex">
                            <input type="text" id="chat-input" class="form-control mr-2"
                                   placeholder="Ask your AI career mentor anything...">
                            <button type="button" id="chat-send" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <!-- Active Career Plans -->
                    <div class="dashboard-panel mb-4">
                        <div class="panel-header">
                            <h5 class="section-title mb-0"><i class="fas fa-tasks mr-2"></i>Active Career Plans</h5>
                        </div>
                        <div class="panel-body">
                            <?php if (!empty($active_sessions)): ?>
                                <?php foreach ($active_sessions as $s): ?>
                                    <?php
                                    $nextMilestones = array_slice($s['next_milestones'] ?? [], 0, 3);
                                    $nextMilestonesText = implode(' | ', $nextMilestones);
                                    $lastNudge = trim((string) ($s['last_nudge'] ?? ''));
                                    $hideNudge = $lastNudge !== '' && $nextMilestonesText !== '' && (
                                        stripos($lastNudge, $nextMilestones[0] ?? '') !== false ||
                                        stripos($nextMilestonesText, $lastNudge) !== false
                                    );
                                    ?>
                                    <div class="mb-3 p-3" style="background: #f8f9fa; border-radius: 8px;">
                                        <h6 class="text-primary mb-1"><?= esc($s['target_role']) ?></h6>
                                        <div class="small mb-1" style="line-height: 1.45; color: #1f2937; font-weight: 500;">
                                            <?= esc($s['main_goal_text'] ?? 'Continue progressing on your active career plan') ?>
                                        </div>
                                        <small style="color: #475569;"><?= esc($s['timeline_label'] ?? ('Timeline: ' . ($s['timeline'] ?? ''))) ?></small>
                                        <small class="float-right" style="color: #334155; font-weight: 600;"><?= (int) ($s['progress_percentage'] ?? 0) ?>%</small>
                                        <div class="progress mt-2" style="height: 6px;">
                                            <div class="progress-bar bg-success" style="width: <?= (int) ($s['progress_percentage'] ?? 0) ?>%"></div>
                                        </div>
                                        <?php if (!empty($nextMilestones)): ?>
                                            <div class="small mt-2" style="color: #475569; line-height: 1.45;">
                                                <strong>Next milestones:</strong>
                                                <?= esc($nextMilestonesText) ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($lastNudge !== '' && !$hideNudge): ?>
                                            <div class="small mt-2" style="color: #64748b; line-height: 1.45;"><?= esc($lastNudge) ?></div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted small mb-0">No active plans yet. Ask the AI mentor to create one!</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Create Career Plan -->
                    <div class="dashboard-panel mb-4">
                        <div class="panel-header">
                            <h5 class="section-title mb-0"><i class="fas fa-plus-circle mr-2"></i>New Career Plan</h5>
                        </div>
                        <div class="panel-body">
                            <form id="career-plan-form">
                                <?= csrf_field() ?>
                                <div class="mb-2">
                                    <input type="text" class="form-control form-control-sm" name="current_role" placeholder="Current role (e.g. PHP Developer)">
                                </div>
                                <div class="mb-2">
                                    <input type="text" class="form-control form-control-sm" name="target_role" placeholder="Target role (e.g. Full Stack Dev)" required>
                                </div>
                                <div class="mb-3">
                                    <select class="form-control form-control-sm" name="timeline" required>
                                        <option value="">Select timeline</option>
                                        <option value="6 months">6 months</option>
                                        <option value="12 months">12 months</option>
                                        <option value="18 months">18 months</option>
                                        <option value="24 months">24 months</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-sm btn-block">
                                    <i class="fas fa-rocket mr-1"></i> Generate Plan
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Plan Features -->
                    <div class="dashboard-panel">
                        <div class="panel-header">
                            <h5 class="section-title mb-0"><i class="fas fa-star mr-2"></i>Your Plan Features</h5>
                        </div>
                        <div class="panel-body">
                            <?php foreach (json_decode($subscription['features'], true) as $feature): ?>
                                <div class="mb-2 small">
                                    <i class="fas fa-check text-success mr-2"></i><?= esc($feature) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<?= view('Layouts/candidate_footer') ?>

<script>
var chatSessionId = '';
var chatUrl = '<?= base_url('premium-mentor/chat') ?>';
var planUrl = '<?= base_url('premium-mentor/create-career-plan') ?>';

function getCsrfData() {
    return {
        name: '<?= csrf_token() ?>',
        hash: '<?= csrf_hash() ?>'
    };
}

function formatFeatureLabel(feature) {
    return feature.replace(/_/g, ' ').replace(/\b\w/g, function(letter) {
        return letter.toUpperCase();
    });
}

function displayMessage(message, type, premiumFeatures) {
    var isUser = (type === 'user');
    var bubble = $('<div>').css({
        background: isUser ? '#007bff' : '#fff',
        color: isUser ? '#fff' : 'inherit',
        border: '1px solid ' + (isUser ? '#007bff' : '#dee2e6'),
        padding: '12px 16px',
        borderRadius: isUser ? '18px 18px 4px 18px' : '18px 18px 18px 4px',
        display: 'inline-block',
        maxWidth: '85%',
        textAlign: 'left',
        whiteSpace: 'pre-wrap'
    });
    if (!isUser) {
        bubble.html('<strong>AI Career Mentor:</strong> ' + message);
    } else {
        bubble.text(message);
    }

    if (!isUser && Array.isArray(premiumFeatures) && premiumFeatures.length) {
        var featureWrap = $('<div>').css({
            marginTop: '10px',
            display: 'flex',
            flexWrap: 'wrap',
            gap: '6px'
        });

        premiumFeatures.forEach(function(feature) {
            $('<span>')
                .text(formatFeatureLabel(feature))
                .css({
                    fontSize: '11px',
                    background: '#eef6ff',
                    color: '#0b5ed7',
                    border: '1px solid #bfd8ff',
                    borderRadius: '999px',
                    padding: '3px 8px',
                    display: 'inline-block'
                })
                .appendTo(featureWrap);
        });

        bubble.append(featureWrap);
    }

    var wrapper = $('<div>').addClass('mb-3').css('textAlign', isUser ? 'right' : 'left').append(bubble);
    $('#chat-messages').append(wrapper);
    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
}

function showTyping() {
    var bubble = $('<div>').css({
        background: '#fff', border: '1px solid #dee2e6',
        padding: '12px 16px', borderRadius: '18px 18px 18px 4px',
        display: 'inline-block'
    }).html('<em>Typing...</em>');
    $('<div>').attr('id', 'typing-indicator').addClass('mb-3').append(bubble).appendTo('#chat-messages');
    $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
}

function hideTyping() { $('#typing-indicator').remove(); }

function sendMessage() {
    var message = $('#chat-input').val().trim();
    if (!message) return;

    displayMessage(message, 'user');
    $('#chat-input').val('');
    showTyping();
    $('#chat-send').prop('disabled', true);

    var csrf = getCsrfData();
    var postData = { message: message, session_id: chatSessionId };
    postData[csrf.name] = csrf.hash;

    $.ajax({
        url: chatUrl,
        type: 'POST',
        data: postData,
        dataType: 'json',
        success: function(res) {
            hideTyping();
            $('#chat-send').prop('disabled', false);
            if (res && res.message) {
                chatSessionId = res.session_id || chatSessionId;
                displayMessage(res.message, 'bot', res.premium_features || []);
            } else if (res && res.error) {
                displayMessage(res.error, 'bot');
            } else {
                displayMessage('No response received. Please try again.', 'bot');
            }
        },
        error: function(xhr) {
            hideTyping();
            $('#chat-send').prop('disabled', false);
            displayMessage('Error ' + xhr.status + ': ' + (xhr.responseText ? xhr.responseText.substring(0, 100) : 'Unknown error'), 'bot');
        }
    });
}

function quickChat(message) {
    $('#chat-input').val(message);
    sendMessage();
}

$(document).ready(function() {
    $('#chat-send').on('click', function() {
        sendMessage();
    });

    $('#chat-input').on('keypress', function(e) {
        if (e.which === 13) { sendMessage(); }
    });

    $('#career-plan-form').on('submit', function(e) {
        e.preventDefault();
        var csrf = getCsrfData();
        var data = $(this).serialize() + '&' + csrf.name + '=' + csrf.hash;
        $.ajax({
            url: planUrl,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(res) {
                if (res && res.success) {
                    displayMessage('Career plan created! Check your Active Career Plans.', 'bot');
                    setTimeout(function() { location.reload(); }, 2000);
                }
            }
        });
    });
});
</script>
