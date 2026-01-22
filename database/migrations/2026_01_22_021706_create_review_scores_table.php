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
        Schema::create('review_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proposal_reviewer_id')->constrained('proposal_reviewer')->onDelete('cascade');
            $table->foreignId('review_criteria_id')->constrained('review_criterias')->onDelete('cascade');
            $table->text('acuan'); // Manual input from reviewer
            $table->integer('score'); // 1-5
            $table->integer('weight_snapshot');
            $table->integer('value'); // Calculated: score * weight_snapshot
            $table->integer('round')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('review_scores');
    }
};
