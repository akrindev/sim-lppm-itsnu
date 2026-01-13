<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, update any existing 'reviewing' status to 'in_progress'
        DB::table('proposal_reviewer')
            ->where('status', 'reviewing')
            ->update(['status' => 'pending']); // Temporarily set to pending to avoid truncation

        Schema::table('proposal_reviewer', function (Blueprint $table) {
            // Add round tracking for revision cycles
            $table->unsignedInteger('round')->default(1)->after('recommendation')
                ->comment('Review round/cycle number');

            // Add timestamp tracking
            $table->timestamp('assigned_at')->nullable()->after('round')
                ->comment('When reviewer was assigned');
            $table->timestamp('deadline_at')->nullable()->after('assigned_at')
                ->comment('Review deadline');
            $table->timestamp('started_at')->nullable()->after('deadline_at')
                ->comment('When reviewer started reviewing');
            $table->timestamp('completed_at')->nullable()->after('started_at')
                ->comment('When review was completed');

            // Add index for deadline queries
            $table->index(['deadline_at']);
            $table->index(['round']);
        });

        // Update status enum to include new statuses
        // Note: MySQL/MariaDB requires dropping and recreating the column for enum changes
        DB::statement("ALTER TABLE proposal_reviewer MODIFY COLUMN status ENUM('pending', 'in_progress', 'completed', 're_review_requested') DEFAULT 'pending' COMMENT 'Status Review'");

        // Set assigned_at to created_at for existing records
        DB::table('proposal_reviewer')
            ->whereNull('assigned_at')
            ->update(['assigned_at' => DB::raw('created_at')]);

        // Set completed_at to updated_at for completed reviews
        DB::table('proposal_reviewer')
            ->where('status', 'completed')
            ->whereNull('completed_at')
            ->update(['completed_at' => DB::raw('updated_at')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First, revert any 'in_progress' or 're_review_requested' to 'pending'
        DB::table('proposal_reviewer')
            ->whereIn('status', ['in_progress', 're_review_requested'])
            ->update(['status' => 'pending']);

        // Revert status enum
        DB::statement("ALTER TABLE proposal_reviewer MODIFY COLUMN status ENUM('pending', 'reviewing', 'completed') DEFAULT 'pending' COMMENT 'Status Review'");

        Schema::table('proposal_reviewer', function (Blueprint $table) {
            $table->dropIndex(['deadline_at']);
            $table->dropIndex(['round']);
            $table->dropColumn(['round', 'assigned_at', 'deadline_at', 'started_at', 'completed_at']);
        });
    }
};
