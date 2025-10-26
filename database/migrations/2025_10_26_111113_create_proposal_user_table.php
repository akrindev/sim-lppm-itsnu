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
        Schema::create('proposal_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_id')->constrained('proposals')->onDelete('cascade')->comment('Proposal');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Anggota Tim');
            $table->enum('role', ['ketua', 'anggota'])->default('anggota')->comment('Peran dalam Tim');
            $table->text('tasks')->nullable()->comment('Bidang Tugas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_user');
    }
};
