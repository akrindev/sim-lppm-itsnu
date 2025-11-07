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
        Schema::table('budget_components', function (Blueprint $table) {
            $table->string('unit', 20)->after('name')->comment('Satuan unit (pcs, pack, liter, etc)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('budget_components', function (Blueprint $table) {
            $table->dropColumn('unit');
        });
    }
};
