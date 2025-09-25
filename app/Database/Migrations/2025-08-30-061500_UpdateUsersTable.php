<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUsersTable extends Migration
{
    public function up()
    {
        // Add name column if it doesn't exist
        if (!$this->db->fieldExists('name', 'users')) {
            $this->forge->addColumn('users', [
                'name' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'after' => 'id'
                ]
            ]);
        }
        
        // Update existing records to have a name
        $this->db->query("UPDATE users SET name = CONCAT(first_name, ' ', last_name) WHERE name IS NULL AND first_name IS NOT NULL AND last_name IS NOT NULL");
        
        // Update existing records without first_name/last_name to have a default name
        $this->db->query("UPDATE users SET name = 'User' WHERE name IS NULL");
        
        // Make name field NOT NULL
        $this->forge->modifyColumn('users', [
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false
            ]
        ]);
    }

    public function down()
    {
        // Remove name column
        $this->forge->dropColumn('users', 'name');
    }
}
