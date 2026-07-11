<?php

namespace App\Filament\Resources\KategoriSuratResource\Pages;

use App\Filament\Resources\KategoriSuratResource;
use Filament\Resources\Pages\ListRecords;

/**
 * Halaman daftar kategori/jenis surat layanan desa.
 *
 * Menampilkan seluruh data master kategori surat dalam tabel dengan dukungan
 * pencarian dan aksi CRUD (buat, edit, hapus).
 *
 * @see \App\Filament\Resources\KategoriSuratResource
 * @see \App\Models\KategoriSurat
 */
class ListKategoriSurat extends ListRecords
{
    protected static string $resource = KategoriSuratResource::class;
}
