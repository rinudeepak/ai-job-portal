<?php

namespace App\Services;

use CodeIgniter\I18n\Time;

class InterviewReminderService
{
    protected $emailService;
    protected $whatsappService;
    protected $bookingModel;
    
    public function __construct()
    {
        $this->emailService = new EmailService();
        $this->whatsappService = new WhatsAppService();
        $this->bookingModel = model('InterviewBookingModel');
    }
    
    /**
     * Send immediate confirmation after booking
     */
    public function sendBookingConfirmation($bookingId)
    {
        $booking = $this->getBookingDetails($bookingId);
        
        if (!$booking) {
            log_message('error', "Booking not found: {$bookingId}");
            return false;
        }
        
        // Send email confirmation
        $emailSent = $this->emailService->sendInterviewConfirmation($booking);
        
        // Send WhatsApp confirmation
        $whatsappSent = $this->whatsappService->sendInterviewConfirmation($booking);
        
        // Log the confirmation
        $this->logReminder($bookingId, 'confirmation', $emailSent, $whatsappSent);
        
        return $emailSent || $whatsappSent;
    }
    
    /**
     * Send reminder 24 hours before interview
     */
    public function send24HourReminder($bookingId)
    {
        $booking = $this->getBookingDetails($bookingId);
        
        if (!$booking) {
            return false;
        }
        
        // Check if interview is actually in 24 hours
        $interviewTime = strtotime($booking['slot_datetime']);
        $now = time();
        $diff = $interviewTime - $now;
        
        // Should be between 23-25 hours (give 1 hour buffer)
        if ($diff < (23 * 3600) || $diff > (25 * 3600)) {
            log_message('info', "Skipping 24h reminder - not the right time. Diff: " . ($diff/3600) . " hours");
            return false;
        }
        
        // Send email reminder
        $emailSent = $this->emailService->send24HourReminder($booking);
        
        // Send WhatsApp reminder
        $whatsappSent = $this->whatsappService->send24HourReminder($booking);
        
        // Log the reminder
        $this->logReminder($bookingId, '24_hour', $emailSent, $whatsappSent);
        
        return $emailSent || $whatsappSent;
    }
    
    /**
     * Send reminder 2 hours before interview
     */
    public function send2HourReminder($bookingId)
    {
        $booking = $this->getBookingDetails($bookingId);
        
        if (!$booking) {
            return false;
        }
        
        // Check if interview is actually in 2 hours
        $interviewTime = strtotime($booking['slot_datetime']);
        $now = time();
        $diff = $interviewTime - $now;
        
        // Should be between 1.5-2.5 hours
        if ($diff < (1.5 * 3600) || $diff > (2.5 * 3600)) {
            return false;
        }
        
        // Send email reminder
        $emailSent = $this->emailService->send2HourReminder($booking);
        
        // Send WhatsApp reminder
        $whatsappSent = $this->whatsappService->send2HourReminder($booking);
        
        // Log the reminder
        $this->logReminder($bookingId, '2_hour', $emailSent, $whatsappSent);
        
        return $emailSent || $whatsappSent;
    }
    
    /**
     * Process all pending reminders (Called by cron job)
     */
    public function processAllReminders()
    {
        $results = [
            '24_hour' => ['sent' => 0, 'failed' => 0],
            '2_hour' => ['sent' => 0, 'failed' => 0]
        ];
        
        // Get bookings for 24 hour reminders
        $bookings24h = $this->getBookingsForReminder(24);
        
        foreach ($bookings24h as $booking) {
            if ($this->shouldSendReminder($booking['id'], '24_hour')) {
                $sent = $this->send24HourReminder($booking['id']);
                if ($sent) {
                    $results['24_hour']['sent']++;
                } else {
                    $results['24_hour']['failed']++;
                }
            }
        }
        
        // Get bookings for 2 hour reminders
        $bookings2h = $this->getBookingsForReminder(2);
        
        foreach ($bookings2h as $booking) {
            if ($this->shouldSendReminder($booking['id'], '2_hour')) {
                $sent = $this->send2HourReminder($booking['id']);
                if ($sent) {
                    $results['2_hour']['sent']++;
                } else {
                    $results['2_hour']['failed']++;
                }
            }
        }
        
        log_message('info', 'Reminder processing complete: ' . json_encode($results));
        
        return $results;
    }
    
    /**
     * Get booking details with user and job info
     */
    private function getBookingDetails($bookingId)
    {
        return $this->bookingModel
            ->select('
                interview_bookings.*,
                users.name as candidate_name,
                users.email as candidate_email,
                users.phone as candidate_phone,
                jobs.title as job_title,
                jobs.company_name,
                interview_slots.slot_date,
                interview_slots.slot_time,
                interview_slots.slot_datetime
            ')
            ->join('users', 'users.id = interview_bookings.user_id', 'left')
            ->join('jobs', 'jobs.id = interview_bookings.job_id', 'left')
            ->join('interview_slots', 'interview_slots.id = interview_bookings.slot_id', 'left')
            ->where('interview_bookings.id', $bookingId)
            ->first();
    }
    
    /**
     * Get bookings that need reminders
     */
    private function getBookingsForReminder($hoursAhead)
    {
        $db = \Config\Database::connect();
        
        $targetTime = date('Y-m-d H:i:s', strtotime("+{$hoursAhead} hours"));
        $bufferStart = date('Y-m-d H:i:s', strtotime("+{$hoursAhead} hours - 30 minutes"));
        $bufferEnd = date('Y-m-d H:i:s', strtotime("+{$hoursAhead} hours + 30 minutes"));
        
        $query = "
            SELECT interview_bookings.*
            FROM interview_bookings
            INNER JOIN interview_slots ON interview_slots.id = interview_bookings.slot_id
            WHERE interview_bookings.booking_status = 'confirmed'
            AND interview_slots.slot_datetime BETWEEN ? AND ?
        ";
        
        return $db->query($query, [$bufferStart, $bufferEnd])->getResultArray();
    }
    
    /**
     * Check if reminder should be sent (prevent duplicates)
     */
    private function shouldSendReminder($bookingId, $reminderType)
    {
        $reminderLogModel = model('ReminderLogModel');
        
        $existing = $reminderLogModel
            ->where('booking_id', $bookingId)
            ->where('reminder_type', $reminderType)
            ->where('sent_at >', date('Y-m-d H:i:s', strtotime('-6 hours'))) // Check last 6 hours
            ->first();
        
        return empty($existing);
    }
    
    /**
     * Log reminder sent
     */
    private function logReminder($bookingId, $reminderType, $emailSent, $whatsappSent)
    {
        $reminderLogModel = model('ReminderLogModel');
        
        return $reminderLogModel->insert([
            'booking_id' => $bookingId,
            'reminder_type' => $reminderType,
            'email_sent' => $emailSent ? 1 : 0,
            'whatsapp_sent' => $whatsappSent ? 1 : 0,
            'sent_at' => date('Y-m-d H:i:s')
        ]);
    }
}