<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Update status enum to match BIMA 2025/2026 status values:
     * - draft: Masih dalam penyusunan
     * - submitted: Sudah disubmit ke jurnal
     * - under_review: Sedang direview
     * - accepted: Diterima
     * - published: Sudah terbit
     * - rejected: Ditolak
     */
    public function up(): void
    {
        // For MariaDB/MySQL, we need to use raw SQL to modify enum
        DB::statement("ALTER TABLE additional_outputs MODIFY COLUMN status ENUM('draft', 'submitted', 'under_review', 'accepted', 'published', 'rejected') NULL COMMENT 'Publication status (BIMA 2025/2026)'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE additional_outputs MODIFY COLUMN status ENUM('review', 'editing', 'published') NOT NULL COMMENT 'Publication status'");
    }
};
