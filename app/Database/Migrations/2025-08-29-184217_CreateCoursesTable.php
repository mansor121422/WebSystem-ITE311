<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoursesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'instructor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'level' => [
                'type' => 'ENUM',
                'constraint' => ['beginner', 'intermediate', 'advanced'],
                'default' => 'beginner',
            ],
            'duration' => [
                'type' => 'INT',
                'constraint' => 11,
                'comment' => 'Duration in minutes',
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'thumbnail' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'published', 'archived'],
                'default' => 'draft',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('instructor_id');
        $this->forge->addKey('category');
        $this->forge->addKey('status');
        $this->forge->createTable('courses');
    }

    public function down()
    {
        $this->forge->dropTable('courses');
    }
}
