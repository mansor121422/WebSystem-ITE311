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
                'email' => 'admin@gmail.com',
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
                'email' => 'superadmin@gmail.com',
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
                'email' => 'BoyetAdlawan@gmail.com',
                'password' => password_hash('instructor123', PASSWORD_DEFAULT),
                'first_name' => 'Boyet',
                'last_name' => 'Adlawan',
                'role' => 'instructor',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'instructor2',
                'email' => 'jade.smith@lms.com',
                'password' => password_hash('instructor123', PASSWORD_DEFAULT),
                'first_name' => 'Jade',
                'last_name' => 'Smith',
                'role' => 'instructor',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'instructor3',
                'email' => 'mike.tyson@lms.com',
                'password' => password_hash('instructor123', PASSWORD_DEFAULT),
                'first_name' => 'Mike',
                'last_name' => 'Tyson',
                'role' => 'instructor',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],

            // Student Users
            [
                'username' => 'student1',
                'email' => 'alice.go@student.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'Alice',
                'last_name' => 'Go',
                'role' => 'student',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'student2',
                'email' => 'bobi.brown@student.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'Bobi',
                'last_name' => 'Brown',
                'role' => 'student',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'student3',
                'email' => 'antony.davis@student.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'antony',
                'last_name' => 'Davis',
                'role' => 'student',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'student4',
                'email' => 'david.smith@student.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'David',
                'last_name' => 'smith',
                'role' => 'student',
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'username' => 'student5',
                'email' => 'emma.brown@student.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'first_name' => 'Emma',
                'last_name' => 'brown',
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
