<?php

namespace App\Filament\Resources\AdministratorResource\Pages;

use App\Filament\Resources\AdministratorResource;
use Filament\Resources\Pages\ListRecords;

/**
 * Halaman daftar administrator pada panel Filament.
 *
 * Menampilkan tabel seluruh data administrator yang terdaftar, dilengkapi
 * fitur pencarian, filter, pengurutan, dan aksi massal sesuai konfigurasi
 * dari `AdministratorResource`.
 */
class ListAdministrator extends ListRecords
{
    /**
     * Resource terkait yang mendefinisikan konfigurasi model, formulir, tabel,
     * dan izin akses untuk halaman ini.
     *
     * @var string
     */
    protected static string $resource = AdministratorResource::class;
}