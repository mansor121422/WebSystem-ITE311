<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateExistingEnrollmentsRequestedBy extends Migration
{
    public function up()
    {
        // Update pending enrollments that don't have requested_by set
        // Set them to 'student' by default (most likely scenario)
        $this->db->query("
            UPDATE enrollments 
            SET requested_by = 'student' 
            WHERE status = 'pending' 
            AND (requested_by IS NULL OR requested_by = '')
        ");
    }

    public function down()
    {
        // This migration doesn't need a down method as it's a data update
        // We can't reliably reverse this without knowing the original values
    }
}

