<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add waiting_reviewer status to the enum
        DB::statement("ALTER TABLE proposals MODIFY COLUMN status ENUM('draft','submitted','need_assignment','approved','waiting_reviewer','under_review','reviewed','revision_needed','completed','rejected') NOT NULL DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, update any proposals with waiting_reviewer status back to approved
        DB::table('proposals')
            ->where('status', 'waiting_reviewer')
            ->update(['status' => 'approved']);

        // Remove waiting_reviewer from the enum
        DB::statement("ALTER TABLE proposals MODIFY COLUMN status ENUM('draft','submitted','need_assignment','approved','under_review','reviewed','revision_needed','completed','rejected') NOT NULL DEFAULT 'draft'");
    }
};
