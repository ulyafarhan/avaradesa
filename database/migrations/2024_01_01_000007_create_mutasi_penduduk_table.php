<?php

/**
 * MIGRASI — Tabel Mutasi Penduduk
 *
 * Mencatat riwayat perubahan data kependudukan: kelahiran, kematian,
 * kedatangan, dan kepindahan warga. Setiap mutasi dapat diverifikasi
 * oleh administrator.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `mutasi_penduduk`.
     *
     * - id                 : ULID (PK)
     * - nik                : NIK penduduk (FK → penduduk.nik, cascadeOnDelete)
     * - jenis_mutasi       : Kelahiran | Kematian | Kedatangan | Kepindahan
     * - tanggal_mutasi     : Tanggal kejadian mutasi
     * - keterangan         : Deskripsi detail mutasi
     * - dokumen_bukti      : Path/file bukti pendukung (akte, surat pindah, dll)
     * - status_verifikasi  : Pending | Disetujui | Ditolak (default: Pending)
     * - diverifikasi_oleh  : ULID administrator (FK → administrators.id, nullOnDelete)
     * - created_at         : Waktu pengajuan mutasi
     *
     * Index: status_verifikasi
     */
    public function up(): void
    {
        Schema::create('mutasi_penduduk', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('nik', 16);
            $table->enum('jenis_mutasi', ['Kelahiran', 'Kematian', 'Kedatangan', 'Kepindahan']);
            $table->date('tanggal_mutasi');
            $table->text('keterangan');
            $table->string('dokumen_bukti');
            $table->enum('status_verifikasi', ['Pending', 'Disetujui', 'Ditolak'])->default('Pending');
            $table->ulid('diverifikasi_oleh')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('nik')
                ->references('nik')
                ->on('penduduk')
                ->cascadeOnDelete();

            $table->foreign('diverifikasi_oleh')
                ->references('id')
                ->on('administrators')
                ->nullOnDelete();

            $table->index('status_verifikasi');
        });
    }

    /**
     * Hapus tabel `mutasi_penduduk`.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_penduduk');
    }
};
