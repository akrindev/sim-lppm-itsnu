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
            $table->foreignId('proposal_id')->constrained('proposals')->onDelete('cascade')->comment('Proposal');
            $table->integer('stage_number')->comment('No. Tahapan');
            $table->string('process_name')->comment('Nama Proses');
            $table->text('outputs')->nullable()->comment('Luaran Tahapan');
            $table->string('indicator')->nullable()->comment('Indikator Keberhasilan');
            $table->foreignId('person_in_charge_id')->nullable()->constrained('users')->onDelete('set null')->comment('Penanggung Jawab');
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
