<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

/**
 * Halaman utama dasbor panel admin Filament.
 *
 * Menampilkan ringkasan status kependudukan dan surat-surat pelayanan desa.
 * Kelas ini menimpa dasbor bawaan Filament untuk menambahkan sapaan
 * personal berdasarkan waktu (Pagi/Siang/Sore/Malam) dan nama administrator
 * yang sedang login.
 */
class Dashboard extends BaseDashboard
{
    /**
     * Judul halaman yang ditampilkan di tab/heading browser.
     *
     * @var string|null
     */
    protected static ?string $title = 'Dashboard Administrasi';

    /**
     * Label navigasi yang tampil di menu sidebar panel admin.
     *
     * @var string|null
     */
    protected static ?string $navigationLabel = 'Dashboard';

    /**
     * Ikon navigasi sidebar yang diambil dari heroicons.
     *
     * @var string|\BackedEnum|null
     */
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    /**
     * Menghasilkan teks subheading (sapaan) pada halaman dasbor.
     *
     * Menentukan sapaan berdasarkan jam saat ini:
     * - 00:00–11:59 => "Selamat Pagi"
     * - 12:00–14:59 => "Selamat Siang"
     * - 15:00–17:59 => "Selamat Sore"
     * - 18:00–23:59 => "Selamat Malam"
     *
     * @return string|null Teks sapaan yang ditampilkan di bawah judul dasbor.
     */
    public function getSubheading(): ?string
    {
        $admin = auth('admin')->user();
        $greeting = match (true) {
            now()->hour < 12 => 'Selamat Pagi',
            now()->hour < 15 => 'Selamat Siang',
            now()->hour < 18 => 'Selamat Sore',
            default => 'Selamat Malam',
        };

        return $greeting . ', ' . ($admin?->username ?? 'Admin') . '! Berikut ringkasan data desa terkini.';
    }
}