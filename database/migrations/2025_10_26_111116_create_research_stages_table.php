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
        Schema::create('research_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('proposal_id')->constrained('proposals')->onDelete('cascade')->comment('Proposal');
            $table->integer('stage_number')->comment('Nomor Tahap');
            $table->string('process_name')->comment('Nama Proses');
            $table->string('outputs')->comment('Output');
            $table->string('indicator')->comment('Indikator');
            $table->foreignUuid('person_in_charge_id')->nullable()->constrained('users')->onDelete('set null')->comment('Penanggung Jawab');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_stages');
    }
};
