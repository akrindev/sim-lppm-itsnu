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
        Schema::table('proposal_user', function (Blueprint $table) {
            // Add status column: 'accepted' for ketua (submitter), 'pending' for anggota
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending')->after('role')->comment('Status Persetujuan Anggota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposal_user', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
