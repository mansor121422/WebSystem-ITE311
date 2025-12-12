<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuizQuestionsTable extends Migration
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
            'quiz_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'question_text' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'question_type' => [
                'type' => 'ENUM',
                'constraint' => ['multiple_choice', 'true_false', 'short_answer'],
                'default' => 'multiple_choice',
            ],
            'options' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of options for multiple choice questions',
            ],
            'correct_answer' => [
                'type' => 'TEXT',
                'null' => false,
                'comment' => 'Correct answer (can be option index, true/false, or text)',
            ],
            'points' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 1.00,
                'comment' => 'Points for this question',
            ],
            'order_index' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'comment' => 'Order of question in quiz',
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
        $this->forge->addKey('quiz_id');
        $this->forge->addForeignKey('quiz_id', 'quizzes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('quiz_questions');
    }

    public function down()
    {
        $this->forge->dropTable('quiz_questions');
    }
}


