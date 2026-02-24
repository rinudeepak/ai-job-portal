<?php

namespace App\Controllers;

class NotificationController extends BaseController
{
    
    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $userId = session()->get('user_id');
        $notificationModel = model('NotificationModel');
        
        $notification = $notificationModel->find($id);
        
        // Verify ownership
        if ($notification && $notification['user_id'] == $userId) {
            $notificationModel->markAsRead($id);
            
            // Redirect to action link if exists
            if (!empty($notification['action_link'])) {
                return redirect()->to($notification['action_link']);
            }
        }
        
        return redirect()->back()->with('success', 'Notification marked as read');
    }
    
    /**
     * Mark all as read
     */
    public function markAllAsRead()
    {
        $userId = session()->get('user_id');
        $notificationModel = model('NotificationModel');
        
        $notificationModel->markAllAsRead($userId);
        
        return redirect()->back()->with('success', '');
    }
    
    /**
     * View all notifications
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $role = (string) session()->get('role');
        $notificationModel = model('NotificationModel');
        
        $notifications = $notificationModel->getUserNotifications($userId, 50);
        $unreadCount = $notificationModel->getUnreadCount($userId);

        $view = $role === 'recruiter' ? 'recruiter/notifications' : 'candidate/notifications';

        return view($view, [
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }
    
    /**
     * Delete notification
     */
    public function delete($id)
    {
        $userId = session()->get('user_id');
        $notificationModel = model('NotificationModel');
        
        $notification = $notificationModel->find($id);
        
        // Verify ownership
        if ($notification && $notification['user_id'] == $userId) {
            $notificationModel->delete($id);
            return redirect()->back()->with('success', 'Notification deleted');
        }
        
        return redirect()->back()->with('error', 'Notification not found');
    }
}
