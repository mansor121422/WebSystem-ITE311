<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Admin extends BaseController
{
    /**
     * Admin Dashboard
     * Task 3: Display admin-specific dashboard
     */
    public function dashboard()
    {
        // Check if user is logged in
        if (!session()->get('logged_in')) {
            session()->setFlashdata('error', 'You must be logged in to access the dashboard.');
            return redirect()->to('/login');
        }

        // Check if user is an admin
        $userRole = strtolower(session('role') ?? '');
        if ($userRole !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Administrators only.');
            return redirect()->to('/dashboard');
        }

        // Prepare data for view
        $data = [
            'title' => 'Admin Dashboard - LMS System',
            'user' => [
                'name' => session('name'),
                'email' => session('email'),
                'role' => session('role')
            ]
        ];

        // Load admin dashboard view
        return view('admin_dashboard', $data);
    }
}

