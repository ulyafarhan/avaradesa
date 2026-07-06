<?php

/**
 * MIGRASI — Tambah kolom dokumen ke tabel penduduk
 *
 * Menambahkan field foto untuk profil, KTP, dan Kartu Keluarga
 * ke tabel penduduk untuk keperluan verifikasi data warga.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom dokumen ke tabel `penduduk`.
     *
     * - foto_profil : Path foto profil warga (nullable)
     * - foto_ktp    : Path hasil scan/foto KTP (nullable)
     * - foto_kk     : Path hasil scan/foto Kartu Keluarga (nullable)
     *
     * Ketiga kolom ditempatkan setelah `telegram_chat_id`.
     */
    public function up(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            $table->string('foto_profil')->nullable()->after('telegram_chat_id');
            $table->string('foto_ktp')->nullable()->after('foto_profil');
            $table->string('foto_kk')->nullable()->after('foto_ktp');
        });
    }

    /**
     * Balikkan: hapus ketiga kolom dokumen.
     */
    public function down(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            $table->dropColumn(['foto_profil', 'foto_ktp', 'foto_kk']);
        });
    }
};
