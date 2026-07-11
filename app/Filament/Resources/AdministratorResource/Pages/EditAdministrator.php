<?php

namespace App\Filament\Resources\AdministratorResource\Pages;

use App\Filament\Resources\AdministratorResource;
use Filament\Resources\Pages\EditRecord;

/**
 * Halaman pengeditan data administrator pada panel Filament.
 *
 * Mengelola proses pembaruan data administrator yang sudah ada melalui
 * formulir CRUD dari `AdministratorResource`, termasuk validasi input,
 * penyimpanan perubahan, dan navigasi kembali setelah penyimpanan.
 */
class EditAdministrator extends EditRecord
{
    /**
     * Resource terkait yang mendefinisikan konfigurasi model, formulir, tabel,
     * dan izin akses untuk halaman ini.
     *
     * @var string
     */
    protected static string $resource = AdministratorResource::class;
}