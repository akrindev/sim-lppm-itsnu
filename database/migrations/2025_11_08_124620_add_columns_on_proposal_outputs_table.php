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
            $table->string('group')->after('category')->nullable()->comment('Group output, e.g., buku, artikel, dll.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposal_outputs', function (Blueprint $table) {
            $table->dropColumn('group');
        });
    }
};
