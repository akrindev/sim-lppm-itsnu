<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Using raw SQL to ensure the column type and nullability are correctly updated
        // as Doctrine DBAL often struggles with modifying ENUMs to Strings or changing nullability of ENUMs.
        
        DB::statement("ALTER TABLE mandatory_outputs MODIFY COLUMN status_type VARCHAR(255) NULL");
        DB::statement("ALTER TABLE mandatory_outputs MODIFY COLUMN author_status VARCHAR(255) NULL");
        
        DB::statement("ALTER TABLE additional_outputs MODIFY COLUMN status VARCHAR(255) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting is complex because we don't know if data violates the enum constraints.
        // We will leave it as is for this hotfix.
    }
};
