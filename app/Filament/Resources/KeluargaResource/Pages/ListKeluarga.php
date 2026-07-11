<?php

namespace App\Filament\Resources\KeluargaResource\Pages;

use App\Filament\Resources\KeluargaResource;
use Filament\Resources\Pages\ListRecords;

/**
 * Halaman daftar Kartu Keluarga (KK).
 *
 * Menampilkan seluruh data KK dalam tabel dengan dukungan pencarian
 * dan aksi CRUD (buat, edit, hapus).
 *
 * @see \App\Filament\Resources\KeluargaResource
 * @see \App\Models\Keluarga
 */
class ListKeluarga extends ListRecords
{
    protected static string $resource = KeluargaResource::class;
}
