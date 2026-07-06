<?php

/**
 * MIGRASI — Tabel Kategori Surat
 *
 * Mendefinisikan jenis-jenis surat desa yang tersedia (contoh: SKCK,
 * SKU, surat keterangan domisili), lengkap dengan template view,
 * skema isian, dan syarat dokumen pendukung.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `kategori_surat`.
     *
     * - id               : ULID (PK)
     * - kode_surat       : Kode unik surat (contoh: 'SKU', 'SKCK'), maks 20 char
     * - nama_surat       : Nama surat (contoh: 'Surat Keterangan Usaha')
     * - template_view    : Nama Blade view untuk rendering template surat
     * - schema_isian     : JSON — field isian yang harus diisi pemohon
     * - syarat_dokumen   : JSON — daftar dokumen yang harus dilampirkan
     * - is_active        : Status aktif/nonaktif (default: true)
     */
    public function up(): void
    {
        Schema::create('kategori_surat', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('kode_surat', 20)->unique();
            $table->string('nama_surat', 100);
            $table->string('template_view', 100);
            $table->json('schema_isian');
            $table->json('syarat_dokumen');
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Hapus tabel `kategori_surat`.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_surat');
    }
};
