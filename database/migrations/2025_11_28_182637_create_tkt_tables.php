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
        Schema::create('tkt_levels', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index(); // e.g., "Umum", "Software"
            $table->integer('level'); // 1-9
            $table->text('description');
            $table->timestamps();
        });

        Schema::create('tkt_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tkt_level_id')->constrained('tkt_levels')->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->text('indicator');
            $table->timestamps();
        });

        Schema::create('research_tkt_level', function (Blueprint $table) {
            $table->foreignUuid('research_id')->constrained('research')->cascadeOnDelete();
            $table->foreignId('tkt_level_id')->constrained('tkt_levels')->cascadeOnDelete();
            $table->decimal('percentage', 5, 2)->default(0);
            $table->primary(['research_id', 'tkt_level_id']);
        });

        Schema::table('research', function (Blueprint $table) {
            $table->string('tkt_type')->nullable()->after('macro_research_group_id');
            if (Schema::hasColumn('research', 'final_tkt_target')) {
                $table->dropColumn('final_tkt_target');
            }
        });
    }

    public function down(): void
    {
        Schema::table('research', function (Blueprint $table) {
            $table->dropColumn('tkt_type');
            $table->integer('final_tkt_target')->nullable();
        });

        Schema::dropIfExists('research_tkt_level');
        Schema::dropIfExists('tkt_indicators');
        Schema::dropIfExists('tkt_levels');
    }
};
