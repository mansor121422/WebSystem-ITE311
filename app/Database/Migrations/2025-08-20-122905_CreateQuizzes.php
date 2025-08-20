<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuizzes extends Migration
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
			'lesson_id' => [
				'type' => 'BIGINT',
				'constraint' => 20,
				'unsigned' => true,
			],
			'question' => [
				'type' => 'TEXT',
			],
			'option_a' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
			],
			'option_b' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
			],
			'option_c' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
			],
			'option_d' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
			],
			'correct_option' => [
				'type' => 'ENUM',
				'constraint' => ['A','B','C','D'],
			],
			'points' => [
				'type' => 'SMALLINT',
				'constraint' => 5,
				'unsigned' => true,
				'default' => 1,
			],
		]);
		$this->forge->addField('created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
		$this->forge->addField('updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
		$this->forge->addKey('id', true);
		$this->forge->addKey('lesson_id');
		$this->forge->createTable('quizzes', true, ['ENGINE' => 'InnoDB']);

		$this->db->query('ALTER TABLE `quizzes` 
			ADD CONSTRAINT `quizzes_lesson_id_fk`
			FOREIGN KEY (`lesson_id`) REFERENCES `lessons`(`id`)
			ON DELETE CASCADE ON UPDATE CASCADE');
	}

	public function down()
	{
		$this->forge->dropTable('quizzes', true);
	}
} 