<?php

namespace App\Filament\Resources\KeluargaResource\Pages;

use App\Filament\Resources\KeluargaResource;
use Filament\Resources\Pages\ManageRecords;

/**
 * Halaman manajemen data Kartu Keluarga (KK).
 *
 * Menampilkan seluruh data KK dalam tampilan tabel dengan aksi CRUD
 * terintegrasi (buat, edit, hapus) tanpa navigasi halaman terpisah.
 *
 * @see \App\Filament\Resources\KeluargaResource
 * @see \App\Models\Keluarga
 */
class ManageKeluarga extends ManageRecords
{
    protected static string $resource = KeluargaResource::class;
}
