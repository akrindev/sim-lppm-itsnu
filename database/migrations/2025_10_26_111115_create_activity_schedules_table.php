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
        Schema::create('activity_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposals')->onDelete('cascade')->comment('Proposal');
            $table->string('activity_name')->comment('Nama Kegiatan');
            $table->integer('year')->comment('Tahun ke-');
            $table->integer('start_month')->comment('Bulan Mulai (1-12)');
            $table->integer('end_month')->comment('Bulan Selesai (1-12)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_schedules');
    }
};
