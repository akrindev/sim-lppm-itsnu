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
        Schema::table('progress_reports', function (Blueprint $table) {
            $table->enum('reporting_period', ['semester_1', 'semester_2', 'annual', 'final'])
                ->comment('Periode pelaporan')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress_reports', function (Blueprint $table) {
            $table->enum('reporting_period', ['semester_1', 'semester_2', 'annual'])
                ->comment('Periode pelaporan')
                ->change();
        });
    }
};
