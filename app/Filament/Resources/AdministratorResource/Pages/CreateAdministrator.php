<?php

namespace App\Filament\Resources\AdministratorResource\Pages;

use App\Filament\Resources\AdministratorResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * Halaman pembuatan administrator baru pada panel Filament.
 *
 * Mengelola proses pembuatan data administrator melalui formulir CRUD
 * yang ditentukan oleh `AdministratorResource`, termasuk validasi input
 * dan penyimpanan data ke dalam tabel administrator.
 */
class CreateAdministrator extends CreateRecord
{
    /**
     * Resource terkait yang mendefinisikan konfigurasi model, formulir, tabel,
     * dan izin akses untuk halaman ini.
     *
     * @var string
     */
    protected static string $resource = AdministratorResource::class;
}