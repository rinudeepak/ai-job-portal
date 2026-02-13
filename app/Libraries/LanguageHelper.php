<?php

namespace App\Libraries;

class LanguageHelper
{
    protected $currentLanguage;
    protected $config;
    protected $translations = [];

    public function __construct()
    {
        $this->config = config('Language');
        $this->currentLanguage = $this->detectLanguage();
    }

    /**
     * Detect user's preferred language
     */
    public function detectLanguage()
    {
        // 1. Check session
        if (session()->has('user_language')) {
            return session()->get('user_language');
        }

        // 2. Check user profile (if logged in)
        if (session()->has('user_id')) {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find(session()->get('user_id'));
            if ($user && !empty($user['preferred_language'])) {
                return $user['preferred_language'];
            }
        }

        // 3. Check browser language
        if ($this->config->autoDetect && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            if (isset($this->config->supportedLanguages[$browserLang])) {
                return $browserLang;
            }
        }

        // 4. Default to English
        return $this->config->defaultLanguage;
    }

    /**
     * Set language
     */
    public function setLanguage($langCode)
    {
        if (isset($this->config->supportedLanguages[$langCode])) {
            $this->currentLanguage = $langCode;
            session()->set('user_language', $langCode);
            
            // Update user profile if logged in
            if (session()->has('user_id')) {
                $userModel = new \App\Models\UserModel();
                $userModel->update(session()->get('user_id'), [
                    'preferred_language' => $langCode
                ]);
            }
            
            return true;
        }
        return false;
    }

    /**
     * Get current language
     */
    public function getCurrentLanguage()
    {
        return $this->currentLanguage;
    }

    /**
     * Get language info
     */
    public function getLanguageInfo($langCode = null)
    {
        $langCode = $langCode ?? $this->currentLanguage;
        return $this->config->supportedLanguages[$langCode] ?? null;
    }

    /**
     * Get all supported languages
     */
    public function getSupportedLanguages()
    {
        return array_filter($this->config->supportedLanguages, function($lang) {
            return $lang['enabled'];
        });
    }

    /**
     * Load translation file
     */
    public function loadTranslations($file)
    {
        $path = APPPATH . "Language/{$this->currentLanguage}/{$file}.php";
        
        if (file_exists($path)) {
            $this->translations = array_merge($this->translations, require $path);
        } elseif ($this->config->fallbackToEnglish && $this->currentLanguage !== 'en') {
            // Fallback to English
            $englishPath = APPPATH . "Language/en/{$file}.php";
            if (file_exists($englishPath)) {
                $this->translations = array_merge($this->translations, require $englishPath);
            }
        }
    }

    /**
     * Translate text
     */
    public function translate($key, $params = [])
    {
        $text = $this->translations[$key] ?? $key;
        
        // Replace parameters
        foreach ($params as $param => $value) {
            $text = str_replace('{' . $param . '}', $value, $text);
        }
        
        return $text;
    }

    /**
     * Shorthand for translate
     */
    public function t($key, $params = [])
    {
        return $this->translate($key, $params);
    }

    /**
     * Get regional cities for current language
     */
    public function getRegionalCities()
    {
        return $this->config->regionalMarkets[$this->currentLanguage]['cities'] ?? 
               $this->config->regionalMarkets['en']['cities'];
    }

    /**
     * Translate AI-generated content
     */
    public function translateAIContent($text, $targetLang = null)
    {
        $targetLang = $targetLang ?? $this->currentLanguage;
        
        // If already in target language or target is English, return as-is
        if ($targetLang === 'en') {
            return $text;
        }

        // Check cache
        $cacheKey = 'translation_' . md5($text . $targetLang);
        if ($this->config->translationAPI['cache_translations']) {
            $cached = cache()->get($cacheKey);
            if ($cached) {
                return $cached;
            }
        }

        // Translate using AI
        $translated = $this->callTranslationAPI($text, $targetLang);
        
        // Cache result
        if ($this->config->translationAPI['cache_translations'] && $translated) {
            cache()->save($cacheKey, $translated, $this->config->translationAPI['cache_duration']);
        }

        return $translated ?? $text;
    }

    /**
     * Call translation API (OpenAI)
     */
    private function callTranslationAPI($text, $targetLang)
    {
        $langInfo = $this->getLanguageInfo($targetLang);
        $langName = $langInfo['name'];

        $apiKey = getenv('OPENAI_API_KEY');
        if (!$apiKey) {
            log_message('error', 'Translation API key not found');
            return null;
        }

        $prompt = "Translate the following text to {$langName}. Maintain the tone and formatting. Only return the translated text, nothing else:\n\n{$text}";

        $data = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a professional translator specializing in Indian languages. Translate accurately while preserving technical terms and context.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.3,
            'max_tokens' => 2000
        ];

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

        log_message('error', 'Translation API failed: ' . $response);
        return null;
    }

    /**
     * Get voice code for text-to-speech
     */
    public function getVoiceCode($langCode = null)
    {
        $langCode = $langCode ?? $this->currentLanguage;
        return $this->config->supportedLanguages[$langCode]['voice_code'] ?? 'en-IN';
    }

    /**
     * Check if voice is enabled
     */
    public function isVoiceEnabled()
    {
        return $this->config->voiceSettings['enabled'];
    }
}