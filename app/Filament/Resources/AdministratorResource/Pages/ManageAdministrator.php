<?php

namespace App\Filament\Resources\AdministratorResource\Pages;

use App\Filament\Resources\AdministratorResource;
use Filament\Resources\Pages\ManageRecords;

/**
 * Halaman pengelolaan administrator pada panel Filament.
 *
 * Menggabungkan fungsi daftar, buat, dan edit dalam satu halaman terpadu
 * sesuai konfigurasi dari `AdministratorResource`, sehingga administrator
 * dapat mengelola data tanpa berpindah halaman.
 */
class ManageAdministrator extends ManageRecords
{
    /**
     * Resource terkait yang mendefinisikan konfigurasi model, formulir, tabel,
     * dan izin akses untuk halaman ini.
     *
     * @var string
     */
    protected static string $resource = AdministratorResource::class;
}