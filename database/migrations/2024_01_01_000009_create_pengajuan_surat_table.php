<?php

/**
 * MIGRASI — Tabel Pengajuan Surat
 *
 * Menycatat permohonan surat desa yang diajukan oleh warga, mulai
 * dari pengajuan hingga selesai (lengkap dengan QR hash dan file PDF).
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `pengajuan_surat`.
     *
     * - id                   : ULID (PK)
     * - nomor_registrasi     : No. registrasi unik (contoh: 'SKU/2024/001'), 30 char
     * - nik_pemohon          : NIK pemohon (FK → penduduk.nik, cascadeOnDelete)
     * - kategori_surat_id    : Jenis surat (FK → kategori_surat.id, restrictOnDelete)
     * - data_isian           : JSON — nilai isian yang diisi pemohon sesuai schema_isian
     * - file_syarat          : JSON — path file dokumen syarat yang diunggah
     * - status               : Pending | Diproses | Disetujui | Ditolak | Selesai
     * - catatan_penolakan    : Alasan penolakan (nullable)
     * - qr_hash              : Hash unik untuk QR code verifikasi (unique, nullable)
     * - file_pdf_url         : Path file PDF surat yang sudah diterbitkan (nullable)
     * - diverifikasi_oleh    : ULID admin pemverifikasi (FK → administrators.id, nullOnDelete)
     * - timestamps           : created_at & updated_at
     *
     * Index: status, nik_pemohon
     */
    public function up(): void
    {
        Schema::create('pengajuan_surat', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('nomor_registrasi', 30)->unique();
            $table->string('nik_pemohon', 16);
            $table->ulid('kategori_surat_id');
            $table->json('data_isian');
            $table->json('file_syarat');
            $table->enum('status', ['Pending', 'Diproses', 'Disetujui', 'Ditolak', 'Selesai'])->default('Pending');
            $table->text('catatan_penolakan')->nullable();
            $table->string('qr_hash', 64)->nullable()->unique();
            $table->string('file_pdf_url')->nullable();
            $table->ulid('diverifikasi_oleh')->nullable();
            $table->timestamps();

            $table->foreign('nik_pemohon')
                ->references('nik')
                ->on('penduduk')
                ->cascadeOnDelete();

            $table->foreign('kategori_surat_id')
                ->references('id')
                ->on('kategori_surat')
                ->restrictOnDelete();

            $table->foreign('diverifikasi_oleh')
                ->references('id')
                ->on('administrators')
                ->nullOnDelete();

            $table->index('status');
            $table->index('nik_pemohon');
        });
    }

    /**
     * Hapus tabel `pengajuan_surat`.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_surat');
    }
};
