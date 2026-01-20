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
        Schema::table('proposal_outputs', function (Blueprint $table) {
            $table->text('description')->nullable()->after('target_status')->comment('Keterangan (URL)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposal_outputs', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
