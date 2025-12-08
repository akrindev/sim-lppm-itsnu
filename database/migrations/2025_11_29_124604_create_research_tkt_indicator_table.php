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
        Schema::create('research_tkt_indicator', function (Blueprint $table) {
            $table->foreignUuid('research_id')->constrained('research')->cascadeOnDelete();
            $table->foreignId('tkt_indicator_id')->constrained('tkt_indicators')->cascadeOnDelete();
            $table->decimal('score', 5, 2)->default(0); // 0.00 to 100.00

            $table->primary(['research_id', 'tkt_indicator_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_tkt_indicator');
    }
};
