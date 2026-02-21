<?php

namespace App\Controllers;

use App\Models\JobModel;

class SlotBookingController extends BaseController
{
    /**
     * Show available slots for booking
     */
    public function bookSlot($applicationId)
    {
        $userId = session()->get('user_id');
        
        $applicationModel = model('ApplicationModel');
        $jobModel = model('JobModel');
        $slotModel = model('InterviewSlotModel');
        $bookingModel = model('InterviewBookingModel');
        
        // Get application
        $application = $applicationModel->find($applicationId);
        
        // Verify ownership and status
        if (!$application || $application['candidate_id'] != $userId) {
            return redirect()->to('/dashboard')->with('error', 'Application not found');
        }
        
        $job = $jobModel->find($application['job_id']);
        $aiPolicy = JobModel::normalizeAiPolicy($job['ai_interview_policy'] ?? JobModel::AI_POLICY_REQUIRED_HARD);

        if (!$this->canBookSlotForStatus($application['status'], $aiPolicy)) {
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
        $jobModel = model('JobModel');
        $slotModel = model('InterviewSlotModel');
        $bookingModel = model('InterviewBookingModel');
        $notificationModel = model('NotificationModel');
        
        // Verify application
        $application = $applicationModel->find($applicationId);
        if (!$application || $application['candidate_id'] != $userId) {
            return redirect()->back()->with('error', 'Invalid application');
        }

        $job = $jobModel->find($application['job_id']);
        $aiPolicy = JobModel::normalizeAiPolicy($job['ai_interview_policy'] ?? JobModel::AI_POLICY_REQUIRED_HARD);
        if (!$this->canBookSlotForStatus($application['status'], $aiPolicy)) {
            return redirect()->back()->with('error', 'You are not eligible to book a slot yet');
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

        $stageModel = model('StageHistoryModel');
            $stageModel->moveToStage($applicationId, 'Interview Slot Booked');
        
        // Create notification
        $notificationModel->triggerApplicationNotifications($userId, $applicationModel->find($applicationId));
            
        $db->transComplete();
        
        if ($db->transStatus()) {
            return redirect()->to('/candidate/my-bookings')->with('success', 'Interview slot booked successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to book slot. Please try again.');
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

    private function canBookSlotForStatus(string $status, string $aiPolicy): bool
    {
        if ($status === 'shortlisted') {
            return true;
        }

        if ($aiPolicy === JobModel::AI_POLICY_OPTIONAL) {
            return in_array($status, ['applied', 'ai_interview_completed'], true);
        }

        if ($aiPolicy === JobModel::AI_POLICY_OFF) {
            return $status === 'applied';
        }

        return false;
    }
}
