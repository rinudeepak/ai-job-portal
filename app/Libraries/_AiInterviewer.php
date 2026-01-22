<?php

namespace App\Libraries;

class AiInterviewer
{
    private $apiKey;
    private $apiUrl;

    // Configurable thresholds
    private $qualifiedThreshold = 70;
    private $questionsPerSkill = 2;

    public function __construct()
    {
        // Store your OpenAI API key in .env file
        $this->apiKey = getenv('OPENAI_API_KEY') ?: '';
        $this->apiUrl = 'https://api.openai.com/v1/chat/completions';
    }

    /**
     * Generate interview questions based on skills and GitHub languages
     */
    public function generateQuestions(array $skills, array $githubLanguages = []): array
    {
        $allTopics = array_unique(array_merge(
            array_column($skills, 'name'),
            $githubLanguages
        ));

        // Limit to top 5 topics for focused interview
        $focusTopics = array_slice($allTopics, 0, 5);

        $prompt = $this->buildQuestionPrompt($focusTopics);

        $response = $this->callAI([
            ['role' => 'system', 'content' => 'You are a technical interviewer. Generate precise, skill-appropriate questions.'],
            ['role' => 'user', 'content' => $prompt]
        ], 'json_object');

        return $this->parseQuestions($response, $focusTopics);
    }

    /**
     * Evaluate all answers and generate scores
     */
    public function evaluateInterview(array $questions, array $answers): array
    {
        $technicalEvaluations = [];
        $communicationEvaluations = [];

        foreach ($questions as $index => $question) {
            $answer = $answers[$index] ?? '';
            
            if (empty(trim($answer))) {
                $technicalEvaluations[] = ['score' => 0, 'feedback' => 'No answer provided'];
                $communicationEvaluations[] = $this->getEmptyCommunicationScore();
                continue;
            }

            // Technical evaluation
            $technicalEvaluations[] = $this->evaluateTechnicalAnswer($question, $answer);

            // Communication evaluation
            $communicationEvaluations[] = $this->evaluateCommunication($answer);
        }

        return $this->calculateFinalScores($technicalEvaluations, $communicationEvaluations);
    }

    /**
     * Build prompt for question generation
     */
    private function buildQuestionPrompt(array $topics): string
    {
        $topicList = implode(', ', $topics);

        return <<<PROMPT
Generate technical interview questions for these skills/languages: {$topicList}

Requirements:
- Generate {$this->questionsPerSkill} questions per topic
- Mix of MCQ (with 4 options) and short-answer questions
- Difficulty: 60% intermediate, 40% advanced
- Each question should test practical knowledge

Return JSON format:
{
    "questions": [
        {
            "id": 1,
            "topic": "skill name",
            "type": "mcq",
            "difficulty": "intermediate",
            "question": "question text",
            "options": ["A", "B", "C", "D"],
            "correct_answer": "A",
            "points": 10
        },
        {
            "id": 2,
            "topic": "skill name",
            "type": "short_answer",
            "difficulty": "advanced",
            "question": "question text",
            "expected_keywords": ["keyword1", "keyword2"],
            "points": 15
        }
    ]
}
PROMPT;
    }

    /**
     * Evaluate a technical answer
     */
    private function evaluateTechnicalAnswer(array $question, string $answer): array
    {
        if ($question['type'] === 'mcq') {
            return $this->evaluateMCQ($question, $answer);
        }

        return $this->evaluateShortAnswer($question, $answer);
    }

    /**
     * Evaluate MCQ answer
     */
    private function evaluateMCQ(array $question, string $answer): array
    {
        $normalizedAnswer = strtoupper(trim($answer));
        $correctAnswer = strtoupper(trim($question['correct_answer'] ?? ''));

        $isCorrect = $normalizedAnswer === $correctAnswer;

        return [
            'score' => $isCorrect ? $question['points'] : 0,
            'max_score' => $question['points'],
            'is_correct' => $isCorrect,
            'feedback' => $isCorrect ? 'Correct answer' : "Incorrect. Correct answer: {$correctAnswer}"
        ];
    }

    /**
     * Evaluate short answer using AI
     */
    private function evaluateShortAnswer(array $question, string $answer): array
    {
        $expectedKeywords = implode(', ', $question['expected_keywords'] ?? []);

        $prompt = <<<PROMPT
Evaluate this technical answer:

Question: {$question['question']}
Expected concepts/keywords: {$expectedKeywords}
Candidate's Answer: {$answer}

Evaluate based on:
1. Technical accuracy (0-100)
2. Completeness (0-100)
3. Relevance to question (0-100)

Return JSON:
{
    "technical_accuracy": 85,
    "completeness": 70,
    "relevance": 90,
    "overall_score": 82,
    "feedback": "brief constructive feedback",
    "missing_concepts": ["concept1", "concept2"]
}
PROMPT;

        $response = $this->callAI([
            ['role' => 'system', 'content' => 'You are a technical evaluator. Be fair but rigorous.'],
            ['role' => 'user', 'content' => $prompt]
        ], 'json_object');

        $evaluation = json_decode($response, true) ?? [];

        $scorePercent = $evaluation['overall_score'] ?? 50;
        $earnedPoints = ($scorePercent / 100) * $question['points'];

        return [
            'score' => round($earnedPoints, 2),
            'max_score' => $question['points'],
            'percentage' => $scorePercent,
            'feedback' => $evaluation['feedback'] ?? 'Unable to evaluate',
            'missing_concepts' => $evaluation['missing_concepts'] ?? [],
            'breakdown' => [
                'accuracy' => $evaluation['technical_accuracy'] ?? 0,
                'completeness' => $evaluation['completeness'] ?? 0,
                'relevance' => $evaluation['relevance'] ?? 0
            ]
        ];
    }

