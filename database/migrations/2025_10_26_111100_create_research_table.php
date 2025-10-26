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
        Schema::create('research', function (Blueprint $table) {
            $table->id();
            $table->integer('final_tkt_target')->nullable()->comment('Target TKT Akhir');
            $table->longText('background')->nullable()->comment('Latar Belakang');
            $table->longText('state_of_the_art')->nullable()->comment('State of the Art');
            $table->longText('methodology')->nullable()->comment('Metodologi');
            $table->json('roadmap_data')->nullable()->comment('Data Roadmap');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research');
    }
};
