<?php

namespace App\Filament\Resources\MutasiPendudukResource\Pages;

use App\Filament\Resources\MutasiPendudukResource;
use Filament\Resources\Pages\EditRecord;

/**
 * Halaman edit data mutasi kependudukan.
 *
 * Menyediakan form untuk mengubah data mutasi (kelahiran, kematian,
 * kedatangan, kepindahan). Setelah perubahan tersimpan, pengguna dialihkan
 * kembali ke halaman daftar (index).
 *
 * @see \App\Filament\Resources\MutasiPendudukResource
 * @see \App\Models\MutasiPenduduk
 */
class EditMutasiPenduduk extends EditRecord
{
    protected static string $resource = MutasiPendudukResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
