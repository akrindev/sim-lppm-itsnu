<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates budget_caps table to store year-based budget limits for
     * research and community service proposals. Each year can have different
     * budget caps for both proposal types.
     */
    public function up(): void
    {
        Schema::create('budget_caps', function (Blueprint $table) {
            $table->id();
            $table->year('year')->unique()->comment('Tahun anggaran');
            $table->decimal('research_budget_cap', 15, 2)
                ->nullable()
                ->comment('Batas maksimal anggaran penelitian (IDR)');
            $table->decimal('community_service_budget_cap', 15, 2)
                ->nullable()
                ->comment('Batas maksimal anggaran pengabdian (IDR)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_caps');
    }
};
