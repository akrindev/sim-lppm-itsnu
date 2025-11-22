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
        Schema::create('progress_reports', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('proposal_id')->constrained()->onDelete('cascade')->comment('Proposal');
            $table->text('summary_update')->nullable()->comment('Updated summary');
            $table->integer('reporting_year')->comment('Tahun pelaporan');
            $table->enum('reporting_period', ['semester_1', 'semester_2', 'annual', 'final'])->comment('Periode pelaporan');
            $table->enum('status', ['draft', 'submitted', 'approved'])->default('draft')->comment('Status laporan');
            $table->foreignUuid('submitted_by')->nullable()->constrained('users')->comment('User who submitted');
            $table->timestamp('submitted_at')->nullable()->comment('Submission timestamp');
            $table->timestamps();

            $table->index(['proposal_id', 'reporting_year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress_reports');
    }
};
