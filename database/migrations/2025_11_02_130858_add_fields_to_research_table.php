<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('research', function (Blueprint $table) {
            $table->foreignId('macro_research_group_id')->nullable()
                ->after('id')
                ->constrained('macro_research_groups')
                ->nullOnDelete();
            $table->string('substance_file')->nullable()->after('methodology');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('research', function (Blueprint $table) {
            $table->dropForeign(['macro_research_group_id']);
            $table->dropColumn(['macro_research_group_id', 'substance_file']);
        });
    }
};
