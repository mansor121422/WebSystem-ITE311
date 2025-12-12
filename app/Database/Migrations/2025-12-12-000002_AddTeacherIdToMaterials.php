<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTeacherIdToMaterials extends Migration
{
    public function up()
    {
        $this->forge->addColumn('materials', [
            'teacher_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'ID of the teacher who uploaded this material',
                'after' => 'course_id',
            ],
        ]);

        // Add foreign key constraint
        $this->forge->addForeignKey('teacher_id', 'users', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        // Drop foreign key first
        $this->forge->dropForeignKey('materials', 'materials_teacher_id_foreign');
        
        // Drop the column
        $this->forge->dropColumn('materials', 'teacher_id');
    }
}

