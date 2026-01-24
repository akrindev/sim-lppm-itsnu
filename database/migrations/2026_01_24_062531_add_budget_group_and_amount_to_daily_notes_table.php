<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_notes', function (Blueprint $table) {
            $table->foreignId('budget_group_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_notes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('budget_group_id');
            $table->dropColumn('amount');
        });
    }
};
