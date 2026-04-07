<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <!-- Subscription Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h4><i class="fas fa-crown me-2"></i><?= esc($subscription['plan_name']) ?></h4>
                            <p class="mb-0">Active until: <?= date('M d, Y', strtotime($subscription['end_date'])) ?></p>
                        </div>
                        <div class="col-md-4 text-end">
                            <?php if ($subscription['chat_limit']): ?>
                                <div class="usage-stats">
                                    <h5><?= $usage_today ?>/<?= $subscription['chat_limit'] ?></h5>
                                    <small>Chats today</small>
                                </div>
                            <?php else: ?>
                                <div class="usage-stats">
                                    <h5><i class="fas fa-infinity"></i></h5>
                                    <small>Unlimited chats</small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- AI Chat Interface -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-robot me-2"></i>Premium AI Career Mentor
                    </h5>
                    <small>Your personal AI career coach - available 24/7</small>
                </div>
                
                <div class="card-body p-0">
                    <!-- Chat Messages -->
                    <div id="chat-messages" class="chat-container" style="height: 500px; overflow-y: auto; padding: 20px;">
                        <div class="message bot-message">
                            <div class="message-content">
                                <strong>AI Career Mentor:</strong> Welcome to your premium career coaching session! 🚀
                                <br><br>
                                I'm here to help you achieve your career goals with personalized guidance, skill development plans, and strategic advice.
                                <br><br>
                                <strong>What would you like to work on today?</strong>
                                <ul class="mt-2">
                                    <li>Career transition planning</li>
                                    <li>Skill gap analysis</li>
                                    <li>Interview preparation</li>
                                    <li>Resume optimization</li>
                                    <li>Salary negotiation strategies</li>
                                </ul>
                            </div>
                            <small class="text-muted">Just now</small>
                        </div>
                    </div>
                    
                    <!-- Chat Input -->
                    <div class="card-footer">
                        <form id="premium-chat-form" class="d-flex">
                            <input type="hidden" id="session-id" value="">
                            <input type="text" id="chat-input" class="form-control me-2" 
                                   placeholder="Ask your AI career mentor anything..." required>
                            <button type="submit" class="btn btn-primary" id="send-btn">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h6>
                </div>
                <div class="card-body">
                    <button class="btn btn-outline-primary btn-sm mb-2 w-100" onclick="startCareerPlan()">
                        <i class="fas fa-route me-2"></i>Create Career Plan
                    </button>
                    <button class="btn btn-outline-success btn-sm mb-2 w-100" onclick="skillGapAnalysis()">
                        <i class="fas fa-chart-bar me-2"></i>Skill Gap Analysis
                    </button>
                    <button class="btn btn-outline-info btn-sm mb-2 w-100" onclick="interviewPrep()">
                        <i class="fas fa-microphone me-2"></i>Interview Prep
                    </button>
                    <button class="btn btn-outline-warning btn-sm w-100" onclick="resumeOptimization()">
                        <i class="fas fa-file-alt me-2"></i>Resume Review
                    </button>
                </div>
            </div>
            
            <!-- Active Sessions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-tasks me-2"></i>Active Career Plans</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($active_sessions)): ?>
                        <?php foreach ($active_sessions as $session): ?>
                            <div class="session-item mb-3 p-3 border rounded">
                                <h6 class="text-primary"><?= esc($session['target_role']) ?></h6>
                                <p class="small mb-2">Timeline: <?= esc($session['timeline']) ?></p>
                                <div class="progress mb-2" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 30%"></div>
                                </div>
                                <small class="text-muted">30% complete</small>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted small">No active career plans. Start by creating your first career roadmap!</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Premium Features -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-star me-2"></i>Premium Features</h6>
                </div>
                <div class="card-body">
                    <div class="feature-item mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        <small>Unlimited AI conversations</small>
                    </div>
                    <div class="feature-item mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        <small>Personalized career roadmaps</small>
                    </div>
                    <div class="feature-item mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        <small>Weekly progress reviews</small>
                    </div>
                    <div class="feature-item mb-2">
                        <i class="fas fa-check text-success me-2"></i>
                        <small>Industry insights & trends</small>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check text-success me-2"></i>
                        <small>Priority support</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Career Plan Modal -->
