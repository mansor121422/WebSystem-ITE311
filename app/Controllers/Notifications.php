<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Notifications extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    /**
     * API method that returns JSON response with current user's notifications
     * Called via AJAX for real-time updates
     * 
     * @return mixed
     */
    public function get()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue.',
                'unreadCount' => 0,
                'notifications' => []
            ]);
        }

        $userId = session()->get('userID');
        
        try {
            // Get unread notification count
            $unreadCount = $this->notificationModel->getUnreadCount($userId);
            
            // Get recent notifications (limit 10 for API)
            $notifications = $this->notificationModel->getNotificationsForUser($userId, 10);
            
            // Format notifications for JSON response
            $formattedNotifications = [];
            foreach ($notifications as $notification) {
                $formattedNotifications[] = [
                    'id' => $notification['id'],
                    'message' => $notification['message'],
                    'is_read' => (bool)$notification['is_read'],
                    'created_at' => $notification['created_at'],
                    'time_ago' => $this->timeAgo($notification['created_at'])
                ];
            }
            
            return $this->response->setJSON([
                'success' => true,
                'unreadCount' => $unreadCount,
                'notifications' => $formattedNotifications
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Failed to get notifications: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load notifications.',
                'unreadCount' => 0,
                'notifications' => []
            ]);
        }
    }

    /**
     * Display notifications page
     * 
     * @return mixed
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to view notifications.');
        }

        $userId = session()->get('userID');
        
        // Get all notifications for the user
        $notifications = $this->notificationModel->getNotificationsForUser($userId, 20);
        
        $data = [
            'title' => 'Notifications',
            'notifications' => $notifications,
            'unreadCount' => $this->notificationModel->getUnreadCount($userId)
        ];

        return view('notifications/index', $data);
    }

    /**
     * Display API test page
     * 
     * @return mixed
     */
    public function apiTest()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to test the API.');
        }

        return view('notifications/api_test');
    }

    /**
     * Display notification system demo page
     * 
     * @return mixed
     */
    public function demo()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to view the demo.');
        }

        return view('notifications/demo');
    }

    /**
     * Display Step 7 test page for notification generation
     * 
     * @return mixed
     */
    public function step7Test()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to view the Step 7 test page.');
        }

        return view('notifications/step7_test');
    }

    /**
     * Display Step 8 test page for comprehensive functionality testing
     * 
     * @return mixed
     */
    public function step8Test()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to view the Step 8 test page.');
        }

        return view('notifications/step8_test');
    }

    /**
     * API method to mark a notification as read via POST
     * Accepts notification ID and returns success/failure JSON response
     * 
     * @param int $id
     * @return mixed
     */
    public function mark_as_read($id = null)
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue.'
            ]);
        }

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification ID is required.'
            ]);
        }

        // Verify the notification belongs to the current user
        $notification = $this->notificationModel->find($id);
        if (!$notification || $notification['user_id'] != session()->get('userID')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notification not found or access denied.'
            ]);
        }

        if ($this->notificationModel->markAsRead($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notification marked as read.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark notification as read.'
            ]);
        }
    }

    /**
     * Mark all notifications as read for the current user
     * 
     * @return mixed
     */
    public function markAllAsRead()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to continue.'
            ]);
        }

        $userId = session()->get('userID');

        if ($this->notificationModel->markAllAsRead($userId)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'All notifications marked as read.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to mark notifications as read.'
            ]);
        }
    }

    /**
     * Create a test notification (for testing purposes)
     * 
     * @return mixed
     */
    public function createTest()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login')->with('error', 'Please login to continue.');
        }

        $userId = session()->get('userID');
        $message = "Test notification created at " . date('Y-m-d H:i:s');

        if ($this->notificationModel->createNotification($userId, $message)) {
            return redirect()->to('/notifications')->with('success', 'Test notification created!');
        } else {
            return redirect()->to('/notifications')->with('error', 'Failed to create test notification.');
        }
    }

    /**
     * Helper method to format time ago string
     * 
     * @param string $datetime
     * @return string
     */
    private function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) {
            return 'Just now';
        } elseif ($time < 3600) {
            $minutes = floor($time / 60);
            return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
        } elseif ($time < 86400) {
            $hours = floor($time / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($time < 2592000) {
            $days = floor($time / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M j, Y', strtotime($datetime));
        }
    }
}
