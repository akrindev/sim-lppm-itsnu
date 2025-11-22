<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('identities', function (Blueprint $table) {
            $table->id();
            $table->string('identity_id')->unique()->comment('NIDN / NIM');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade')->comment('User');
            $table->string('sinta_id')->nullable()->comment('ID SINTA');
            $table->enum('type', ['dosen', 'mahasiswa'])->comment('Tipe User');
            $table->string('address')->nullable()->comment('Alamat');
            $table->date('birthdate')->nullable()->comment('Tanggal Lahir');
            $table->string('birthplace')->nullable()->comment('Tempat Lahir');
            $table->foreignId('institution_id')->nullable()->constrained('institutions')->onDelete('set null')->comment('Institusi');
            $table->foreignId('study_program_id')->nullable()->constrained('study_programs')->onDelete('set null')->comment('Program Studi');
            $table->string('profile_picture')->nullable()->comment('Foto Profil');
            $table->foreignId('faculty_id')->nullable()->constrained('faculties')->onDelete('set null')->comment('Fakultas');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('identities');
    }
};
