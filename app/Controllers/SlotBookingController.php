<?php

namespace App\Controllers;

class SlotBookingController extends BaseController
{
    /**
     * Show available slots for booking
     */
    public function bookSlot($applicationId)
    {
        $userId = session()->get('user_id');
        
        $applicationModel = model('ApplicationModel');
        $slotModel = model('InterviewSlotModel');
        $bookingModel = model('InterviewBookingModel');
        
        // Get application
        $application = $applicationModel->find($applicationId);
        
        // Verify ownership and status
        if (!$application || $application['candidate_id'] != $userId) {
            return redirect()->to('/dashboard')->with('error', 'Application not found');
        }
        
        // Only shortlisted candidates can book
        if ($application['status'] !== 'shortlisted') {
            return redirect()->to('/dashboard')->with('error', 'You are not eligible to book a slot yet');
        }
        
        // Check if already booked
        $existingBooking = $bookingModel->getByApplicationId($applicationId);
        if ($existingBooking) {
            return redirect()->to('/candidate/my-bookings')->with('info', 'You have already booked an interview slot');
        }
        
        // Get available slots
        $availableSlots = $slotModel->getAvailableSlotsGrouped($application['job_id']);
        
        return view('candidate/book_slot', [
            'application' => $application,
            'available_slots' => $availableSlots
        ]);
    }
    
    /**
     * Process slot booking
     */
    public function processBooking()
    {
        $userId = session()->get('user_id');
        $applicationId = $this->request->getPost('application_id');
        $slotId = $this->request->getPost('slot_id');
        
        $applicationModel = model('ApplicationModel');
        $slotModel = model('InterviewSlotModel');
        $bookingModel = model('InterviewBookingModel');
        $notificationModel = model('NotificationModel');
        
        // Verify application
        $application = $applicationModel->find($applicationId);
        if (!$application || $application['candidate_id'] != $userId) {
            return redirect()->back()->with('error', 'Invalid application');
        }
        
        // Check if slot is available
        if (!$slotModel->isSlotAvailable($slotId)) {
            return redirect()->back()->with('error', 'Selected slot is no longer available');
        }
        
        $slot = $slotModel->find($slotId);
        
        // Check if already booked
        if ($bookingModel->getByApplicationId($applicationId)) {
            return redirect()->to('/candidate/my-bookings')->with('error', 'Already booked');
        }
        
        // Create booking
        $db = \Config\Database::connect();
        $db->transStart();
        
        $bookingId = $bookingModel->insert([
            'application_id' => $applicationId,
            'user_id' => $userId,
            'job_id' => $application['job_id'],
            'slot_id' => $slotId,
            'slot_datetime' => $slot['slot_datetime'],
            'booking_status' => 'booked',
            'reschedule_count' => 0,
            'max_reschedules' => 2, // Configurable
            'can_reschedule' => 1,
            'booked_at' => date('Y-m-d H:i:s')
        ]);
        
        // Increment slot booked count
        $slotModel->incrementBookedCount($slotId);
        
        // Update application status and booking_id
        $applicationModel->update($applicationId, [
            'status' => 'interview_slot_booked',
            'interview_slot' => $slot['slot_datetime'],
            'booking_id' => $bookingId
        ]);
        
        // Create notification
        $notificationModel->triggerApplicationNotifications($userId, $applicationModel->find($applicationId));
                    // Schedule reminders
            // $this->scheduleReminders($bookingId, $application, $slot);
            
            // Send immediate confirmation
            // $this->sendBookingConfirmation($application, $slot);

        $db->transComplete();
        
        if ($db->transStatus()) {
            return redirect()->to('/candidate/my-bookings')->with('success', 'Interview slot booked successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to book slot. Please try again.');
        }
    }

        /**
     * Schedule all reminders for a booking
     */
    private function scheduleReminders($bookingId, $application, $slot)
    {
        $reminderModel = model('ReminderModel');
        
        $interviewDateTime = strtotime($slot['slot_datetime']);
        
        // Schedule reminders at different intervals
        $reminderIntervals = [
            '24_hours' => $interviewDateTime - (24 * 60 * 60),  // 24 hours before
            '2_hours' => $interviewDateTime - (2 * 60 * 60),    // 2 hours before
            '30_minutes' => $interviewDateTime - (30 * 60)      // 30 minutes before
        ];
        
        foreach ($reminderIntervals as $type => $sendTime) {
            // Only schedule if time is in the future
            if ($sendTime > time()) {
                // Email reminder
                $reminderModel->insert([
                    'booking_id' => $bookingId,
                    'user_id' => $application['candidate_id'],
                    'reminder_type' => 'email',
                    'reminder_interval' => $type,
                    'scheduled_time' => date('Y-m-d H:i:s', $sendTime),
                    'status' => 'scheduled',
                    'message_data' => json_encode([
                        'candidate_name' => $application['name'],
                        'job_title' => $application['job_title'],
                        'interview_date' => date('l, F d, Y', $interviewDateTime),
                        'interview_time' => date('h:i A', $interviewDateTime),
                        'email' => $application['email']
                    ])
                ]);
                
                // WhatsApp reminder (if phone number exists)
                if (!empty($application['phone'])) {
                    $reminderModel->insert([
                        'booking_id' => $bookingId,
                        'user_id' => $application['candidate_id'],
                        'reminder_type' => 'whatsapp',
                        'reminder_interval' => $type,
                        'scheduled_time' => date('Y-m-d H:i:s', $sendTime),
                        'status' => 'scheduled',
                        'message_data' => json_encode([
                            'candidate_name' => $application['name'],
                            'job_title' => $application['job_title'],
                            'interview_date' => date('l, F d, Y', $interviewDateTime),
                            'interview_time' => date('h:i A', $interviewDateTime),
                            'phone' => $application['phone']
                        ])
                    ]);
                }
            }
        }
    }
    
