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
        Schema::create('proposal_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposals')->onDelete('cascade')->comment('Proposal');
            $table->integer('output_year')->comment('Tahun Luaran');
            $table->string('category')->comment('Kategori Luaran (Wajib/Tambahan)');
            $table->string('type')->comment('Jenis Luaran (Jurnal/Paten/dll)');
            $table->string('target_status')->comment('Status Target (Q1/Q2/Granted/dll)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_outputs');
    }
};
