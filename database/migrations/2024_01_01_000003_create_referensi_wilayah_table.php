<?php

/**
 * MIGRASI — Tabel Referensi Wilayah
 *
 * Membuat tabel `referensi_wilayah` untuk menyimpan data kewilayahan
 * Indonesia secara hierarkis: Provinsi → Kabupaten/Kota → Kecamatan → Desa.
 *
 * Tabel ini menggunakan struktur self-referencing (parent-child) untuk
 * merepresentasikan relasi antar level wilayah, sehingga memungkinkan
 * pencarian data secara berjenjang.
 *
 * @see \App\Models\ReferensiWilayah
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi — buat tabel `referensi_wilayah`.
     *
     * Struktur tabel:
     * - kode_wilayah : Kode unik wilayah (primary key), contoh: '11' (provinsi), '11.01' (kabupaten),
     *                  '11.01.01' (kecamatan), '11.01.01.2001' (desa). Format sesuai kode BPS/Kemendagri.
     * - nama_wilayah : Nama wilayah, maksimal 100 karakter (contoh: 'Aceh', 'Pidie Jaya', 'Bandar Baru')
     * - level        : Tingkatan wilayah — 'provinsi', 'kabupaten', 'kecamatan', atau 'desa'
     * - parent_kode  : Kode wilayah induk (nullable untuk provinsi karena merupakan level tertinggi).
     *                  Melakukan referensi ke kolom `kode_wilayah` pada tabel yang sama.
     *
     * Relasi: parent_kode → kode_wilayah (self-referencing foreign key dengan restrictOnDelete,
     *          artinya wilayah induk tidak dapat dihapus jika masih memiliki child).
     *
     * Index: parent_kode (mempercepat pencarian child dari suatu wilayah induk)
     */
    public function up(): void
    {
        Schema::create('referensi_wilayah', function (Blueprint $table) {
            $table->string('kode_wilayah', 15)->primary();
            $table->string('nama_wilayah', 100);
            $table->enum('level', ['provinsi', 'kabupaten', 'kecamatan', 'desa']);
            $table->string('parent_kode', 15)->nullable();

            $table->foreign('parent_kode')
                ->references('kode_wilayah')
                ->on('referensi_wilayah')
                ->restrictOnDelete();

            $table->index('parent_kode');
        });
    }

    /**
     * Balikkan migrasi — hapus tabel `referensi_wilayah`.
     */
    public function down(): void
    {
        Schema::dropIfExists('referensi_wilayah');
    }
};
