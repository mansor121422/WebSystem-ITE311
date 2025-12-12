<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnrollmentFieldsToCourses extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'course_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'comment' => 'Course code (e.g., CS101, MATH201)',
            ],
            'school_year' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'comment' => 'School year (e.g., 2024-2025)',
            ],
            'semester' => [
                'type' => 'ENUM',
                'constraint' => ['1st', '2nd', 'Summer'],
                'null' => true,
                'comment' => 'Semester period',
            ],
            'schedule_day' => [
                'type' => 'ENUM',
                'constraint' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                'null' => true,
                'comment' => 'Day of the week for class',
            ],
            'schedule_time_start' => [
                'type' => 'TIME',
                'null' => true,
                'comment' => 'Class start time',
            ],
            'schedule_time_end' => [
                'type' => 'TIME',
                'null' => true,
                'comment' => 'Class end time',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', [
            'course_code',
            'school_year',
            'semester',
            'schedule_day',
            'schedule_time_start',
            'schedule_time_end'
        ]);
    }
}

