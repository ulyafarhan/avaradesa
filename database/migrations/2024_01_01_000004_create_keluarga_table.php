<?php

/**
 * MIGRASI — Tabel Keluarga
 *
 * Membuat tabel `keluarga` untuk menyimpan data kepala keluarga (KK)
 * warga desa. Setiap keluarga diidentifikasi secara unik
 * melalui Nomor Kartu Keluarga (KK) 16 digit.
 *
 * @see \App\Models\Keluarga
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi — buat tabel `keluarga`.
     *
     * Struktur tabel:
     * - no_kk   : Nomor Kartu Keluarga (16 digit) sebagai primary key, sesuai format standar Dukcapil
     * - alamat  : Alamat lengkap keluarga (menggunakan TEXT untuk fleksibilitas panjang alamat)
     * - dusun   : Nama dusun/jorong tempat keluarga berada (maksimal 50 karakter)
     * - rt_rw   : Nomor RT dan RW, disimpan dalam satu kolom (contoh: '001/002'), maksimal 30 karakter
     *
     * Catatan: Tabel ini belum menggunakan foreign key ke referensi_wilayah,
     * karena wilayah (desa) sudah ditentukan di level aplikasi.
     */
    public function up(): void
    {
        Schema::create('keluarga', function (Blueprint $table) {
            $table->string('no_kk', 16)->primary();
            $table->text('alamat');
            $table->string('dusun', 50);
            $table->string('rt_rw', 30);
        });
    }

    /**
     * Balikkan migrasi — hapus tabel `keluarga`.
     */
    public function down(): void
    {
        Schema::dropIfExists('keluarga');
    }
};