<div class="modal fade" id="careerPlanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Your Career Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="career-plan-form">
                    <div class="mb-3">
                        <label class="form-label">Current Role</label>
                        <input type="text" class="form-control" name="current_role" placeholder="e.g., Junior Developer">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Role</label>
                        <input type="text" class="form-control" name="target_role" placeholder="e.g., Senior Full Stack Developer" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Timeline</label>
                        <select class="form-control" name="timeline" required>
                            <option value="">Select timeline</option>
                            <option value="6 months">6 months</option>
                            <option value="12 months">12 months</option>
                            <option value="18 months">18 months</option>
                            <option value="24 months">24 months</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="createCareerPlan()">Create Plan</button>
            </div>
        </div>
    </div>
</div>

<style>
.chat-container {
    background: #f8f9fa;
}

.message {
    margin-bottom: 20px;
    max-width: 80%;
}

.user-message {
    margin-left: auto;
    text-align: right;
}

.user-message .message-content {
    background: #007bff;
    color: white;
    padding: 12px 16px;
    border-radius: 18px 18px 4px 18px;
    display: inline-block;
}

.bot-message .message-content {
    background: white;
    border: 1px solid #dee2e6;
    padding: 12px 16px;
    border-radius: 18px 18px 18px 4px;
    display: inline-block;
}

.session-item {
    background: #f8f9fa;
}

.usage-stats h5 {
    margin-bottom: 0;
    font-size: 1.5rem;
}

.bg-gradient-primary {
    background: linear-gradient(45deg, #007bff, #0056b3);
}
</style>

<script>
class PremiumCareerMentor {
    constructor() {
        this.sessionId = '';
        this.initializeChat();
    }
    
    initializeChat() {
        $('#premium-chat-form').on('submit', (e) => {
            e.preventDefault();
            this.sendMessage();
        });
    }
    
    sendMessage() {
        const message = $('#chat-input').val().trim();
        if (!message) return;
        
        this.displayMessage(message, 'user');
        $('#chat-input').val('');
        
        $.post('/premium-mentor/chat', {
            message: message,
            session_id: this.sessionId
        })
        .done((response) => {
            if (response.error) {
                this.displayMessage(response.error, 'bot');
                if (response.upgrade_required) {
                    this.showUpgradePrompt();
                }
            } else {
                this.handleResponse(response);
            }
        });
    }
    
    handleResponse(response) {
        this.sessionId = response.session_id;
        this.displayMessage(response.message, 'bot');
        
        if (response.premium_features) {
            this.showPremiumFeatures(response.premium_features);
        }
    }
    
    displayMessage(message, type) {
        const messageClass = type === 'user' ? 'user-message' : 'bot-message';
        const sender = type === 'user' ? 'You' : 'AI Career Mentor';
        
        const messageHtml = `
            <div class="message ${messageClass}">
                <div class="message-content">
                    ${type === 'bot' ? '<strong>' + sender + ':</strong> ' : ''}${message}
                </div>
                <small class="text-muted">Just now</small>
            </div>
        `;
        
        $('#chat-messages').append(messageHtml);
        $('#chat-messages').scrollTop($('#chat-messages')[0].scrollHeight);
    }
    
    showUpgradePrompt() {
        // Show upgrade modal or redirect to plans
        window.location.href = '/premium-mentor/plans';
    }
}

// Quick action functions
function startCareerPlan() {
    $('#careerPlanModal').modal('show');
}

function skillGapAnalysis() {
    $('#chat-input').val('I want to do a skill gap analysis for my target role');
    $('#premium-chat-form').submit();
}

function interviewPrep() {
    $('#chat-input').val('Help me prepare for interviews for my target role');
    $('#premium-chat-form').submit();
}

function resumeOptimization() {
    $('#chat-input').val('Review and optimize my resume for better job applications');
    $('#premium-chat-form').submit();
}

function createCareerPlan() {
    const formData = new FormData($('#career-plan-form')[0]);
    
    $.post('/premium-mentor/create-career-plan', formData)
    .done((response) => {
        if (response.success) {
            $('#careerPlanModal').modal('hide');
            location.reload();
        }
    });
}

$(document).ready(function() {
    new PremiumCareerMentor();
});
</script>

<?= $this->endSection() ?>