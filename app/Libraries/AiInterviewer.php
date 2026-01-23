<?php

namespace App\Libraries;

class AiInterviewer
{
    private $apiKey;
    private $apiUrl;
    private $maxTurns = 3; // Maximum conversation turns
    private $passingScore = 70; // Minimum score to qualify

    public function __construct()
    {
        $this->apiKey = getenv('MISTRAL_API_KEY') ?: '';
        $this->apiUrl = 'https://api.mistral.ai/v1/chat/completions';


    }

    /**
     * Start a new interview session
     */
    public function startInterview(array $resumeSkills, array $githubLanguages, string $position): array
    {
        $systemPrompt = $this->buildSystemPrompt($resumeSkills, $githubLanguages, $position);

        // Initialize conversation
        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt
            ]
        ];


        // Get AI's opening message
        $response = $this->callAI($messages);

        // DEBUG: Check if AI responded
        log_message('debug', 'AI first response: ' . $response);

        if (empty($response) || strpos($response, 'technical difficulties') !== false) {
            log_message('error', 'AI failed to generate opening message');
            // Fallback message
            $response = "Hi! I'm Sarah, and I'll be conducting your technical interview today for the {$position} position. Let's start by having you introduce yourself briefly - what excites you about this role?";
        }

        // Add AI's response to conversation
        $messages[] = ['role' => 'assistant', 'content' => $response];

        return [
            'session_id' => uniqid('interview_', true),
            'turn' => 1,
            'max_turns' => $this->maxTurns,
            'ai_message' => $response,
            'conversation_history' => $messages,
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Continue the interview conversation
     */
    public function continueInterview(array $sessionData, string $candidateAnswer): array
    {
        $messages = $sessionData['conversation_history'];

        // Add candidate's response
        $messages[] = ['role' => 'user', 'content' => $candidateAnswer];

        // Get AI's response
        $aiResponse = $this->callAI($messages);

        // Add AI response to history
        $messages[] = ['role' => 'assistant', 'content' => $aiResponse];

        // Check if interview is complete
        $isComplete = $this->isInterviewComplete($aiResponse, $sessionData['turn']);

        $sessionData['turn']++;
        $sessionData['conversation_history'] = $messages;
        $sessionData['ai_message'] = $aiResponse;
        $sessionData['status'] = $isComplete ? 'completed' : 'active';
        $sessionData['updated_at'] = date('Y-m-d H:i:s');

        return $sessionData;
    }

    /**
     * Evaluate the complete interview
     */
    public function evaluateInterview(array $conversationHistory, array $resumeSkills, string $position): array
    {
        // Extract only user responses (exclude system and AI messages)
        $candidateResponses = array_filter($conversationHistory, function ($msg) {
            return $msg['role'] === 'user';
        });

        $conversationText = $this->formatConversationForEvaluation($conversationHistory);

        $evaluationPrompt = $this->buildEvaluationPrompt($conversationText, $resumeSkills, $position);

        $response = $this->callAI([
            ['role' => 'system', 'content' => 'You are an expert interview evaluator. Provide detailed, fair assessment.'],
            ['role' => 'user', 'content' => $evaluationPrompt]
        ]);
        // Assume $response is the raw AI response
        $response = trim($response);

        // Remove starting ```json and ending ```
        $response = preg_replace('/^```json\s*/', '', $response);  // remove starting ```json
        $response = preg_replace('/\s*```$/', '', $response);      // remove ending ```
        $response = trim($response);

        $evaluation = json_decode($response, true) ?? [];


        return $this->formatEvaluationResults($evaluation, count($candidateResponses));
    }

    /**
     * Build system prompt for conversational interviewer
     */
    private function buildSystemPrompt(array $resumeSkills, array $githubLanguages, string $position): string
    {
        $skillsList = implode(', ', array_column($resumeSkills, 'name'));
        $githubList = implode(', ', $githubLanguages);

        return <<<PROMPT
You are Sarah, an expert technical interviewer for a growing tech company.
You are conducting a first-round screening interview for the position: **{$position}**.

**CANDIDATE'S BACKGROUND:**
- Resume Skills: {$skillsList}
- GitHub Languages: {$githubList}

**YOUR INTERVIEW APPROACH:**

1. **Introduction (Turn 1):**
   - Introduce yourself warmly: "Hi! I'm Sarah, and I'll be conducting your technical interview today."
   - Ask them to briefly introduce themselves and what excites them about this role.

2. **Resume Deep-Dive (Turns 2-4):**
   - Pick specific skills from their resume and ask about real experience
   - If they mention a project, dig deeper: "Tell me more about that project..."
   - Ask about challenges they faced and how they solved them
   - Example: "I see you have React on your resume. Can you walk me through a complex component you built?"

3. **GitHub Analysis (Turns 3-5):**
   - Reference their GitHub languages naturally
   - Example: "I noticed you've been working with Python. What's the most interesting thing you've built with it?"
   - Ask about coding patterns, testing, or architecture decisions

4. **Technical Probing (Turns 4-7):**
   - Ask follow-up technical questions based on their answers
   - If they give a shallow answer, probe deeper: "That's interesting. Can you elaborate on how you handled [specific aspect]?"
   - If they seem uncertain, give hints: "Let me rephrase - have you worked with [related concept]?"
   - If they answer well, ask a slightly harder question
   - If they struggle, ask an easier question to build confidence

5. **Behavioral & Communication (Throughout):**
   - Assess clarity, confidence, and professionalism
   - Notice if they explain things well
   - Check if they admit when they don't know something (positive trait!)

6. **Adaptive Questioning:**
   - **If answer is WRONG/WEAK:** Don't immediately move on. Probe gently:
     * "Interesting perspective. Let me ask this differently..."
     * "That's one approach. Have you considered [alternative]?"
     * "I think there might be some confusion. Let me clarify..."
   - **If answer is STRONG:** Follow up with harder question:
     * "Great! Now, how would you handle [edge case]?"
     * "Excellent. What if we had [constraint]?"
   - **If answer shows confusion:** Help them:
     * "No worries! Let me give you a hint..."
     * "Think about it from [angle]..."

7. **Natural Conversation Flow:**
   - Use transitions: "That makes sense. Building on that..."
   - Show engagement: "Interesting!", "I see what you mean."
   - Be encouraging: "Good thinking!", "That's a solid approach."
   - Be empathetic: "I know this can be challenging..."

8. **Closing (Turn 8-10):**
   - Thank them for their time
   - End with: "That concludes our interview. Thank you for sharing your experience with me today. INTERVIEW_COMPLETE"

**IMPORTANT RULES:**
- Ask exactly ONE clear question per turn. Never combine multiple questions in a single response.
- Keep responses concise (2-3 sentences max)
- Act like a human interviewer, not a robot
- Don't list multiple questions in one turn
- Adapt based on their answers - this is a conversation, not a quiz
- If they struggle, help them; if they excel, challenge them
- Maximum {$this->maxTurns} turns total

**EVALUATION CRITERIA (Track Mentally):**
- Technical Knowledge (40%)
- Problem-Solving Ability (30%)
- Communication Skills (20%)
- Cultural Fit & Enthusiasm (10%)

Begin the interview now with your introduction.
PROMPT;
    }

    /**
     * Build evaluation prompt for final assessment
     */
    private function buildEvaluationPrompt(string $conversation, array $skills, string $position): string
    {
        $skillsList = implode(', ', array_column($skills, 'name'));

        return <<<PROMPT
You are evaluating a technical interview for the position: {$position}

**CANDIDATE'S RESUME SKILLS:** {$skillsList}

**FULL INTERVIEW TRANSCRIPT:**
{$conversation}

**EVALUATION TASK:**
Analyze the entire conversation and provide a comprehensive assessment.

**SCORING CRITERIA:**

1. **Technical Knowledge (0-100):**
   - Depth of understanding of resume skills
   - Ability to explain concepts clearly
   - Practical experience vs theoretical knowledge
   - Accuracy of technical statements

2. **Problem-Solving (0-100):**
   - Approach to challenges
   - Logical thinking
   - Creativity in solutions
   - Ability to handle follow-up questions

3. **Communication Skills (0-100):**
   - Clarity of explanations
   - Grammar and structure
   - Confidence (not arrogant, not overly hesitant)
   - Professional tone
   - Active listening (responses relevant to questions)

4. **Adaptability (0-100):**
   - Response to probing questions
   - Ability to recover from mistakes
   - Willingness to admit "I don't know"
   - Learning from hints given

5. **Enthusiasm & Fit (0-100):**
   - Genuine interest in the role
   - Passion for technology
   - Cultural alignment
   - Professional demeanor

**RED FLAGS TO CHECK:**
- Copy-pasted or generic answers
- Inability to explain own resume items
- Poor communication despite technical knowledge
- Overconfidence without substance
- Evasive answers

**GREEN FLAGS TO CHECK:**
- Real-world project examples
- Admits knowledge gaps honestly
- Asks clarifying questions
- Shows continuous learning mindset
- Explains trade-offs in technical decisions

Return JSON format:
{
    "technical_knowledge": {
        "score": 85,
        "feedback": "Strong understanding of React and Node.js. Demonstrated practical experience.",
        "strengths": ["Clear explanations", "Real examples"],
        "weaknesses": ["Limited database knowledge"]
    },
    "problem_solving": {
        "score": 78,
        "feedback": "Good analytical thinking. Could improve on edge case handling.",
        "strengths": ["Logical approach"],
        "weaknesses": ["Missed optimization opportunities"]
    },
    "communication": {
        "score": 90,
        "feedback": "Excellent communicator. Clear, concise, professional.",
        "strengths": ["Well-structured answers", "Professional tone"],
        "weaknesses": ["Minor grammar issues"]
    },
    "adaptability": {
        "score": 82,
        "feedback": "Handled probing questions well. Open to feedback.",
        "strengths": ["Recovered from initial confusion", "Asked for clarification"],
        "weaknesses": ["Hesitated on unfamiliar topics"]
    },
    "enthusiasm": {
        "score": 88,
        "feedback": "Genuine passion for the role. Engaged throughout.",
        "strengths": ["Clear interest in technology", "Prepared for interview"],
        "weaknesses": ["Could research company more"]
    },
    "overall_assessment": {
        "total_score": 84.6,
        "recommendation": "STRONG HIRE / HIRE / MAYBE / NO HIRE",
        "key_highlights": [
            "Solid technical foundation in required skills",
            "Excellent communication skills",
            "Real-world project experience"
        ],
        "concerns": [
            "Limited experience with advanced topics",
            "Needs growth in system design"
        ],
        "decision_reasoning": "Candidate demonstrates strong fundamentals and excellent communication. Recommended for next round with senior engineer.",
        "next_steps": "Technical deep-dive with live coding",
        "growth_areas": [
            "Study distributed systems",
            "Practice whiteboard coding",
            "Review SQL optimization"
        ]
    },
    "notable_moments": [
        {
            "turn": 3,
            "what_happened": "Candidate explained complex React hook pattern clearly",
            "impact": "Positive - shows deep understanding"
        },
        {
            "turn": 5,
            "what_happened": "Struggled with scaling question but recovered with hint",
            "impact": "Neutral - shows learning ability"
        }
    ]
}
PROMPT;
    }

    /**
     * Format conversation for evaluation
     */
    private function formatConversationForEvaluation(array $messages): string
    {
        $formatted = "";
        $turn = 0;

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system')
                continue;

            $turn++;
            $speaker = $msg['role'] === 'assistant' ? 'INTERVIEWER' : 'CANDIDATE';
            $formatted .= "\n[Turn {$turn} - {$speaker}]\n{$msg['content']}\n";
        }

        return $formatted;
    }

    /**
     * Format evaluation results for storage/display
     */
    private function formatEvaluationResults(array $evaluation, int $totalResponses): array
    {
        $overallScore = $evaluation['overall_assessment']['total_score'] ?? 0;
        $recommendation = $evaluation['overall_assessment']['recommendation'] ?? 'NO HIRE';

        // Map recommendation to decision
        $decision = 'rejected';



        if (in_array($recommendation, ['STRONG HIRE', 'HIRE'])) {
            $decision = 'qualified';
        } elseif ($recommendation === 'MAYBE') {
            $decision = $overallScore >= $this->passingScore ? 'qualified' : 'rejected';
        }

        return [
            'technical_score' => $evaluation['technical_knowledge']['score'] ?? 0,
            'communication_score' => $evaluation['communication']['score'] ?? 0,
            'problem_solving_score' => $evaluation['problem_solving']['score'] ?? 0,
            'adaptability_score' => $evaluation['adaptability']['score'] ?? 0,
            'enthusiasm_score' => $evaluation['enthusiasm']['score'] ?? 0,
            'overall_rating' => round($overallScore, 2),
            'ai_decision' => $decision,
            'recommendation' => $recommendation,
            'total_responses' => $totalResponses,
            'technical_feedback' => $evaluation['technical_knowledge']['feedback'] ?? '',
            'communication_feedback' => $evaluation['communication']['feedback'] ?? '',
            'key_highlights' => $evaluation['overall_assessment']['key_highlights'] ?? [],
            'concerns' => $evaluation['overall_assessment']['concerns'] ?? [],
            'recommendations' => $evaluation['overall_assessment']['growth_areas'] ?? [],
            'decision_reasoning' => $evaluation['overall_assessment']['decision_reasoning'] ?? '',
            'next_steps' => $evaluation['overall_assessment']['next_steps'] ?? '',
            'notable_moments' => $evaluation['notable_moments'] ?? [],
            'detailed_breakdown' => [
                'technical' => $evaluation['technical_knowledge'] ?? [],
                'problem_solving' => $evaluation['problem_solving'] ?? [],
                'communication' => $evaluation['communication'] ?? [],
                'adaptability' => $evaluation['adaptability'] ?? [],
                'enthusiasm' => $evaluation['enthusiasm'] ?? []
            ]
        ];
    }

    /**
     * Check if interview is complete
     */
    private function isInterviewComplete(string $aiResponse, int $currentTurn): bool
    {
        // Check for completion phrase
        if (stripos($aiResponse, 'INTERVIEW_COMPLETE') !== false) {
            return true;
        }

        // Force complete if max turns reached
        if ($currentTurn >= $this->maxTurns) {
            return true;
        }

        return false;
    }

    /**
     * Call OpenAI API
     */
    private function callAI(array $messages): string
    {
        $model = "mistral-large-latest";

        // Request body
        $data = [
            "model" => $model,
            "messages" => $messages,
            "temperature" => 0.7
        ];

        $ch = curl_init('https://api.mistral.ai/v1/chat/completions');

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . trim($this->apiKey),
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_TIMEOUT => 60,
        ]);

        $response = curl_exec(handle: $ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        log_message('error', 'Mistral HTTP Code: ' . $httpCode);
        log_message('error', 'Mistral RAW Response: ' . $response);

        if ($httpCode !== 200) {
            return 'AI service error';
        }

        $data = json_decode($response, true);
        return $data['choices'][0]['message']['content'] ?? 'No response';
    }



    /**
     * Get interview session status
     */
    public function getSessionStatus(array $sessionData): array
    {
        return [
            'session_id' => $sessionData['session_id'],
            'status' => $sessionData['status'],
            'turn' => $sessionData['turn'],
            'max_turns' => $sessionData['max_turns'],
            'progress_percentage' => round(($sessionData['turn'] / $sessionData['max_turns']) * 100, 2),
            'is_active' => $sessionData['status'] === 'active'
        ];
    }
}