    /**
     * Send immediate booking confirmation
     */
    private function sendBookingConfirmation($application, $slot)
    {
        $emailService = service('email');
        $whatsappService = service('whatsapp');
        
        $interviewDateTime = strtotime($slot['slot_datetime']);
        
        // Email confirmation
        $emailData = [
            'to' => $application['email'],
            'subject' => 'Interview Confirmed - ' . $application['job_title'],
            'template' => 'booking_confirmation',
            'data' => [
                'candidate_name' => $application['name'],
                'job_title' => $application['job_title'],
                'interview_date' => date('l, F d, Y', $interviewDateTime),
                'interview_time' => date('h:i A', $interviewDateTime),
                'booking_link' => base_url('candidate/my-bookings')
            ]
        ];
        
        $emailService->send($emailData);
        
        // WhatsApp confirmation
        if (!empty($application['phone'])) {
            $message = "🎉 Interview Confirmed!\n\n";
            $message .= "Hi {$application['name']},\n\n";
            $message .= "Your interview for *{$application['job_title']}* has been scheduled.\n\n";
            $message .= "📅 Date: " . date('l, F d, Y', $interviewDateTime) . "\n";
            $message .= "🕒 Time: " . date('h:i A', $interviewDateTime) . "\n\n";
            $message .= "You will receive reminders before your interview.\n\n";
            $message .= "Good luck! 🍀";
            
            $whatsappService->send($application['phone'], $message);
        }
    }

    
    /**
     * Show reschedule page
     */
    public function rescheduleSlot($applicationId)
    {
        $userId = session()->get('user_id');
        
        $applicationModel = model('ApplicationModel');
        $slotModel = model('InterviewSlotModel');
        $bookingModel = model('InterviewBookingModel');
        $rescheduleHistoryModel = model('RescheduleHistoryModel');
        
        // Get application and booking
        $application = $applicationModel->find($applicationId);
        
        if (!$application || $application['candidate_id'] != $userId) {
            return redirect()->to('/dashboard')->with('error', 'Application not found');
        }
        
        $booking = $bookingModel->getByApplicationId($applicationId);
        
        if (!$booking) {
            return redirect()->to('/dashboard')->with('error', 'No booking found');
        }
        
        // Check if can reschedule
        $canReschedule = $bookingModel->canReschedule($booking['id']);
        
        if (!$canReschedule['can_reschedule']) {
            return redirect()->to('/candidate/my-bookings')->with('error', $canReschedule['reason']);
        }
        
        // Get available slots (excluding current)
        $availableSlots = $slotModel->getAvailableSlotsGrouped($application['job_id']);
        
        // Get reschedule history
        $history = $rescheduleHistoryModel->getBookingHistory($booking['id']);
        
        return view('candidate/reschedule_slot', [
            'application' => $application,
            'booking' => $booking,
            'available_slots' => $availableSlots,
            'can_reschedule_info' => $canReschedule,
            'history' => $history
        ]);
    }
    
    /**
     * Process reschedule
     */
    public function processReschedule()
    {
        $userId = session()->get('user_id');
        $applicationId = $this->request->getPost('application_id');
        $newSlotId = $this->request->getPost('slot_id');
        $reason = $this->request->getPost('reason');
        
        // Validate slot selection
        if (empty($newSlotId)) {
            return redirect()->back()->with('error', 'Please select a new time slot to reschedule your interview.');
        }
        
        $applicationModel = model('ApplicationModel');
        $bookingModel = model('InterviewBookingModel');
        $notificationModel = model('NotificationModel');
        
        // Verify application
        $application = $applicationModel->find($applicationId);
        if (!$application || $application['candidate_id'] != $userId) {
            return redirect()->back()->with('error', 'Invalid application');
        }
        
        $booking = $bookingModel->getByApplicationId($applicationId);
        
        if (!$booking) {
            return redirect()->back()->with('error', 'Booking not found');
        }
        
        // Attempt reschedule
        $success = $bookingModel->rescheduleBooking($booking['id'], $newSlotId, $reason);
        
        if ($success) {
            // Update application
            $updatedBooking = $bookingModel->find($booking['id']);
            $applicationModel->update($applicationId, [
                'interview_slot' => $updatedBooking['slot_datetime']
            ]);
            
            // Trigger notification
            $notificationModel->triggerApplicationNotifications($userId, $applicationModel->find($applicationId));
            
            return redirect()->to('/candidate/my-bookings')->with('success', 'Interview rescheduled successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to reschedule. Please check slot availability and limits.');
        }
    }
    
    /**
     * View my bookings
     */
    public function myBookings()
    {
        $userId = session()->get('user_id');
        $bookingModel = model('InterviewBookingModel');
        
        $bookings = $bookingModel->getUserBookings($userId);
        
        return view('candidate/my_bookings', [
            'bookings' => $bookings
        ]);
    }
}
