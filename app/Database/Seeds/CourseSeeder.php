<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        $courses = [
            [
                'id' => 1,
                'title'       => 'Introduction to Programming',
                'description' => 'Learn the fundamentals of programming with Python',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'title'       => 'Web Development Basics',
                'description' => 'HTML, CSS, and JavaScript fundamentals',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'title'       => 'Database Management',
                'description' => 'SQL and database design principles',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'title'       => 'Data Structures & Algorithms',
                'description' => 'Advanced programming concepts and problem solving',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Clear existing courses first (handle foreign key constraints)
        $this->db->table('enrollments')->where('id >', 0)->delete();
        $this->db->table('courses')->where('id >', 0)->delete();

        // Reset auto increment to ensure IDs start from 1
        $this->db->query('ALTER TABLE courses AUTO_INCREMENT = 1');

        // Insert sample courses individually to preserve IDs
        foreach ($courses as $course) {
            $this->db->table('courses')->insert($course);
        }
        
        echo "Inserted " . count($courses) . " courses successfully.\n";
    }
}