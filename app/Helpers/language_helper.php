<?php

/**
 * Translation Helper Functions
 * File: app/Helpers/language_helper.php
 */

if (!function_exists('lang_text')) {
    /**
     * Get translated text
     * 
     * @param string $key Translation key
     * @param array $params Optional parameters to replace
     * @return string Translated text
     */
    function lang_text($key, $params = [])
    {
        $currentLang = session()->get('user_language') ?? 'en';
        
        // Load translation file
        $translationFile = APPPATH . "Language/{$currentLang}/common.php";
        
        // Fallback to English if translation file doesn't exist
        if (!file_exists($translationFile)) {
            $translationFile = APPPATH . "Language/en/common.php";
        }
        
        $translations = [];
        if (file_exists($translationFile)) {
            $translations = require $translationFile;
        }
        
        // Get translation or return key if not found
        $text = $translations[$key] ?? $key;
        
        // Replace parameters
        foreach ($params as $param => $value) {
            $text = str_replace('{' . $param . '}', $value, $text);
        }
        
        return $text;
    }
}

if (!function_exists('current_language')) {
    /**
     * Get current language code
     * 
     * @return string Language code (e.g., 'en', 'hi', 'ta')
     */
    function current_language()
    {
        return session()->get('user_language') ?? 'en';
    }
}

if (!function_exists('is_rtl')) {
    /**
     * Check if current language is RTL
     * 
     * @return bool
     */
    function is_rtl()
    {
        $rtlLanguages = ['ar', 'he', 'ur'];
        return in_array(current_language(), $rtlLanguages);
    }
}