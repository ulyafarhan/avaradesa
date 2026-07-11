<?php

namespace App\Filament\Resources\MutasiPendudukResource\Pages;

use App\Filament\Resources\MutasiPendudukResource;
use Filament\Resources\Pages\ListRecords;

/**
 * Halaman daftar mutasi kependudukan.
 *
 * Menampilkan seluruh data permohonan mutasi (kelahiran, kematian,
 * kedatangan, kepindahan) dalam tabel dengan dukungan filter status,
 * pencarian, dan aksi verifikasi (setujui/tolak).
 *
 * @see \App\Filament\Resources\MutasiPendudukResource
 * @see \App\Models\MutasiPenduduk
 */
class ListMutasiPenduduk extends ListRecords
{
    protected static string $resource = MutasiPendudukResource::class;
}
