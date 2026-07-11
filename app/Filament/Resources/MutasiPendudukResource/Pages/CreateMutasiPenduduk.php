<?php

namespace App\Filament\Resources\MutasiPendudukResource\Pages;

use App\Filament\Resources\MutasiPendudukResource;
use Filament\Resources\Pages\CreateRecord;

/**
 * Halaman pembuatan data mutasi kependudukan.
 *
 * Menyediakan form untuk mencatat permohonan mutasi (kelahiran, kematian,
 * kedatangan, kepindahan). Setelah tersimpan, pengguna dialihkan kembali
 * ke halaman daftar (index).
 *
 * @see \App\Filament\Resources\MutasiPendudukResource
 * @see \App\Models\MutasiPenduduk
 */
class CreateMutasiPenduduk extends CreateRecord
{
    protected static string $resource = MutasiPendudukResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
