<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Admin Users
            [
                'username' => 'admin',
                'email' => 'admin@lms.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'role' => 'admin',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'superadmin',
                'email' => 'superadmin@lms.com',
                'password' => password_hash('super123', PASSWORD_DEFAULT),
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'role' => 'admin',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Instructor Users
            [
                'username' => 'instructor1',
                'email' => 'john.doe@lms.com',
                'password' => password_hash('instructor123', PASSWORD_DEFAULT),
                'first_name' => 'John',
                'last_name' => 'Doe',
                'role' => 'instructor',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'instructor2',
                'email' => 'jane.smith@lms.com',
                'password' => password_hash('instructor123', PASSWORD_DEFAULT),
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'role' => 'instructor',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'instructor3',
                'email' => 'mike.wilson@lms.com',
                'password' => password_hash('instructor123', PASSWORD_DEFAULT),
                'first_name' => 'Mike',
                'last_name' => 'Wilson',
                'role' => 'instructor',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Student Users
            [
                'username' => 'student1',
                'email' => 'alice.johnson@student.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'Alice',
                'last_name' => 'Johnsonn',
                'role' => 'student',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'student2',
                'email' => 'bob.brown@student.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'Bob',
                'last_name' => 'Brown',
                'role' => 'student',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'student3',
                'email' => 'carol.davis@student.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'Carol',
                'last_name' => 'Davis',
                'role' => 'student',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'student4',
                'email' => 'david.miller@student.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'David',
                'last_name' => 'Miller',
                'role' => 'student',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'student5',
                'email' => 'emma.wilson@student.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'Emma',
                'last_name' => 'Wilson',
                'role' => 'student',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'student6',
                'email' => 'frank.garcia@student.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'Frank',
                'last_name' => 'Garcia',
                'role' => 'student',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'student7',
                'email' => 'grace.lee@student.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'Grace',
                'last_name' => 'Lee',
                'role' => 'student',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'student8',
                'email' => 'henry.taylor@student.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'Henry',
                'last_name' => 'Taylor',
                'role' => 'student',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert all users
        $this->db->table('users')->insertBatch($data);
        
        echo "Sample users created successfully!\n";
    }
}
