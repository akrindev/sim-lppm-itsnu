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
        Schema::create('mandatory_outputs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('progress_report_id')->constrained()->onDelete('cascade')->comment('Progress Report');
            $table->foreignId('proposal_output_id')->constrained('proposal_outputs')->onDelete('cascade')->comment('Link to planned output');

            // Status & Author Information
            $table->enum('status_type', ['published', 'accepted', 'under_review', 'rejected'])->comment('Publication status');
            $table->enum('author_status', ['first_author', 'co_author', 'corresponding_author'])->comment('Author role');

            // Journal Information
            $table->string('journal_title')->comment('Nama jurnal');
            $table->string('issn', 20)->nullable()->comment('ISSN number');
            $table->string('eissn', 20)->nullable()->comment('E-ISSN number');
            $table->string('indexing_body')->nullable()->comment('Scopus/WoS/Sinta');
            $table->string('journal_url', 500)->nullable()->comment('Journal website URL');

            // Article Information
            $table->string('article_title')->comment('Judul artikel');
            $table->year('publication_year')->comment('Tahun publikasi');
            $table->string('volume', 50)->nullable()->comment('Volume');
            $table->string('issue_number', 50)->nullable()->comment('Issue/nomor');
            $table->integer('page_start')->nullable()->comment('Halaman awal');
            $table->integer('page_end')->nullable()->comment('Halaman akhir');
            $table->string('article_url', 500)->nullable()->comment('Article URL');
            $table->string('doi', 255)->nullable()->comment('DOI');
            $table->string('document_file')->nullable()->comment('Uploaded PDF file');

            $table->timestamps();

            $table->index('progress_report_id');
            $table->index('proposal_output_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mandatory_outputs');
    }
};
