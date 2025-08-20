<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLessons extends Migration
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
			'course_id' => [
				'type' => 'BIGINT',
				'constraint' => 20,
				'unsigned' => true,
			],
			'title' => [
				'type' => 'VARCHAR',
				'constraint' => 200,
			],
			'content' => [
				'type' => 'LONGTEXT',
				'null' => true,
			],
			'position' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'default' => 1,
			],
		]);
		$this->forge->addField('created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
		$this->forge->addField('updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
		$this->forge->addKey('id', true);
		$this->forge->addKey('course_id');
		$this->forge->addKey(['course_id','position']);
		$this->forge->createTable('lessons', true, ['ENGINE' => 'InnoDB']);

		$this->db->query('ALTER TABLE `lessons` 
			ADD CONSTRAINT `lessons_course_id_fk`
			FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`)
			ON DELETE CASCADE ON UPDATE CASCADE');
	}

	public function down()
	{
		$this->forge->dropTable('lessons', true);
	}
} 