<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsers extends Migration
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
			'name' => [
				'type' => 'VARCHAR',
				'constraint' => 150,
			],
			'email' => [
				'type' => 'VARCHAR',
				'constraint' => 191,
			],
			'password_hash' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
			],
			'role' => [
				'type' => 'ENUM',
				'constraint' => ['student','instructor','admin'],
				'default' => 'student',
			],
		]);
		$this->forge->addField('created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
		$this->forge->addField('updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP');
		$this->forge->addKey('id', true);
		$this->forge->addUniqueKey('email');
		$this->forge->addKey('role');
		$this->forge->createTable('users', true, ['ENGINE' => 'InnoDB']);
	}

	public function down()
	{
		$this->forge->dropTable('users', true);
	}
} 