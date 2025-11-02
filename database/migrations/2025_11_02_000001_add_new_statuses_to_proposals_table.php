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
        Schema::table('proposals', function (Blueprint $table) {
            // Update enum column to include new statuses
            $table->enum('status', [
                'draft',
                'submitted',
                'need_assignment',
                'approved',
                'under_review',
                'reviewed',
                'revision_needed',
                'completed',
                'rejected',
            ])->default('draft')->comment('Status Proposal')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proposals', function (Blueprint $table) {
            // Revert to original enum values
            $table->enum('status', [
                'draft',
                'submitted',
                'reviewed',
                'approved',
                'rejected',
                'completed',
            ])->default('draft')->comment('Status Proposal')->change();
        });
    }
};
