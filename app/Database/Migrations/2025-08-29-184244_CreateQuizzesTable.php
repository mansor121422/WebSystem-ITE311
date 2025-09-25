<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuizzesTable extends Migration
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
            'lesson_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Optional: quiz can be part of a specific lesson',
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'instructions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'time_limit' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Time limit in minutes (null = no limit)',
            ],
            'max_attempts' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
                'comment' => 'Maximum number of attempts allowed',
            ],
            'passing_score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 70.00,
                'comment' => 'Passing score percentage',
            ],
            'is_published' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'show_correct_answers' => [
                'type' => 'BOOLEAN',
                'default' => true,
                'comment' => 'Whether to show correct answers after submission',
            ],
            'randomize_questions' => [
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
        $this->forge->addKey(['course_id', 'lesson_id']);
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('lesson_id', 'lessons', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('quizzes');
    }

    public function down()
    {
        $this->forge->dropTable('quizzes');
    }
}
