<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeScheduleDayToSupportMultiple extends Migration
{
    public function up()
    {
        // Change schedule_day from ENUM to VARCHAR to support multiple days (comma-separated)
        $this->forge->modifyColumn('courses', [
            'schedule_day' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'comment' => 'Days of the week for class (comma-separated, e.g., "Monday,Wednesday,Friday")',
            ],
        ]);
    }

    public function down()
    {
        // Revert back to ENUM (single day)
        $this->forge->modifyColumn('courses', [
            'schedule_day' => [
                'type' => 'ENUM',
                'constraint' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                'null' => true,
                'comment' => 'Day of the week for class',
            ],
        ]);
    }
}

