<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AnnouncementModel;

class Announcement extends BaseController
{
    protected $announcementModel;

    public function __construct()
    {
        $this->announcementModel = new AnnouncementModel();
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

        // Fetch all announcements from database, ordered by date posted (newest first)
        $announcements = $this->announcementModel->orderBy('date_posted', 'DESC')->findAll();

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

            // Prepare announcement data
            $announcementData = [
                'title' => $title,
                'content' => $content,
                'posted_by' => session('userID'),
                'date_posted' => date('Y-m-d H:i:s')
            ];

            // Insert into database
            if ($this->announcementModel->insert($announcementData)) {
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
}

