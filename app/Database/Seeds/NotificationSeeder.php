<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        // Get some existing user IDs from the database
        $db = \Config\Database::connect();
        $users = $db->query("SELECT id FROM users LIMIT 5")->getResultArray();
        
        if (empty($users)) {
            echo "No users found. Please run UserSeeder first.\n";
            return;
        }

        $notifications = [];
        $messages = [
            'Welcome to the Learning Management System!',
            'You have been enrolled in a new course: Introduction to Programming',
            'New assignment has been posted in your Mathematics course',
            'Your quiz submission has been graded',
            'Course materials have been updated for Web Development',
            'Reminder: Assignment deadline is tomorrow',
            'New announcement posted by your instructor',
            'Your course enrollment has been confirmed'
        ];

        foreach ($users as $user) {
            // Create 2-3 notifications per user
            $notificationCount = rand(2, 3);
            
            for ($i = 0; $i < $notificationCount; $i++) {
                $notifications[] = [
                    'user_id' => $user['id'],
                    'message' => $messages[array_rand($messages)],
                    'is_read' => rand(0, 1), // Random read status
                    'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days'))
                ];
            }
        }

        // Insert notifications
        $this->db->table('notifications')->insertBatch($notifications);
        
        echo "Inserted " . count($notifications) . " test notifications.\n";
    }
}
