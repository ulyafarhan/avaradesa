<?php

/**
 * MIGRASI — Tambah kolom kepala_keluarga_nik ke tabel keluarga
 *
 * Menambahkan relasi yang menunjuk kepala keluarga (penduduk.nik)
 * ke dalam tabel keluarga.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom `kepala_keluarga_nik` sebagai FK → penduduk.nik.
     * nullOnDelete: jika penduduk dihapus, kepala_keluarga_nik menjadi null.
     */
    public function up(): void
    {
        Schema::table('keluarga', function (Blueprint $table) {
            $table->string('kepala_keluarga_nik', 16)->nullable();

            $table->foreign('kepala_keluarga_nik')
                ->references('nik')
                ->on('penduduk')
                ->nullOnDelete();
        });
    }

    /**
     * Balikkan: hapus foreign key dan kolom.
     */
    public function down(): void
    {
        Schema::table('keluarga', function (Blueprint $table) {
            $table->dropForeign(['kepala_keluarga_nik']);
            $table->dropColumn('kepala_keluarga_nik');
        });
    }
};
