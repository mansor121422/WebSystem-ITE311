<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnrollments extends Migration
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
			'student_id' => [
				'type' => 'BIGINT',
				'constraint' => 20,
				'unsigned' => true,
			],
			'course_id' => [
				'type' => 'BIGINT',
				'constraint' => 20,
				'unsigned' => true,
			],
			'status' => [
				'type' => 'ENUM',
				'constraint' => ['active','completed','dropped'],
				'default' => 'active',
			],
		]);
		$this->forge->addField('enrolled_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
		$this->forge->addKey('id', true);
		$this->forge->addKey('student_id');
		$this->forge->addKey('course_id');
		$this->forge->addUniqueKey(['student_id','course_id']);
		$this->forge->createTable('enrollments', true, ['ENGINE' => 'InnoDB']);

		$this->db->query('ALTER TABLE `enrollments` 
			ADD CONSTRAINT `enrollments_student_id_fk`
			FOREIGN KEY (`student_id`) REFERENCES `users`(`id`)
			ON DELETE CASCADE ON UPDATE CASCADE');
		$this->db->query('ALTER TABLE `enrollments` 
			ADD CONSTRAINT `enrollments_course_id_fk`
			FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`)
			ON DELETE CASCADE ON UPDATE CASCADE');
	}

	public function down()
	{
		$this->forge->dropTable('enrollments', true);
	}
} 