<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubmissionsTable extends Migration
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
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'quiz_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'submission_data' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'comment' => 'JSON data containing answers and responses',
            ],
            'score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'comment' => 'Final score percentage',
            ],
            'total_questions' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'correct_answers' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'time_taken' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'comment' => 'Time taken in seconds',
            ],
            'attempt_number' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
                'comment' => 'Attempt number for this quiz',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['in_progress', 'completed', 'abandoned'],
                'default' => 'in_progress',
            ],
            'started_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
                'null' => true,
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
        $this->forge->addKey(['user_id', 'quiz_id', 'attempt_number']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('quiz_id', 'quizzes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('submissions');
    }

    public function down()
    {
        $this->forge->dropTable('submissions');
    }
}
