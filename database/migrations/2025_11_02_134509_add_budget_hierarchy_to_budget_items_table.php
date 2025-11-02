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
        Schema::table('budget_items', function (Blueprint $table) {
            $table->foreignId('budget_group_id')->nullable()->after('proposal_id')->constrained()->nullOnDelete();
            $table->foreignId('budget_component_id')->nullable()->after('budget_group_id')->constrained()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budget_items', function (Blueprint $table) {
            $table->dropForeign(['budget_group_id']);
            $table->dropForeign(['budget_component_id']);
            $table->dropColumn(['budget_group_id', 'budget_component_id']);
        });
    }
};
