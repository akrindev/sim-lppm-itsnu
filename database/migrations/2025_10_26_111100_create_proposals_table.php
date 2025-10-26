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
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Judul Proposal');
            $table->foreignId('submitter_id')->constrained('users')->onDelete('cascade')->comment('Pengaju');
            $table->morphs('detailable'); // Polymorphic relation to researches/community_services
            $table->foreignId('research_scheme_id')->nullable()->constrained('research_schemes')->onDelete('set null')->comment('Skema Penelitian');
            $table->foreignId('focus_area_id')->nullable()->constrained('focus_areas')->onDelete('set null')->comment('Bidang Fokus');
            $table->foreignId('theme_id')->nullable()->constrained('themes')->onDelete('set null')->comment('Tema');
            $table->foreignId('topic_id')->nullable()->constrained('topics')->onDelete('set null')->comment('Topik');
            $table->foreignId('national_priority_id')->nullable()->constrained('national_priorities')->onDelete('set null')->comment('Prioritas Riset Nasional');
            $table->foreignId('cluster_level1_id')->nullable()->constrained('science_clusters')->onDelete('set null')->comment('Rumpun Ilmu Level 1');
            $table->foreignId('cluster_level2_id')->nullable()->constrained('science_clusters')->onDelete('set null')->comment('Rumpun Ilmu Level 2');
            $table->foreignId('cluster_level3_id')->nullable()->constrained('science_clusters')->onDelete('set null')->comment('Rumpun Ilmu Level 3');
            $table->decimal('sbk_value', 15, 2)->nullable()->comment('Nilai SBK');
            $table->integer('duration_in_years')->default(1)->comment('Lama Kegiatan (tahun)');
            $table->text('summary')->nullable()->comment('Ringkasan');
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'approved', 'rejected', 'completed'])->default('draft')->comment('Status Proposal');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
