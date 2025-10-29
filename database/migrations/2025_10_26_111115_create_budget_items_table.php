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
        Schema::create('budget_items', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('proposal_id')->constrained('proposals')->onDelete('cascade')->comment('Proposal');
            $table->string('group')->comment('Kelompok RAB (Honor/Peralatan/dll)');
            $table->string('component')->comment('Komponen');
            $table->text('item_description')->comment('Item / Deskripsi');
            $table->integer('volume')->comment('Volume');
            $table->decimal('unit_price', 15, 2)->comment('Biaya Satuan');
            $table->decimal('total_price', 15, 2)->comment('Total Biaya');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_items');
    }
};
