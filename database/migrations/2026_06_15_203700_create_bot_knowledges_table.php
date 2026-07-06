<?php

/**
 * MIGRASI — Tabel Basis Pengetahuan Bot
 *
 * Menyimpan database pengetahuan untuk Asisten Virtual Desa.
 * Berisi pasangan pertanyaan-jawaban (FAQ) dan konten referensi
 * yang digunakan AI untuk menjawab pertanyaan warga.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `bot_knowledges`.
     *
     * - id                   : ULID (PK)
     * - kunci                : Kode unik pengetahuan (contoh: 'faq-kk', 'info-posyandu')
     * - tipe                 : Jenis pengetahuan — 'faq' | 'informasi' | 'prosedur' (default: faq)
     * - pertanyaan_atau_topik: Pertanyaan (untuk FAQ) atau judul topik (untuk informasi/prosedur)
     * - kata_kunci           : JSON — array kata kunci untuk pencocokan pertanyaan warga
     * - jawaban_atau_konten  : Jawaban (untuk FAQ) atau konten referensi (untuk informasi/prosedur)
     * - is_aktif             : Status aktif/nonaktif (default: true)
     * - timestamps           : created_at & updated_at
     *
     * Index: kunci, tipe
     */
    public function up(): void
    {
        Schema::create('bot_knowledges', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('kunci', 50)->unique();
            $table->string('tipe', 20)->default('faq');
            $table->string('pertanyaan_atau_topik');
            $table->json('kata_kunci');
            $table->text('jawaban_atau_konten');
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->index('kunci');
            $table->index('tipe');
        });
    }

    /**
     * Hapus tabel `bot_knowledges`.
     */
    public function down(): void
    {
        Schema::dropIfExists('bot_knowledges');
    }
};
