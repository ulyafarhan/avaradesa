<?php

/**
 * MIGRASI — Tabel Informasi Publik
 *
 * Menyimpan konten berita, pengumuman, dan informasi publik lainnya
 * yang ditampilkan di halaman depan web AvaraDesa.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `informasi_publik`.
     *
     * - id            : ULID (PK)
     * - judul         : Judul informasi/berita
     * - slug          : Slug URL unik untuk SEO (contoh: 'pengumuman-posyandu-juli-2024')
     * - konten        : Isi konten (HTML/Markdown)
     * - kategori      : Kategori informasi (contoh: 'Berita', 'Pengumuman', 'Kegiatan'), 50 char
     * - cover_image   : Path gambar sampul (nullable)
     * - is_published  : Status publish/draft (default: false/draft)
     * - author_id     : ULID admin penulis (FK → administrators.id, nullOnDelete)
     * - created_at    : Waktu publikasi
     *
     * Index: kategori, slug
     */
    public function up(): void
    {
        Schema::create('informasi_publik', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('konten');
            $table->string('kategori', 50);
            $table->string('cover_image')->nullable();
            $table->boolean('is_published')->default(false);
            $table->ulid('author_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('author_id')
                ->references('id')
                ->on('administrators')
                ->nullOnDelete();

            $table->index('kategori');
            $table->index('slug');
        });
    }

    /**
     * Hapus tabel `informasi_publik`.
     */
    public function down(): void
    {
        Schema::dropIfExists('informasi_publik');
    }
};
