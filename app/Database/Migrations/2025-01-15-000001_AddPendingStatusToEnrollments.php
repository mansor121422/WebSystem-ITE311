<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPendingStatusToEnrollments extends Migration
{
    public function up()
    {
        // Modify the status ENUM to include 'pending' and 'rejected'
        $this->db->query("ALTER TABLE enrollments MODIFY COLUMN status ENUM('pending', 'active', 'completed', 'dropped', 'suspended', 'rejected') DEFAULT 'pending'");
    }

    public function down()
    {
        // Revert back to original status values
        $this->db->query("ALTER TABLE enrollments MODIFY COLUMN status ENUM('active', 'completed', 'dropped', 'suspended') DEFAULT 'active'");
    }
}

