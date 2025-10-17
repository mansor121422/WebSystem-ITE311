<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Teacher extends BaseController
{
    /**
     * Teacher Dashboard
     * Task 3: Display teacher-specific dashboard
     */
    public function dashboard()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            session()->setFlashdata('error', 'You must be logged in to access the dashboard.');
            return redirect()->to('/login');
        }

        // Check if user is a teacher
        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'teacher') {
            session()->setFlashdata('error', 'Access denied. Teachers only.');
            return redirect()->to('/dashboard');
        }

        // Prepare data for view
        $data = [
            'title' => 'Teacher Dashboard - LMS System',
            'user' => [
                'name' => session('name'),
                'email' => session('email'),
                'role' => session('role')
            ]
        ];

        // Load teacher dashboard view
        return view('teacher_dashboard', $data);
    }
}

