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
        Schema::create('partners', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->comment('Nama Mitra');
            $table->string('email')->nullable();
            $table->string('institution')->nullable();
            $table->string('country')->nullable();
            $table->string('type')->comment('Tipe Mitra');
            $table->text('address')->nullable()->comment('Alamat Mitra');
            $table->string('commitment_letter_file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
