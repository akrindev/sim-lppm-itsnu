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
        Schema::table('institutions', function (Blueprint $table) {
            $table->string('short_name', 100)->nullable()->after('name')->comment('Nama singkat institusi');
            $table->text('address')->nullable()->after('short_name')->comment('Alamat institusi');
            $table->string('phone', 50)->nullable()->after('address')->comment('Nomor telepon');
            $table->string('email')->nullable()->after('phone')->comment('Email institusi');
            $table->string('website')->nullable()->after('email')->comment('Website institusi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn(['short_name', 'address', 'phone', 'email', 'website']);
        });
    }
};
