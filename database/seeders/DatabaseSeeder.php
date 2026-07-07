<?php

/**
 * SEEDER — Database Utama (Desa Udeung)
 *
 * Seeder utama yang memanggil semua seeder lain untuk mengisi
 * data awal aplikasi SIG-Udeung berdasarkan studi uji kelayakan
 * di Desa Udeung, Kecamatan Bandar Baru, Kabupaten Pidie Jaya,
 * Provinsi Aceh.
 *
 * @see \App\Models\PengaturanDesa
 */

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Jalankan semua seeder untuk data awal sistem.
     *
     * Urutan pemanggilan:
     * 1. AdministratorSeeder     — 3 akun admin default (kepala desa, sekdes, operator)
     * 2. PengaturanDesaSeeder — Konfigurasi awal desa
     * 3. KategoriSuratSeeder     — Jenis-jenis surat desa yang tersedia
     * 4. WilayahPendudukSeeder   — Data wilayah dan contoh penduduk
     * 5. TransaksiDummySeeder    — Riwayat pengajuan surat dan mutasi
     * 6. BeritaSeeder           — Informasi publik dan berita desa
     * 7. DemoAccountSeeder      — Akun demo untuk testing (warga & admin)
     * 8. TrafficLogSeeder       — Statistik kunjungan website
     * 9. BotKnowledgeSeeder     — Basis pengetahuan Telegram bot
     */
    public function run(): void
    {
        $this->call([
            AdministratorSeeder::class,
            PengaturanDesaSeeder::class,
            KategoriSuratSeeder::class,
            WilayahPendudukSeeder::class,
            TransaksiDummySeeder::class,
            BeritaSeeder::class,
            DemoAccountSeeder::class,
            TrafficLogSeeder::class,
            BotKnowledgeSeeder::class,
        ]);
    }
}
