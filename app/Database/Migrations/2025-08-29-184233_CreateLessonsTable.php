<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLessonsTable extends Migration
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
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'content' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'comment' => 'Lesson content (HTML, Markdown, etc.)',
            ],
            'lesson_type' => [
                'type' => 'ENUM',
                'constraint' => ['video', 'text', 'quiz', 'assignment', 'document'],
                'default' => 'text',
            ],
            'video_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'duration' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Duration in minutes',
            ],
            'order_index' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Order of lesson within course',
            ],
            'is_published' => [
                'type' => 'BOOLEAN',
                'default' => false,
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
        $this->forge->addKey(['course_id', 'order_index']);
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('lessons');
    }

    public function down()
    {
        $this->forge->dropTable('lessons');
    }
}
