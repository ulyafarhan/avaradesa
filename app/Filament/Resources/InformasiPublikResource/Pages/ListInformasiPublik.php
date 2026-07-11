<?php

namespace App\Filament\Resources\InformasiPublikResource\Pages;

use App\Filament\Resources\InformasiPublikResource;
use Filament\Resources\Pages\ListRecords;

/**
 * Halaman daftar artikel informasi publik desa.
 *
 * Menampilkan seluruh artikel berita, pengumuman, dan informasi transparansi
 * dalam tampilan tabel dengan dukungan pencarian, filter, dan aksi CRUD.
 *
 * @see \App\Filament\Resources\InformasiPublikResource
 * @see \App\Models\InformasiPublik
 */
class ListInformasiPublik extends ListRecords
{
    protected static string $resource = InformasiPublikResource::class;
}