    /**
     * Evaluate communication quality
     */
    private function evaluateCommunication(string $answer): array
    {
        $prompt = <<<PROMPT
Analyze this answer for communication quality:

Answer: {$answer}

Evaluate:
1. Grammar quality (0-100): spelling, punctuation, sentence structure
2. Clarity (0-100): easy to understand, well-organized
3. Structure (0-100): logical flow, proper formatting
4. Confidence indicators (0-100): assertive language, no excessive hedging
5. Professionalism (0-100): appropriate tone, technical vocabulary usage

Return JSON:
{
    "grammar": 85,
    "clarity": 80,
    "structure": 75,
    "confidence": 70,
    "professionalism": 85,
    "overall_communication": 79,
    "issues": ["issue1", "issue2"],
    "strengths": ["strength1"]
}
PROMPT;

        $response = $this->callAI([
            ['role' => 'system', 'content' => 'You are a communication skills assessor.'],
            ['role' => 'user', 'content' => $prompt]
        ], 'json_object');

        return json_decode($response, true) ?? $this->getEmptyCommunicationScore();
    }

    /**
     * Calculate final scores and decision
     */
    private function calculateFinalScores(array $technicalEvals, array $commEvals): array
    {
        // Technical score calculation
        $totalPoints = 0;
        $maxPoints = 0;
        $technicalDetails = [];

        foreach ($technicalEvals as $eval) {
            $totalPoints += $eval['score'];
            $maxPoints += $eval['max_score'] ?? 10;
            $technicalDetails[] = $eval;
        }

        $technicalScore = $maxPoints > 0 ? ($totalPoints / $maxPoints) * 100 : 0;

        // Communication score calculation
        $commScores = array_filter(array_column($commEvals, 'overall_communication'));
        $communicationScore = !empty($commScores) ? array_sum($commScores) / count($commScores) : 0;

        // Overall rating (60% technical, 40% communication)
        $overallRating = ($technicalScore * 0.6) + ($communicationScore * 0.4);

        // AI Decision
        $decision = $this->makeDecision($technicalScore, $communicationScore, $overallRating);

        return [
            'technical_score' => round($technicalScore, 2),
            'communication_score' => round($communicationScore, 2),
            'overall_rating' => round($overallRating, 2),
            'ai_decision' => $decision['decision'],
            'ai_feedback' => [
                'technical_details' => $technicalDetails,
                'communication_details' => $commEvals,
                'decision_reasoning' => $decision['reasoning'],
                'recommendations' => $decision['recommendations']
            ]
        ];
    }

    /**
     * Make qualification decision
     */
    private function makeDecision(float $technical, float $communication, float $overall): array
    {
        $decision = 'rejected';
        $reasoning = '';
        $recommendations = [];

        if ($overall >= $this->qualifiedThreshold && $technical >= 60) {
            $decision = 'qualified';
            $reasoning = "Strong overall performance with {$overall}% rating.";
            
            if ($communication < 70) {
                $recommendations[] = 'Consider communication skills training';
            }
        } else {
            if ($technical < 60) {
                $reasoning = "Technical skills below threshold ({$technical}%).";
                $recommendations[] = 'Strengthen core technical concepts';
            } elseif ($communication < 50) {
                $reasoning = "Communication skills need improvement ({$communication}%).";
                $recommendations[] = 'Work on written communication clarity';
            } else {
                $reasoning = "Overall score ({$overall}%) below qualification threshold.";
            }
        }

        return [
            'decision' => $decision,
            'reasoning' => $reasoning,
            'recommendations' => $recommendations
        ];
    }

    /**
     * Call OpenAI API
     */
    private function callAI(array $messages, string $responseFormat = null): string
    {
        if (empty($this->apiKey)) {
            log_message('error', 'OpenAI API key not configured');
            return json_encode(['error' => 'AI service not configured']);
        }

        $payload = [
            'model' => 'gpt-4o-mini',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 2000
        ];

        if ($responseFormat === 'json_object') {
            $payload['response_format'] = ['type' => 'json_object'];
        }

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 60
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            log_message('error', "OpenAI API error: {$httpCode} - {$response}");
            return json_encode(['error' => 'AI service error']);
        }

        $data = json_decode($response, true);
        return $data['choices'][0]['message']['content'] ?? '';
    }

    /**
     * Parse AI-generated questions
     */
    private function parseQuestions(string $response, array $topics): array
    {
        $data = json_decode($response, true);

        if (!isset($data['questions']) || !is_array($data['questions'])) {
            // Fallback: generate basic questions
            return $this->generateFallbackQuestions($topics);
        }

        return $data['questions'];
    }

    /**
     * Fallback questions if AI fails
     */
    private function generateFallbackQuestions(array $topics): array
    {
        $questions = [];
        $id = 1;

        foreach ($topics as $topic) {
            $questions[] = [
                'id' => $id++,
                'topic' => $topic,
                'type' => 'short_answer',
                'difficulty' => 'intermediate',
                'question' => "Explain your experience with {$topic} and provide a practical example.",
                'expected_keywords' => [$topic],
                'points' => 10
            ];
        }

        return $questions;
    }

    private function getEmptyCommunicationScore(): array
    {
        return [
            'grammar' => 0,
            'clarity' => 0,
            'structure' => 0,
            'confidence' => 0,
            'professionalism' => 0,
            'overall_communication' => 0,
            'issues' => ['No answer provided'],
            'strengths' => []
        ];
    }
}
