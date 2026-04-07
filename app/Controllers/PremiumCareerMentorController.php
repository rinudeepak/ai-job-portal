<?php

namespace App\Controllers;

use App\Models\CareerGoalModel;
use App\Models\CareerConversationModel;
use App\Models\SubscriptionModel;
use App\Models\ChatbotUsageModel;
use App\Models\PremiumCareerSessionModel;
use App\Libraries\AILibrary;

class PremiumCareerMentorController extends BaseController
{
    protected $careerGoalModel;
    protected $conversationModel;
    protected $subscriptionModel;
    protected $usageModel;
    protected $sessionModel;
    protected $aiLibrary;

    public function __construct()
    {
        $this->careerGoalModel = new CareerGoalModel();
        $this->conversationModel = new CareerConversationModel();
        $this->subscriptionModel = new SubscriptionModel();
        $this->usageModel = new ChatbotUsageModel();
        $this->sessionModel = new PremiumCareerSessionModel();
        $this->aiLibrary = new AILibrary();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $subscription = $this->subscriptionModel->getUserActiveSubscription($userId);
        
        if (!$subscription) {
            return redirect()->to('/premium-mentor/plans');
        }

        $data = [
            'title' => 'AI Career Mentor - Premium',
            'subscription' => $subscription,
            'usage_today' => $this->usageModel->getTodayUsage($userId),
            'active_sessions' => $this->sessionModel->getUserActiveSessions($userId)
        ];

        return view('premium_mentor/dashboard', $data);
    }

    public function plans()
    {
        $plans = $this->subscriptionModel->getActivePlans();
        $userId = session()->get('user_id');
        $currentSubscription = $this->subscriptionModel->getUserActiveSubscription($userId);

        $data = [
            'title' => 'Premium Career Mentor Plans',
            'plans' => $plans,
            'current_subscription' => $currentSubscription
        ];

        return view('premium_mentor/plans', $data);
    }

    public function chat()
    {
        $userId = session()->get('user_id');
        $message = $this->request->getPost('message');
        $sessionId = $this->request->getPost('session_id') ?: uniqid();

        // Check subscription and usage limits
        $canUse = $this->checkUsageLimit($userId);
        if (!$canUse['allowed']) {
            return $this->response->setJSON([
                'error' => $canUse['message'],
                'upgrade_required' => true
            ]);
        }

        // Process premium career mentoring
        $response = $this->processPremiumCareerChat($message, $userId, $sessionId);
        
        // Track usage
        $this->usageModel->trackUsage($userId, $sessionId, $response['feature_used']);

        return $this->response->setJSON($response);
    }

    private function processPremiumCareerChat($message, $userId, $sessionId)
    {
        $subscription = $this->subscriptionModel->getUserActiveSubscription($userId);
        $userProfile = $this->getUserCareerProfile($userId);
        
        // Enhanced AI prompt for premium users
        $prompt = $this->buildPremiumPrompt($message, $userProfile, $subscription);
        
        $aiResponse = $this->aiLibrary->generateResponse($prompt);
        
        // Determine response type and features
        $responseType = $this->analyzeMessageIntent($message);
        
        $response = [
            'message' => $aiResponse,
            'session_id' => $sessionId,
            'feature_used' => $responseType,
            'premium_features' => $this->getPremiumFeatures($responseType, $subscription)
        ];

        // Save conversation
        $this->conversationModel->save([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'message' => $message,
            'response' => $aiResponse,
            'message_type' => 'premium_chat'
        ]);

        return $response;
    }

    private function buildPremiumPrompt($message, $userProfile, $subscription)
    {
        $planFeatures = json_decode($subscription['features'], true);
        
        $prompt = "You are an expert AI Career Mentor with access to premium features: " . 
                 implode(', ', $planFeatures) . "\n\n";
        
        $prompt .= "User Profile:\n";
        $prompt .= "- Current Role: " . ($userProfile['current_role'] ?? 'Not specified') . "\n";
        $prompt .= "- Target Role: " . ($userProfile['target_role'] ?? 'Not specified') . "\n";
        $prompt .= "- Experience: " . ($userProfile['experience'] ?? 'Not specified') . "\n";
        $prompt .= "- Skills: " . implode(', ', $userProfile['skills'] ?? []) . "\n\n";
        
        $prompt .= "User Message: {$message}\n\n";
        
        $prompt .= "Provide detailed, actionable career guidance including:\n";
        $prompt .= "1. Specific steps to achieve their career goals\n";
        $prompt .= "2. Skill development recommendations\n";
        $prompt .= "3. Timeline and milestones\n";
        $prompt .= "4. Industry insights and market trends\n";
        $prompt .= "5. Networking and personal branding tips\n";
        $prompt .= "Be encouraging, specific, and provide premium-level insights.";

        return $prompt;
    }

