<?php

namespace App\Filament\Resources\PengajuanSuratResource\Pages;

use App\Filament\Resources\PengajuanSuratResource;
use Filament\Resources\Pages\ListRecords;

/**
 * Halaman daftar pengajuan surat warga.
 *
 * Menampilkan seluruh permohonan surat warga dalam tabel dengan dukungan
 * filter status, pencarian, dan aksi verifikasi (setujui/tolak) serta
 * fitur setujui massal.
 *
 * @see \App\Filament\Resources\PengajuanSuratResource
 * @see \App\Models\PengajuanSurat
 */
class ListPengajuanSurat extends ListRecords
{
    protected static string $resource = PengajuanSuratResource::class;
}
