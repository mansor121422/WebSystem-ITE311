<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
	public function run()
	{
		$users = [
			[
				'name' => 'Admin User',
				'email' => 'admin@example.com',
				'password_hash' => password_hash('admin123', PASSWORD_DEFAULT),
				'role' => 'admin',
			],
			[
				'name' => 'Alice Instructor',
				'email' => 'alice.instructor@example.com',
				'password_hash' => password_hash('instructor123', PASSWORD_DEFAULT),
				'role' => 'instructor',
			],
			[
				'name' => 'Bob Instructor',
				'email' => 'bob.instructor@example.com',
				'password_hash' => password_hash('instructor123', PASSWORD_DEFAULT),
				'role' => 'instructor',
			],
			[
				'name' => 'Sam Student',
				'email' => 'sam.student@example.com',
				'password_hash' => password_hash('student123', PASSWORD_DEFAULT),
				'role' => 'student',
			],
			[
				'name' => 'Jane Student',
				'email' => 'jane.student@example.com',
				'password_hash' => password_hash('student123', PASSWORD_DEFAULT),
				'role' => 'student',
			],
			[
				'name' => 'Mike Student',
				'email' => 'mike.student@example.com',
				'password_hash' => password_hash('student123', PASSWORD_DEFAULT),
				'role' => 'student',
			],
		];

		$this->db->table('users')->insertBatch($users);
	}
}
