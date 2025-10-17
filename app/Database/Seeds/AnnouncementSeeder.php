<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run()
    {
        // Get the first admin user ID (or create sample data with user_id = 1)
        $db = \Config\Database::connect();
        $adminQuery = $db->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
        $admin = $adminQuery->getRow();
        $adminId = $admin ? $admin->id : 1;

        // Sample announcements data
        $data = [
            [
                'title' => 'Welcome to the LMS System!',
                'content' => 'We are excited to announce the launch of our new Learning Management System. This platform will help streamline your learning experience and provide easy access to course materials, assignments, and grades.',
                'posted_by' => $adminId,
                'date_posted' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ],
            [
                'title' => 'New Course Offerings for Fall 2025',
                'content' => 'We are pleased to announce new course offerings for the Fall 2025 semester. These include Advanced Web Development, Machine Learning Fundamentals, and Mobile App Development. Registration is now open!',
                'posted_by' => $adminId,
                'date_posted' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'title' => 'System Maintenance Scheduled',
                'content' => 'Please be advised that routine system maintenance will be performed this Saturday from 2:00 AM to 6:00 AM. The LMS will be temporarily unavailable during this time. We apologize for any inconvenience.',
                'posted_by' => $adminId,
                'date_posted' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            [
                'title' => 'Important: Assignment Deadline Extension',
                'content' => 'Due to technical difficulties experienced by some students, the deadline for all assignments due this week has been extended by 48 hours. Please check your course pages for updated due dates.',
                'posted_by' => $adminId,
                'date_posted' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert sample announcements
        $this->db->table('announcements')->insertBatch($data);
        
        echo "Successfully seeded " . count($data) . " announcements.\n";
    }
}

