<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds percentage field to budget_groups table to define allocation limits.
     * Each budget group can have a percentage (0-100) representing the maximum
     * proportion of total proposal budget that can be allocated to this group.
     */
    public function up(): void
    {
        Schema::table('budget_groups', function (Blueprint $table) {
            $table->decimal('percentage', 5, 2)
                ->nullable()
                ->after('description')
                ->comment('Persentase alokasi anggaran (0.00-100.00)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budget_groups', function (Blueprint $table) {
            $table->dropColumn('percentage');
        });
    }
};
