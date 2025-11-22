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
        Schema::create('community_services', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('partner_id')->nullable()->comment('Mitra');
            $table->text('partner_issue_summary')->nullable()->comment('Ringkasan Masalah Mitra');
            $table->text('solution_offered')->nullable()->comment('Solusi yang Ditawarkan');
            $table->timestamps();

            $table->foreign('partner_id')->references('id')->on('partners')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_services');
    }
};
