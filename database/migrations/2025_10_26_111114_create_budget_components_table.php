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
        Schema::create('budget_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('budget_group_id')->constrained()->cascadeOnDelete();
            $table->string('code', 10);
            $table->string('name');
            $table->string('unit', 20)->comment('Satuan unit (pcs, pack, liter, etc)');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['budget_group_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_components');
    }
};
