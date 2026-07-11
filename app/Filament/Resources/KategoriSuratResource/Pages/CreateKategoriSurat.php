<?php

namespace App\Filament\Resources\KategoriSuratResource\Pages;

use App\Filament\Resources\KategoriSuratResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * Halaman pembuatan kategori/jenis surat layanan desa.
 *
 * Menyediakan form untuk menambah data master kategori surat, termasuk kode surat,
 * nama, template view, serta konfigurasi schema isian dan persyaratan dokumen.
 *
 * @see \App\Filament\Resources\KategoriSuratResource
 * @see \App\Models\KategoriSurat
 */
class CreateKategoriSurat extends CreateRecord
{
    protected static string $resource = KategoriSuratResource::class;
}
