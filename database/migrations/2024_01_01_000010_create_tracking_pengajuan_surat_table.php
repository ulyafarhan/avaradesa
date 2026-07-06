<?php

/**
 * MIGRASI — Tabel Tracking Pengajuan Surat
 *
 * Mencatat riwayat perubahan status setiap pengajuan surat, sebagai
 * log audit untuk melacak siapa mengubah status dan kapan.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `tracking_pengajuan_surat`.
     *
     * - id                  : ULID (PK)
     * - pengajuan_surat_id  : ULID pengajuan (FK → pengajuan_surat.id, cascadeOnDelete)
     * - status_sebelumnya   : Status sebelum perubahan (nullable untuk log pertama)
     * - status_baru         : Status setelah perubahan
     * - keterangan_update   : Catatan/alasan perubahan (nullable)
     * - diupdate_oleh       : ULID admin pelaku perubahan (FK → administrators.id, nullOnDelete)
     * - created_at          : Waktu perubahan status
     *
     * Index: pengajuan_surat_id
     */
    public function up(): void
    {
        Schema::create('tracking_pengajuan_surat', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('pengajuan_surat_id');
            $table->string('status_sebelumnya', 20)->nullable();
            $table->string('status_baru', 20);
            $table->text('keterangan_update')->nullable();
            $table->ulid('diupdate_oleh')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('pengajuan_surat_id')
                ->references('id')
                ->on('pengajuan_surat')
                ->cascadeOnDelete();

            $table->foreign('diupdate_oleh')
                ->references('id')
                ->on('administrators')
                ->nullOnDelete();

            $table->index('pengajuan_surat_id');
        });
    }

    /**
     * Hapus tabel `tracking_pengajuan_surat`.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_pengajuan_surat');
    }
};
