<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubmissions extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'BIGINT',
				'constraint' => 20,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'quiz_id' => [
				'type' => 'BIGINT',
				'constraint' => 20,
				'unsigned' => true,
			],
			'student_id' => [
				'type' => 'BIGINT',
				'constraint' => 20,
				'unsigned' => true,
			],
			'selected_option' => [
				'type' => 'ENUM',
				'constraint' => ['A','B','C','D'],
			],
			'is_correct' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'null' => false,
				'default' => 0,
			],
			'score' => [
				'type' => 'SMALLINT',
				'constraint' => 5,
				'unsigned' => true,
				'default' => 0,
			],
		]);
		$this->forge->addField('submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
		$this->forge->addKey('id', true);
		$this->forge->addKey('quiz_id');
		$this->forge->addKey('student_id');
		$this->forge->addUniqueKey(['student_id','quiz_id']);
		$this->forge->createTable('submissions', true, ['ENGINE' => 'InnoDB']);

		$this->db->query('ALTER TABLE `submissions` 
			ADD CONSTRAINT `submissions_quiz_id_fk`
			FOREIGN KEY (`quiz_id`) REFERENCES `quizzes`(`id`)
			ON DELETE CASCADE ON UPDATE CASCADE');
		$this->db->query('ALTER TABLE `submissions` 
			ADD CONSTRAINT `submissions_student_id_fk`
			FOREIGN KEY (`student_id`) REFERENCES `users`(`id`)
			ON DELETE CASCADE ON UPDATE CASCADE');
	}

	public function down()
	{
		$this->forge->dropTable('submissions', true);
	}
} 