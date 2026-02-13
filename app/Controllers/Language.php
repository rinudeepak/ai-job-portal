<?php

namespace App\Controllers;

class Language extends BaseController
{
    /**
     * Switch language - Simplified version with error handling
     */
    public function switch($langCode = null)
    {
        try {
            // Validate language code
            if (!$langCode) {
                throw new \Exception('Language code is required');
            }

            // Supported languages
            $supportedLangs = ['en', 'hi', 'ta', 'te', 'kn', 'ml'];
            
            if (!in_array($langCode, $supportedLangs)) {
                throw new \Exception('Invalid language code: ' . $langCode);
            }

            // Set language in session
            session()->set('user_language', $langCode);

            // Update user profile if logged in
            if (session()->has('user_id')) {
                try {
                    $userModel = new \App\Models\UserModel();
                    $userId = session()->get('user_id');
                    
                    // Check if column exists before updating
                    $db = \Config\Database::connect();
                    $fields = $db->getFieldNames('users');
                    
                    if (in_array('preferred_language', $fields)) {
                        $db->table('users')
   ->where('id', $userId)
   ->update(['preferred_language' => $langCode]);
// Direct SQL update, no validation errors

                    }
                } catch (\Exception $e) {
                    // Log error but don't fail the request
                    log_message('error', 'Failed to update user language preference: ' . $e->getMessage());
                }
            }

            // For AJAX requests
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'language' => $langCode,
                    'message' => 'Language changed successfully'
                ]);
            }
            
            // For regular requests
            return redirect()->back()->with('success', 'Language changed successfully');

        } catch (\Exception $e) {
            log_message('error', 'Language switch error: ' . $e->getMessage());
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $e->getMessage()
                ])->setStatusCode(500);
            }
            
            return redirect()->back()->with('error', 'Failed to change language: ' . $e->getMessage());
        }
    }

    /**
     * Get current language
     */
    public function current()
    {
        try {
            $currentLang = session()->get('user_language') ?? 'en';
            
            $languages = [
                'en' => ['name' => 'English', 'native_name' => 'English', 'flag' => '🇬🇧'],
                'hi' => ['name' => 'Hindi', 'native_name' => 'हिन्दी', 'flag' => '🇮🇳'],
                'ta' => ['name' => 'Tamil', 'native_name' => 'தமிழ்', 'flag' => '🇮🇳'],
                'te' => ['name' => 'Telugu', 'native_name' => 'తెలుగు', 'flag' => '🇮🇳'],
                'kn' => ['name' => 'Kannada', 'native_name' => 'ಕನ್ನಡ', 'flag' => '🇮🇳'],
                'ml' => ['name' => 'Malayalam', 'native_name' => 'മലയാളം', 'flag' => '🇮🇳'],
            ];
            
            return $this->response->setJSON([
                'code' => $currentLang,
                'info' => $languages[$currentLang] ?? $languages['en']
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get all supported languages
     */
    public function supported()
    {
        try {
            $languages = [
                'en' => ['name' => 'English', 'native_name' => 'English', 'flag' => '🇬🇧', 'enabled' => true],
                'hi' => ['name' => 'Hindi', 'native_name' => 'हिन्दी', 'flag' => '🇮🇳', 'enabled' => true],
                'ta' => ['name' => 'Tamil', 'native_name' => 'தமிழ்', 'flag' => '🇮🇳', 'enabled' => true],
                'te' => ['name' => 'Telugu', 'native_name' => 'తెలుగు', 'flag' => '🇮🇳', 'enabled' => true],
                'kn' => ['name' => 'Kannada', 'native_name' => 'ಕನ್ನಡ', 'flag' => '🇮🇳', 'enabled' => true],
                'ml' => ['name' => 'Malayalam', 'native_name' => 'മലയാളം', 'flag' => '🇮🇳', 'enabled' => true],
            ];
            
            return $this->response->setJSON([
                'languages' => $languages
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}