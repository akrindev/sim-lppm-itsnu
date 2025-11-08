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
        Schema::create('additional_outputs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('progress_report_id')->constrained()->onDelete('cascade')->comment('Progress Report');
            $table->foreignId('proposal_output_id')->constrained('proposal_outputs')->onDelete('cascade')->comment('Link to planned output');

            // Status & Book Information
            $table->enum('status', ['review', 'editing', 'published'])->comment('Status buku');
            $table->string('book_title')->comment('Judul buku');
            $table->string('publisher_name')->comment('Nama penerbit');
            $table->string('isbn', 30)->nullable()->comment('ISBN');
            $table->year('publication_year')->nullable()->comment('Tahun terbit');
            $table->integer('total_pages')->nullable()->comment('Jumlah halaman');
            $table->string('publisher_url', 500)->nullable()->comment('URL penerbit');
            $table->string('book_url', 500)->nullable()->comment('URL buku');

            // Documents
            $table->string('document_file')->nullable()->comment('File buku/draft');
            $table->string('publication_certificate')->nullable()->comment('Surat keterangan terbit');

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
        Schema::dropIfExists('additional_outputs');
    }
};
