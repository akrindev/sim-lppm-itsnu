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
        Schema::create('proposal_partner', function (Blueprint $table) {
            $table->uuid('proposal_id');
            $table->uuid('partner_id');
            $table->timestamps();

            $table->foreign('proposal_id')
                ->references('id')
                ->on('proposals')
                ->cascadeOnDelete();

            $table->foreign('partner_id')
                ->references('id')
                ->on('partners')
                ->cascadeOnDelete();

            $table->primary(['proposal_id', 'partner_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposal_partner');
    }
};
