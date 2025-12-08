<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSemesterEndDateToEnrollments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('enrollments', [
            'semester_end_date' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Semester expiration date (4 months from enrollment)',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('enrollments', 'semester_end_date');
    }
}

