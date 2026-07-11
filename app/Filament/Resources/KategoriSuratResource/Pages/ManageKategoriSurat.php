<?php

namespace App\Filament\Resources\KategoriSuratResource\Pages;

use App\Filament\Resources\KategoriSuratResource;
use Filament\Resources\Pages\ManageRecords;

/**
 * Halaman manajemen kategori/jenis surat layanan desa.
 *
 * Menampilkan seluruh data master kategori surat dalam tampilan tabel dengan
 * aksi CRUD terintegrasi (buat, edit, hapus) tanpa navigasi halaman terpisah.
 *
 * @see \App\Filament\Resources\KategoriSuratResource
 * @see \App\Models\KategoriSurat
 */
class ManageKategoriSurat extends ManageRecords
{
    protected static string $resource = KategoriSuratResource::class;
}
