<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds description fields to research_schemes and national_priorities tables
     * Adds 'PKM' value to research_schemes.strata enum
     */
    public function up(): void
    {
        // Add description field to research_schemes
        Schema::table('research_schemes', function (Blueprint $table) {
            $table->text('description')->nullable()->after('strata')
                ->comment('Deskripsi skema penelitian/pengabdian');
        });

        // Add description field to national_priorities
        Schema::table('national_priorities', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name')
                ->comment('Deskripsi Prioritas Riset Nasional');
        });

        // Modify strata enum to include PKM
        // Note: Laravel doesn't support modifying enums directly, so we use raw SQL
        DB::statement("ALTER TABLE research_schemes MODIFY COLUMN strata ENUM('Dasar', 'Terapan', 'Pengembangan', 'PKM') COMMENT 'Strata Penelitian/PKM'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove description fields
        Schema::table('research_schemes', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('national_priorities', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        // Revert strata enum back to original
        DB::statement("ALTER TABLE research_schemes MODIFY COLUMN strata ENUM('Dasar', 'Terapan', 'Pengembangan') COMMENT 'Strata Penelitian'");
    }
};
