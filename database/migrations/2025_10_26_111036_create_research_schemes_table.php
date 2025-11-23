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
        Schema::create('research_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama Skema Penelitian');
            $table->enum('strata', ['Dasar', 'Terapan', 'Pengembangan', 'PKM'])->comment('Strata Penelitian/PKM');
            $table->text('description')->nullable()->comment('Deskripsi skema penelitian/pengabdian');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_schemes');
    }
};