    private function analyzeMessageIntent($message)
    {
        $message = strtolower($message);
        
        if (strpos($message, 'goal') !== false || strpos($message, 'target') !== false) {
            return 'goal_setting';
        } elseif (strpos($message, 'skill') !== false || strpos($message, 'learn') !== false) {
            return 'skill_development';
        } elseif (strpos($message, 'interview') !== false) {
            return 'interview_prep';
        } elseif (strpos($message, 'resume') !== false || strpos($message, 'cv') !== false) {
            return 'resume_optimization';
        } elseif (strpos($message, 'salary') !== false || strpos($message, 'negotiate') !== false) {
            return 'salary_negotiation';
        } else {
            return 'general_guidance';
        }
    }

    private function getPremiumFeatures($responseType, $subscription)
    {
        $features = [];
        
        switch ($responseType) {
            case 'goal_setting':
                $features = [
                    'smart_goals_generator' => true,
                    'milestone_tracker' => true,
                    'progress_analytics' => true
                ];
                break;
            case 'skill_development':
                $features = [
                    'skill_gap_analysis' => true,
                    'learning_roadmap' => true,
                    'course_recommendations' => true
                ];
                break;
            case 'interview_prep':
                $features = [
                    'mock_interview_questions' => true,
                    'answer_templates' => true,
                    'company_insights' => true
                ];
                break;
        }

        return $features;
    }

    private function checkUsageLimit($userId)
    {
        $subscription = $this->subscriptionModel->getUserActiveSubscription($userId);
        
        if (!$subscription) {
            return [
                'allowed' => false,
                'message' => 'Premium subscription required for AI Career Mentor'
            ];
        }

        if ($subscription['chat_limit']) {
            $todayUsage = $this->usageModel->getTodayUsage($userId);
            if ($todayUsage >= $subscription['chat_limit']) {
                return [
                    'allowed' => false,
                    'message' => 'Daily chat limit reached. Upgrade for unlimited access.'
                ];
            }
        }

        return ['allowed' => true];
    }

    private function getUserCareerProfile($userId)
    {
        // Get user's career information from existing profile
        $db = \Config\Database::connect();
        
        $profile = $db->table('users')
                     ->select('users.*, candidate_profiles.*')
                     ->join('candidate_profiles', 'candidate_profiles.user_id = users.id', 'left')
                     ->where('users.id', $userId)
                     ->get()
                     ->getRowArray();

        $skills = $db->table('candidate_skills')
                     ->where('user_id', $userId)
                     ->get()
                     ->getResultArray();

        return [
            'current_role' => $profile['current_position'] ?? null,
            'target_role' => $profile['preferred_job_title'] ?? null,
            'experience' => $profile['total_experience'] ?? null,
            'skills' => array_column($skills, 'skill_name')
        ];
    }

    public function createCareerPlan()
    {
        $userId = session()->get('user_id');
        $targetRole = $this->request->getPost('target_role');
        $timeline = $this->request->getPost('timeline');
        $currentRole = $this->request->getPost('current_role');

        // Generate comprehensive career plan using AI
        $prompt = "Create a detailed career transition plan:
                   Current Role: {$currentRole}
                   Target Role: {$targetRole}
                   Timeline: {$timeline}
                   
                   Provide:
                   1. Skill gap analysis
                   2. Learning roadmap with specific courses/certifications
                   3. Monthly milestones
                   4. Networking strategies
                   5. Portfolio/project recommendations
                   6. Interview preparation timeline
                   
                   Format as structured JSON with phases, tasks, and deadlines.";

        $aiAnalysis = $this->aiLibrary->generateResponse($prompt);
        
        // Save premium career session
        $sessionData = [
            'user_id' => $userId,
            'session_type' => 'career_strategy',
            'current_role' => $currentRole,
            'target_role' => $targetRole,
            'timeline' => $timeline,
            'ai_analysis' => $aiAnalysis,
            'status' => 'active'
        ];

        $sessionId = $this->sessionModel->insert($sessionData);

        return $this->response->setJSON([
            'success' => true,
            'session_id' => $sessionId,
            'message' => 'Career plan created successfully!'
        ]);
    }

    public function subscribe()
    {
        $planId = $this->request->getPost('plan_id');
        $userId = session()->get('user_id');

        $plan = $this->subscriptionModel->find($planId);
        
        if (!$plan) {
            return redirect()->back()->with('error', 'Invalid plan selected');
        }

        // In a real implementation, integrate with payment gateway
        // For now, we'll simulate successful payment
        
        $subscriptionData = [
            'user_id' => $userId,
            'plan_id' => $planId,
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d', strtotime('+' . $plan['duration_days'] . ' days')),
            'amount_paid' => $plan['price'],
            'payment_id' => 'demo_' . uniqid(),
            'status' => 'active'
        ];

        if ($this->subscriptionModel->saveSubscription($subscriptionData)) {
            return redirect()->to('/premium-mentor')->with('success', 'Subscription activated successfully!');
        }

        return redirect()->back()->with('error', 'Failed to activate subscription');
    }
}