<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

class Language extends BaseConfig
{
    /**
     * Supported Languages
     * Key: Language code
     * Value: Language details
     */
    public $supportedLanguages = [
        'en' => [
            'name' => 'English',
            'native_name' => 'English',
            'locale' => 'en_US',
            'direction' => 'ltr',
            'flag' => '🇬🇧',
            'voice_code' => 'en-IN', // Google TTS code
            'enabled' => true
        ],
        'hi' => [
            'name' => 'Hindi',
            'native_name' => 'हिन्दी',
            'locale' => 'hi_IN',
            'direction' => 'ltr',
            'flag' => '🇮🇳',
            'voice_code' => 'hi-IN',
            'enabled' => true
        ],
        'ta' => [
            'name' => 'Tamil',
            'native_name' => 'தமிழ்',
            'locale' => 'ta_IN',
            'direction' => 'ltr',
            'flag' => '🇮🇳',
            'voice_code' => 'ta-IN',
            'enabled' => true
        ],
        'te' => [
            'name' => 'Telugu',
            'native_name' => 'తెలుగు',
            'locale' => 'te_IN',
            'direction' => 'ltr',
            'flag' => '🇮🇳',
            'voice_code' => 'te-IN',
            'enabled' => true
        ],
        'kn' => [
            'name' => 'Kannada',
            'native_name' => 'ಕನ್ನಡ',
            'locale' => 'kn_IN',
            'direction' => 'ltr',
            'flag' => '🇮🇳',
            'voice_code' => 'kn-IN',
            'enabled' => true
        ],
        'ml' => [
            'name' => 'Malayalam',
            'native_name' => 'മലയാളം',
            'locale' => 'ml_IN',
            'direction' => 'ltr',
            'flag' => '🇮🇳',
            'voice_code' => 'ml-IN',
            'enabled' => true
        ]
    ];

    /**
     * Default language
     */
    public $defaultLanguage = 'en';

    /**
     * Auto-detect language from browser
     */
    public $autoDetect = true;

    /**
     * Fallback to English if translation missing
     */
    public $fallbackToEnglish = true;

    /**
     * Translation API Settings (for AI content translation)
     */
    public $translationAPI = [
        'provider' => 'openai', // 'openai', 'google', 'azure'
        'cache_translations' => true,
        'cache_duration' => 86400 * 30, // 30 days
    ];

    /**
     * Voice Settings
     */
    public $voiceSettings = [
        'enabled' => false,
        'provider' => 'browser', // 'browser' (Web Speech API) or 'google' (Google Cloud TTS)
        'speech_rate' => 1.0,
        'speech_pitch' => 1.0,
    ];

    /**
     * Regional Job Markets
     */
    public $regionalMarkets = [
        'en' => ['cities' => ['Bangalore', 'Mumbai', 'Delhi', 'Hyderabad', 'Chennai', 'Pune']],
        'hi' => ['cities' => ['Delhi', 'Mumbai', 'Jaipur', 'Lucknow', 'Kanpur']],
        'ta' => ['cities' => ['Chennai', 'Coimbatore', 'Madurai', 'Trichy', 'Salem']],
        'te' => ['cities' => ['Hyderabad', 'Vijayawada', 'Visakhapatnam', 'Warangal']],
        'kn' => ['cities' => ['Bangalore', 'Mysore', 'Mangalore', 'Hubli', 'Belgaum']],
        'ml' => ['cities' => ['Kochi', 'Thiruvananthapuram', 'Kozhikode', 'Thrissur']],
    ];
}