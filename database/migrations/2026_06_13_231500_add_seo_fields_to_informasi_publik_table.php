<?php

/**
 * MIGRASI — Tambah kolom SEO ke tabel informasi_publik
 *
 * Menambahkan meta deskripsi dan kata kunci untuk optimasi
 * mesin pencari (SEO) pada konten informasi publik.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom SEO ke tabel `informasi_publik`.
     *
     * - meta_description : Deskripsi pendek untuk meta tag (ditampilkan di hasil pencarian)
     * - kata_kunci       : Kata kunci SEO (contoh: 'berita desa, kegiatan desa')
     */
    public function up(): void
    {
        Schema::table('informasi_publik', function (Blueprint $table) {
            $table->text('meta_description')->nullable()->after('cover_image');
            $table->string('kata_kunci')->nullable()->after('meta_description');
        });
    }

    /**
     * Balikkan: hapus kedua kolom SEO.
     */
    public function down(): void
    {
        Schema::table('informasi_publik', function (Blueprint $table) {
            $table->dropColumn(['meta_description', 'kata_kunci']);
        });
    }
};
