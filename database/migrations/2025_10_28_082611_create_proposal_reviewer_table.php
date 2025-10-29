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
        Schema::create('proposal_reviewer', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('proposal_id')->constrained('proposals')->onDelete('cascade')->comment('Proposal');
            $table->foreignUuid('user_id')->constrained('users')->onDelete('cascade')->comment('Reviewer');
            $table->enum('status', ['pending', 'reviewing', 'completed'])->default('pending')->comment('Status Review');
            $table->text('review_notes')->nullable()->comment('Catatan Review');
            $table->enum('recommendation', ['approved', 'rejected', 'revision_needed'])->nullable()->comment('Rekomendasi Reviewer');
            $table->timestamps();

            $table->unique(['proposal_id', 'user_id']);
            $table->index(['proposal_id']);
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_reviewer');
    }
};
