<?php

namespace App\Filament\Resources\PendudukResource\Pages;

use App\Filament\Resources\PendudukResource;
use Filament\Resources\Pages\ManageRecords;

/**
 * Halaman manajemen data penduduk.
 *
 * Menampilkan seluruh data kependudukan dalam tampilan tabel dengan aksi
 * CRUD terintegrasi (buat, edit, hapus) dan dukungan filter tanpa navigasi
 * halaman terpisah.
 *
 * @see \App\Filament\Resources\PendudukResource
 * @see \App\Models\Penduduk
 */
class ManagePenduduk extends ManageRecords
{
    protected static string $resource = PendudukResource::class;
}
