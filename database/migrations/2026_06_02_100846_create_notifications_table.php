<?php

/**
 * MIGRASI — Tabel Notifikasi (Laravel)
 *
 * Tabel bawaan Laravel untuk menyimpan notifikasi yang dikirim
 * ke pengguna (admin maupun warga). Mendukung notifikasi
 * polymorphic dengan ULID dan status read/unread.
 *
 * @see https://laravel.com/docs/notifications
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `notifications`.
     *
     * - id               : UUID (PK)
     * - type             : Nama kelas notifikasi (contoh: 'MutasiDisetujui')
     * - notifiable_type  : Model penerima (polymorphic: Penduduk / Administrator)
     * - notifiable_id    : ULID penerima (ulidMorphs)
     * - data             : JSON — konten notifikasi (pesan, link, dll)
     * - read_at          : Waktu dibaca (null = belum dibaca)
     * - timestamps       : created_at & updated_at
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->ulidMorphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel `notifications`.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
