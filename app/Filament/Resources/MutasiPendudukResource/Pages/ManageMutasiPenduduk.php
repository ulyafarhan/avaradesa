<?php

namespace App\Filament\Resources\MutasiPendudukResource\Pages;

use App\Filament\Resources\MutasiPendudukResource;
use Filament\Resources\Pages\ManageRecords;

/**
 * Halaman manajemen mutasi kependudukan.
 *
 * Menampilkan seluruh data permohonan mutasi dalam tampilan tabel dengan
 * aksi CRUD terintegrasi (buat, edit, hapus) dan dukungan verifikasi
 * (setujui/tolak) tanpa navigasi halaman terpisah.
 *
 * @see \App\Filament\Resources\MutasiPendudukResource
 * @see \App\Models\MutasiPenduduk
 */
class ManageMutasiPenduduk extends ManageRecords
{
    protected static string $resource = MutasiPendudukResource::class;
}
