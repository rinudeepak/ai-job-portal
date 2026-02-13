<?php

namespace App\Libraries;

class MultiLanguageAIInterview
{
    protected $langHelper;

    public function __construct()
    {
        $this->langHelper = new LanguageHelper();
    }

    /**
     * Generate interview questions in user's language
     */
    public function generateInterviewQuestions($jobRole, $experienceLevel = 'intermediate')
    {
        $currentLang = $this->langHelper->getCurrentLanguage();
        $langInfo = $this->langHelper->getLanguageInfo($currentLang);

        $apiKey = getenv('OPENAI_API_KEY');
        if (!$apiKey) {
            return $this->getFallbackQuestions($jobRole);
        }

        $prompt = $currentLang === 'en' 
            ? "Generate 5 interview questions for a {$jobRole} position at {$experienceLevel} level."
            : "Generate 5 interview questions for a {$jobRole} position at {$experienceLevel} level. Provide questions in {$langInfo['name']} language. Make them culturally appropriate for Indian job market.";

        $data = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system', 
                    'content' => $currentLang === 'en' 
                        ? 'You are an expert interviewer.' 
                        : "You are an expert interviewer who asks questions in {$langInfo['name']}. Keep technical terms in English but explanations in {$langInfo['name']}."
                ],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 1000
        ];

        $response = $this->callOpenAI($data);
        
        if ($response) {
            // Parse questions
            $questions = $this->parseQuestions($response);
            return $questions;
        }

        return $this->getFallbackQuestions($jobRole);
    }

    /**
     * Evaluate answer in user's language
     */
    public function evaluateAnswer($question, $answer, $jobRole)
    {
        $currentLang = $this->langHelper->getCurrentLanguage();
        $langInfo = $this->langHelper->getLanguageInfo($currentLang);

        $apiKey = getenv('OPENAI_API_KEY');
        if (!$apiKey) {
            return [
                'score' => 7,
                'feedback' => 'Good answer. Keep practicing!',
                'suggestions' => ['Be more specific', 'Add examples']
            ];
        }

        $prompt = $currentLang === 'en'
            ? "Question: {$question}\nCandidate's Answer: {$answer}\n\nEvaluate this answer for a {$jobRole} interview. Provide score (1-10), feedback, and suggestions."
            : "Question: {$question}\nCandidate's Answer: {$answer}\n\nEvaluate this answer for a {$jobRole} interview. Provide response in {$langInfo['name']} including: score (1-10), feedback, and improvement suggestions.";

        $data = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $currentLang === 'en'
                        ? 'You are an expert interviewer providing constructive feedback.'
                        : "You are an expert interviewer providing feedback in {$langInfo['name']}. Be encouraging and constructive."
                ],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.5,
            'max_tokens' => 500
        ];

        $response = $this->callOpenAI($data);

        if ($response) {
            return $this->parseEvaluation($response);
        }

        return [
            'score' => 7,
            'feedback' => 'Good effort!',
            'suggestions' => ['Practice more']
        ];
    }

    

    /**
     * Get regional job market insights
     */
    public function getRegionalInsights($industry)
    {
        $currentLang = $this->langHelper->getCurrentLanguage();
        $cities = $this->langHelper->getRegionalCities();
        $langInfo = $this->langHelper->getLanguageInfo($currentLang);

        $apiKey = getenv('OPENAI_API_KEY');
        if (!$apiKey) {
            return $this->getFallbackInsights($industry, $cities);
        }

        $citiesList = implode(', ', $cities);
        $prompt = $currentLang === 'en'
            ? "Provide job market insights for {$industry} in these Indian cities: {$citiesList}. Include salary ranges, demand, and top companies."
            : "Provide job market insights for {$industry} in these Indian cities: {$citiesList}. Respond in {$langInfo['name']}. Include salary ranges (in INR), demand levels, and top companies.";

        $data = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $currentLang === 'en'
                        ? 'You are a job market analyst for India.'
                        : "You are a job market analyst for India. Provide insights in {$langInfo['name']}."
                ],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 800
        ];

        $response = $this->callOpenAI($data);

        return [
            'insights' => $response,
            'cities' => $cities,
            'language' => $currentLang
        ];
    }

    /**
     * Call OpenAI API
     */
    private function callOpenAI($data)
    {
        $apiKey = getenv('OPENAI_API_KEY');
        
        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $result = json_decode($response, true);
            return $result['choices'][0]['message']['content'] ?? null;
        }

        return null;
    }

    /**
     * Parse questions from AI response
     */
    private function parseQuestions($response)
    {
        $lines = explode("\n", $response);
        $questions = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if (preg_match('/^\d+[\.\)]\s*(.+)$/', $line, $matches)) {
                $questions[] = trim($matches[1]);
            }
        }

        return $questions ?: [$response];
    }

    /**
     * Parse evaluation from AI response
     */
    private function parseEvaluation($response)
    {
        // Simple parsing - can be enhanced
        preg_match('/score[:\s]+(\d+)/i', $response, $scoreMatch);
        $score = isset($scoreMatch[1]) ? (int)$scoreMatch[1] : 7;

        return [
            'score' => $score,
            'feedback' => $response,
            'suggestions' => []
        ];
    }

    /**
     * Fallback questions
     */
    private function getFallbackQuestions($jobRole)
    {
        return [
            "Tell me about yourself and your experience with {$jobRole}.",
            "What are your key strengths for this role?",
            "Describe a challenging project you worked on.",
            "Where do you see yourself in 5 years?",
            "Why are you interested in this position?"
        ];
    }

    /**
     * Fallback insights
     */
    private function getFallbackInsights($industry, $cities)
    {
        return [
            'insights' => "Job market for {$industry} is growing in " . implode(', ', $cities),
            'cities' => $cities,
            'language' => $this->langHelper->getCurrentLanguage()
        ];
    }

    /**
     * Voice instructions in current language
     */
    private function getVoiceInstructions()
    {
        $currentLang = $this->langHelper->getCurrentLanguage();
        
        $instructions = [
            'en' => 'Click the microphone button and speak your answer. The system will listen and evaluate your response.',
            'hi' => 'माइक्रोफोन बटन पर क्लिक करें और अपना उत्तर बोलें। सिस्टम आपकी प्रतिक्रिया सुनेगा और मूल्यांकन करेगा।',
            'ta' => 'மைக்ரோஃபோன் பொத்தானைக் கிளிக் செய்து உங்கள் பதிலைப் பேசுங்கள். கணினி உங்கள் பதிலைக் கேட்கும் மற்றும் மதிப்பீடு செய்யும்.',
            'te' => 'మైక్రోఫోన్ బటన్‌ను క్లిక్ చేసి మీ సమాధానం చెప్పండి. సిస్టమ్ మీ ప్రతిస్పందనను వింటుంది మరియు మూల్యాంకనం చేస్తుంది.',
            'kn' => 'ಮೈಕ್ರೊಫೋನ್ ಬಟನ್ ಕ್ಲಿಕ್ ಮಾಡಿ ಮತ್ತು ನಿಮ್ಮ ಉತ್ತರವನ್ನು ಮಾತನಾಡಿ. ಸಿಸ್ಟಮ್ ನಿಮ್ಮ ಪ್ರತಿಕ್ರಿಯೆಯನ್ನು ಆಲಿಸುತ್ತದೆ ಮತ್ತು ಮೌಲ್ಯಮಾಪನ ಮಾಡುತ್ತದೆ.',
            'ml' => 'മൈക്രോഫോൺ ബട്ടൺ ക്ലിക്ക് ചെയ്ത് നിങ്ങളുടെ ഉത്തരം പറയുക. സിസ്റ്റം നിങ്ങളുടെ പ്രതികരണം കേൾക്കുകയും വിലയിരുത്തുകയും ചെയ്യും.'
        ];

        return $instructions[$currentLang] ?? $instructions['en'];
    }
}