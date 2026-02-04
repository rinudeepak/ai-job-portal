<?php

namespace App\Services;

class WhatsAppService
{
    protected $apiUrl;
    protected $apiKey;
    protected $from;
    
    public function __construct()
    {
        // Using Twilio WhatsApp API as an example
        // You can replace with any WhatsApp Business API provider
        $this->apiUrl = getenv('whatsapp.apiUrl') ?: 'https://api.twilio.com/2010-04-01/Accounts';
        $this->apiKey = getenv('whatsapp.apiKey');
        $this->accountSid = getenv('whatsapp.accountSid');
        $this->authToken = getenv('whatsapp.authToken');
        $this->from = getenv('whatsapp.fromNumber') ?: 'whatsapp:+14155238886'; // Twilio sandbox
    }
    
    /**
     * Send immediate booking confirmation via WhatsApp
     */
    public function sendInterviewConfirmation($booking)
    {
        if (empty($booking['candidate_phone'])) {
            log_message('warning', 'No phone number for candidate: ' . $booking['candidate_name']);
            return false;
        }
        
        $message = $this->getConfirmationMessage($booking);
        
        return $this->sendMessage($booking['candidate_phone'], $message);
    }
    
    /**
     * Send 24 hour reminder
     */
    public function send24HourReminder($booking)
    {
        if (empty($booking['candidate_phone'])) {
            return false;
        }
        
        $message = $this->get24HourReminderMessage($booking);
        
        return $this->sendMessage($booking['candidate_phone'], $message);
    }
    
    /**
     * Send 2 hour reminder
     */
    public function send2HourReminder($booking)
    {
        if (empty($booking['candidate_phone'])) {
            return false;
        }
        
        $message = $this->get2HourReminderMessage($booking);
        
        return $this->sendMessage($booking['candidate_phone'], $message);
    }
    
    /**
     * Send WhatsApp message using Twilio API
     */
    private function sendMessage($to, $message)
    {
        try {
            // Format phone number for WhatsApp
            $to = $this->formatPhoneNumber($to);
            
            $url = $this->apiUrl . '/' . $this->accountSid . '/Messages.json';
            
            $data = [
                'From' => $this->from,
                'To' => 'whatsapp:' . $to,
                'Body' => $message
            ];
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            curl_setopt($ch, CURLOPT_USERPWD, $this->accountSid . ':' . $this->authToken);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/x-www-form-urlencoded'
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode >= 200 && $httpCode < 300) {
                log_message('info', 'WhatsApp sent to: ' . $to);
                return true;
            } else {
                log_message('error', 'WhatsApp failed: ' . $response);
                return false;
            }
            
        } catch (\Exception $e) {
            log_message('error', 'WhatsApp error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Alternative: Using WhatsApp Business API (Facebook/Meta)
     */
    private function sendMessageViaBusinessAPI($to, $message)
    {
        try {
            $accessToken = getenv('whatsapp.accessToken');
            $phoneNumberId = getenv('whatsapp.phoneNumberId');
            
            $url = "https://graph.facebook.com/v17.0/{$phoneNumberId}/messages";
            
            $data = [
                'messaging_product' => 'whatsapp',
                'to' => $this->formatPhoneNumber($to),
                'type' => 'text',
                'text' => [
                    'body' => $message
                ]
            ];
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken
            ]);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            return ($httpCode >= 200 && $httpCode < 300);
            
        } catch (\Exception $e) {
            log_message('error', 'WhatsApp Business API error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Format phone number for WhatsApp (E.164 format)
     */
    private function formatPhoneNumber($phone)
    {
        // Remove all non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Add country code if not present (assuming +91 for India)
        if (strlen($phone) == 10) {
            $phone = '91' . $phone;
        }
        
        // Ensure it starts with +
        if (substr($phone, 0, 1) !== '+') {
            $phone = '+' . $phone;
        }
        
        return $phone;
    }
    
    /**
     * Confirmation message template
     */
    private function getConfirmationMessage($booking)
    {
        $date = date('l, F j, Y', strtotime($booking['slot_date']));
        $time = date('g:i A', strtotime($booking['slot_time']));
        
        return "✅ *Interview Confirmed*\n\n" .
               "Hi {$booking['candidate_name']},\n\n" .
               "Your interview has been scheduled:\n\n" .
               "📋 *Position:* {$booking['job_title']}\n" .
               "🏢 *Company:* {$booking['company_name']}\n" .
               "📅 *Date:* {$date}\n" .
               "⏰ *Time:* {$time}\n\n" .
               "We'll send you reminders before the interview.\n\n" .
               "Good luck! 🎯";
    }
    
    /**
     * 24 hour reminder message
     */
    private function get24HourReminderMessage($booking)
    {
        $date = date('l, F j', strtotime($booking['slot_date']));
        $time = date('g:i A', strtotime($booking['slot_time']));
        
        return "⏰ *Interview Reminder*\n\n" .
               "Hi {$booking['candidate_name']},\n\n" .
               "This is a reminder that your interview is *tomorrow*:\n\n" .
               "📋 *Position:* {$booking['job_title']}\n" .
               "📅 *Date:* {$date}\n" .
               "⏰ *Time:* {$time}\n\n" .
               "Tips:\n" .
               "✓ Review the job description\n" .
               "✓ Prepare your questions\n" .
               "✓ Test your internet connection\n" .
               "✓ Dress professionally\n\n" .
               "See you tomorrow! 👍";
    }
    
    /**
     * 2 hour reminder message
     */
    private function get2HourReminderMessage($booking)
    {
        $time = date('g:i A', strtotime($booking['slot_time']));
        
        return "🔔 *Interview Starting Soon!*\n\n" .
               "Hi {$booking['candidate_name']},\n\n" .
               "Your interview starts in *2 hours* at {$time}\n\n" .
               "📋 *Position:* {$booking['job_title']}\n\n" .
               "Quick checklist:\n" .
               "✓ Join link ready\n" .
               "✓ Camera and mic working\n" .
               "✓ Quiet environment\n" .
               "✓ Documents ready\n\n" .
               "All the best! 🌟";
    }
}