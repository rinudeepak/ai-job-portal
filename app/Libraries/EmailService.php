<?php

namespace App\Services;

class EmailService
{
    protected $email;
    
    public function __construct()
    {
        $this->email = \Config\Services::email();
        
        // Email configuration
        $config = [
            'protocol' => 'smtp',
            'SMTPHost' => getenv('email.SMTPHost'),
            'SMTPUser' => getenv('email.SMTPUser'),
            'SMTPPass' => getenv('email.SMTPPass'),
            'SMTPPort' => getenv('email.SMTPPort') ?: 587,
            'SMTPCrypto' => getenv('email.SMTPCrypto') ?: 'tls',
            'mailType' => 'html',
            'charset' => 'utf-8',
            'newline' => "\r\n"
        ];
        
        $this->email->initialize($config);
    }
    
    /**
     * Send immediate booking confirmation
     */
    public function sendInterviewConfirmation($booking)
    {
        try {
            $this->email->setFrom(getenv('email.fromEmail'), getenv('email.fromName'));
            $this->email->setTo($booking['candidate_email']);
            $this->email->setSubject('Interview Confirmed - ' . $booking['job_title']);
            
            $message = $this->getConfirmationEmailTemplate($booking);
            $this->email->setMessage($message);
            
            $sent = $this->email->send();
            
            if (!$sent) {
                log_message('error', 'Email confirmation failed: ' . $this->email->printDebugger());
            }
            
            return $sent;
            
        } catch (\Exception $e) {
            log_message('error', 'Email error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send 24 hour reminder
     */
    public function send24HourReminder($booking)
    {
        try {
            $this->email->setFrom(getenv('email.fromEmail'), getenv('email.fromName'));
            $this->email->setTo($booking['candidate_email']);
            $this->email->setSubject('Reminder: Interview Tomorrow - ' . $booking['job_title']);
            
            $message = $this->get24HourReminderTemplate($booking);
            $this->email->setMessage($message);
            
            return $this->email->send();
            
        } catch (\Exception $e) {
            log_message('error', 'Email error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send 2 hour reminder
     */
    public function send2HourReminder($booking)
    {
        try {
            $this->email->setFrom(getenv('email.fromEmail'), getenv('email.fromName'));
            $this->email->setTo($booking['candidate_email']);
            $this->email->setSubject('Reminder: Interview in 2 Hours - ' . $booking['job_title']);
            
            $message = $this->get2HourReminderTemplate($booking);
            $this->email->setMessage($message);
            
            return $this->email->send();
            
        } catch (\Exception $e) {
            log_message('error', 'Email error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Confirmation email template
     */
    private function getConfirmationEmailTemplate($booking)
    {
        $interviewDate = date('l, F j, Y', strtotime($booking['slot_date']));
        $interviewTime = date('g:i A', strtotime($booking['slot_time']));
        
        return view('emails/interview_confirmation', [
            'candidate_name' => $booking['candidate_name'],
            'job_title' => $booking['job_title'],
            'company_name' => $booking['company_name'] ?? 'Our Company',
            'interview_date' => $interviewDate,
            'interview_time' => $interviewTime,
            'booking_id' => $booking['id']
        ]);
    }
    
    /**
     * 24 hour reminder template
     */
    private function get24HourReminderTemplate($booking)
    {
        $interviewDate = date('l, F j, Y', strtotime($booking['slot_date']));
        $interviewTime = date('g:i A', strtotime($booking['slot_time']));
        
        return view('emails/interview_reminder_24h', [
            'candidate_name' => $booking['candidate_name'],
            'job_title' => $booking['job_title'],
            'company_name' => $booking['company_name'] ?? 'Our Company',
            'interview_date' => $interviewDate,
            'interview_time' => $interviewTime,
            'booking_id' => $booking['id']
        ]);
    }
    
    /**
     * 2 hour reminder template
     */
    private function get2HourReminderTemplate($booking)
    {
        $interviewTime = date('g:i A', strtotime($booking['slot_time']));
        
        return view('emails/interview_reminder_2h', [
            'candidate_name' => $booking['candidate_name'],
            'job_title' => $booking['job_title'],
            'company_name' => $booking['company_name'] ?? 'Our Company',
            'interview_time' => $interviewTime,
            'booking_id' => $booking['id']
        ]);
    }
}