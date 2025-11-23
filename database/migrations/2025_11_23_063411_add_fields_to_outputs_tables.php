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
        // Add fields to mandatory_outputs to support other types (Book, HKI, Product, Media, Video)
        Schema::table('mandatory_outputs', function (Blueprint $table) {
            // Book fields
            $table->string('book_title')->nullable()->comment('Judul Buku');
            $table->string('isbn', 30)->nullable()->comment('ISBN');
            $table->string('authors')->nullable()->comment('Penulis');
            $table->string('publisher')->nullable()->comment('Penerbit');
            $table->integer('total_pages')->nullable()->comment('Jumlah Halaman');

            // HKI fields
            $table->string('hki_type')->nullable()->comment('Jenis HKI (Paten, Hak Cipta, dll)');
            $table->string('registration_number')->nullable()->comment('Nomor Pendaftaran/Sertifikat');
            $table->string('inventors')->nullable()->comment('Inventor/Pencipta');

            // Product/TTG fields
            $table->string('product_name')->nullable()->comment('Nama Produk/Alat');
            $table->text('description')->nullable()->comment('Deskripsi Produk');
            $table->string('readiness_level')->nullable()->comment('TKT');
            $table->string('implementation_location')->nullable()->comment('Lokasi Penerapan');

            // Mass Media fields
            $table->string('media_name')->nullable()->comment('Nama Media Massa');
            $table->string('media_url')->nullable()->comment('Link Berita/Media');
            $table->date('publication_date')->nullable()->comment('Tanggal Terbit');

            // Video fields
            $table->string('video_url')->nullable()->comment('Link Video');
            $table->string('platform')->nullable()->comment('Platform (YouTube, Instagram, dll)');
        });

        // Add fields to additional_outputs to support other types (Journal, HKI, Product, Media, Video)
        Schema::table('additional_outputs', function (Blueprint $table) {
            // Journal fields
            $table->string('journal_title')->nullable()->comment('Nama Jurnal');
            $table->string('issn', 20)->nullable()->comment('ISSN');
            $table->string('eissn', 20)->nullable()->comment('E-ISSN');
            $table->string('volume', 50)->nullable()->comment('Volume');
            $table->string('issue_number', 50)->nullable()->comment('Issue');
            $table->string('doi', 255)->nullable()->comment('DOI');

            // HKI fields
            $table->string('hki_type')->nullable()->comment('Jenis HKI (Paten, Hak Cipta, dll)');
            $table->string('registration_number')->nullable()->comment('Nomor Pendaftaran/Sertifikat');
            $table->string('inventors')->nullable()->comment('Inventor/Pencipta');

            // Product/TTG fields
            $table->string('product_name')->nullable()->comment('Nama Produk/Alat');
            $table->text('description')->nullable()->comment('Deskripsi Produk');
            $table->string('readiness_level')->nullable()->comment('TKT');
            $table->string('implementation_location')->nullable()->comment('Lokasi Penerapan');

            // Mass Media fields
            $table->string('media_name')->nullable()->comment('Nama Media Massa');
            $table->string('media_url')->nullable()->comment('Link Berita/Media');
            $table->date('publication_date')->nullable()->comment('Tanggal Terbit');

            // Video fields
            $table->string('video_url')->nullable()->comment('Link Video');
            $table->string('platform')->nullable()->comment('Platform (YouTube, Instagram, dll)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mandatory_outputs', function (Blueprint $table) {
            $table->dropColumn([
                'book_title',
                'isbn',
                'authors',
                'publisher',
                'total_pages',
                'hki_type',
                'registration_number',
                'inventors',
                'product_name',
                'description',
                'readiness_level',
                'implementation_location',
                'media_name',
                'media_url',
                'publication_date',
                'video_url',
                'platform',
            ]);
        });

        Schema::table('additional_outputs', function (Blueprint $table) {
            $table->dropColumn([
                'journal_title',
                'issn',
                'eissn',
                'volume',
                'issue_number',
                'doi',
                'hki_type',
                'registration_number',
                'inventors',
                'product_name',
                'description',
                'readiness_level',
                'implementation_location',
                'media_name',
                'media_url',
                'publication_date',
                'video_url',
                'platform',
            ]);
        });
    }
};
