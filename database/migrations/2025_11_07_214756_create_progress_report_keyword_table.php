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
        Schema::create('progress_report_keyword', function (Blueprint $table) {
            $table->foreignUuid('progress_report_id')->constrained()->onDelete('cascade')->comment('Progress Report');
            $table->foreignId('keyword_id')->constrained()->onDelete('cascade')->comment('Keyword');
            $table->timestamps();

            $table->primary(['progress_report_id', 'keyword_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_report_keyword');
    }
};
