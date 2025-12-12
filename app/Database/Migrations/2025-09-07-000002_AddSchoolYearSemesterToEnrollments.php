<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSchoolYearSemesterToEnrollments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('enrollments', [
            'school_year' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'comment' => 'School year for this enrollment',
            ],
            'semester' => [
                'type' => 'ENUM',
                'constraint' => ['1st', '2nd', 'Summer'],
                'null' => true,
                'comment' => 'Semester for this enrollment',
            ],
            'requested_by' => [
                'type' => 'ENUM',
                'constraint' => ['student', 'teacher', 'admin'],
                'default' => 'student',
                'comment' => 'Who initiated the enrollment request',
            ],
            'approved_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'User ID who approved the enrollment',
            ],
            'approved_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'When the enrollment was approved',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', [
            'school_year',
            'semester',
            'requested_by',
            'approved_by',
            'approved_at'
        ]);
    }
}

