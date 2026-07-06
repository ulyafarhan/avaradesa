<?php

/**
 * MIGRASI — Tabel Antrean Broadcast Telegram
 *
 * Menyimpan pesan broadcast yang akan dikirim ke warga melalui
 * Telegram, dengan dukungan penjadwalan dan pelacakan status
 * pengiriman.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `telegram_broadcast_queue`.
     *
     * - id              : ULID (PK)
     * - pesan           : Isi pesan yang akan di-broadcast
     * - kategori_target : Target penerima (contoh: 'all', 'dusun_a', 'rt_01'), 50 char
     * - status          : Queued | Processing | Completed | Failed (default: Queued)
     * - jadwal_kirim    : Waktu terjadwal untuk pengiriman (default: sekarang)
     * - waktu_selesai   : Waktu pengiriman selesai (nullable, terisi setelah Completed/Failed)
     * - created_by      : ULID admin pembuat broadcast (FK → administrators.id, nullOnDelete)
     *
     * Index: status, jadwal_kirim
     */
    public function up(): void
    {
        Schema::create('telegram_broadcast_queue', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->text('pesan');
            $table->string('kategori_target', 50);
            $table->enum('status', ['Queued', 'Processing', 'Completed', 'Failed'])->default('Queued');
            $table->timestamp('jadwal_kirim')->useCurrent();
            $table->timestamp('waktu_selesai')->nullable();
            $table->ulid('created_by')->nullable();

            $table->foreign('created_by')
                ->references('id')
                ->on('administrators')
                ->nullOnDelete();

            $table->index('status');
            $table->index('jadwal_kirim');
        });
    }

    /**
     * Hapus tabel `telegram_broadcast_queue`.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_broadcast_queue');
    }
};
