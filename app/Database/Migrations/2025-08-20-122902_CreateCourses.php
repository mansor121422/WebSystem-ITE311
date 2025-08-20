<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourses extends Migration
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
			'instructor_id' => [
				'type' => 'BIGINT',
				'constraint' => 20,
				'unsigned' => true,
				'null' => true,
			],
			'title' => [
				'type' => 'VARCHAR',
				'constraint' => 200,
			],
			'description' => [
				'type' => 'TEXT',
				'null' => true,
			],
		]);
		$this->forge->addField('created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
		$this->forge->addField('updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
		$this->forge->addKey('id', true);
		$this->forge->addKey('instructor_id');
		$this->forge->createTable('courses', true, ['ENGINE' => 'InnoDB']);

		$this->db->query('ALTER TABLE `courses` 
			ADD CONSTRAINT `courses_instructor_id_fk`
			FOREIGN KEY (`instructor_id`) REFERENCES `users`(`id`)
			ON DELETE SET NULL ON UPDATE CASCADE');
	}

	public function down()
	{
		$this->forge->dropTable('courses', true);
	}
} 