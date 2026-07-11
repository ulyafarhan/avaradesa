<?php

namespace App\Filament\Resources\PendudukResource\Pages;

use App\Filament\Resources\PendudukResource;
use Filament\Resources\Pages\ListRecords;

/**
 * Halaman daftar penduduk.
 *
 * Menampilkan seluruh data kependudukan dalam tabel dengan dukungan
 * pencarian, filter (status mutasi, jenis kelamin), dan aksi CRUD.
 *
 * @see \App\Filament\Resources\PendudukResource
 * @see \App\Models\Penduduk
 */
class ListPenduduk extends ListRecords
{
    protected static string $resource = PendudukResource::class;
}
