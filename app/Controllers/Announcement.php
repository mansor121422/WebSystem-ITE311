<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;
use App\Models\NotificationModel;

class Announcement extends BaseController
{
    protected $announcementModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->announcementModel = new AnnouncementModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Display all announcements
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            session()->setFlashdata('error', 'You must be logged in to view announcements.');
            return redirect()->to('/login');
        }

        // Fetch all announcements from database, ordered by created_at (newest first)
        // Task 2 Requirement: Order by created_at in descending order
        $announcements = $this->announcementModel->orderBy('created_at', 'DESC')->findAll();

        // Prepare data for view
        $data = [
            'title' => 'Announcements - LMS System',
            'announcements' => $announcements,
            'user' => [
                'name' => session('name'),
                'role' => session('role')
            ]
        ];

        // Load the announcements view
        return view('announcements', $data);
    }

    /**
     * Create new announcement (Admin only)
     */
    public function create()
    {
        // Check if user is logged in and is admin
        if (!session()->get('logged_in')) {
            session()->setFlashdata('error', 'You must be logged in.');
            return redirect()->to('/login');
        }

        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'admin') {
            session()->setFlashdata('error', 'Only administrators can create announcements.');
            return redirect()->to('/announcements');
        }

        // If POST request, process the form
        if ($this->request->getMethod() === 'POST') {
            $title = $this->request->getPost('title');
            $content = $this->request->getPost('content');

            // Validation
            if (empty($title) || empty($content)) {
                session()->setFlashdata('error', 'Title and content are required.');
                return redirect()->back()->withInput();
            }

            // Prepare announcement data with current timestamp
            $currentTime = date('Y-m-d H:i:s');
            
            $announcementData = [
                'title' => $title,
                'content' => $content,
                'posted_by' => session('userID'),
                'date_posted' => $currentTime,
                'created_at' => $currentTime,  // Capture real-time when posted
                'updated_at' => $currentTime
            ];

            // Insert into database
            if ($this->announcementModel->insert($announcementData)) {
                // Step 7: Create notifications for all students about new announcement
                try {
                    $this->notifyAllStudentsOfNewAnnouncement($title);
                } catch (\Exception $e) {
                    // Don't fail the announcement creation if notification creation fails
                    log_message('error', "Failed to create announcement notifications: " . $e->getMessage());
                }
                
                session()->setFlashdata('success', 'Announcement created successfully!');
                return redirect()->to('/announcements');
            } else {
                session()->setFlashdata('error', 'Failed to create announcement.');
                return redirect()->back()->withInput();
            }
        }

        // For GET request, show the create form
        $data = [
            'title' => 'Create Announcement - LMS System',
            'user' => [
                'name' => session('name'),
                'role' => session('role')
            ]
        ];

        return view('announcement_create', $data);
    }

    /**
     * Step 7: Notify all students about new announcement
     * 
     * @param string $announcementTitle
     * @return void
     */
    protected function notifyAllStudentsOfNewAnnouncement($announcementTitle)
    {
        // Get all students from the database
        $db = \Config\Database::connect();
        $students = $db->query("
            SELECT id 
            FROM users 
            WHERE role = 'student'
        ")->getResultArray();
        
        if (empty($students)) {
            log_message('info', "No students found to notify about announcement");
            return;
        }
        
        $notificationMessage = "New announcement posted: '{$announcementTitle}'. Check it out in the announcements section!";
        
        $notificationsCreated = 0;
        foreach ($students as $student) {
            try {
                $result = $this->notificationModel->createNotification($student['id'], $notificationMessage);
                if ($result) {
                    $notificationsCreated++;
                }
            } catch (\Exception $e) {
                log_message('error', "Failed to create announcement notification for user {$student['id']}: " . $e->getMessage());
            }
        }
        
        log_message('info', "Created {$notificationsCreated} announcement notifications");
    }
}

