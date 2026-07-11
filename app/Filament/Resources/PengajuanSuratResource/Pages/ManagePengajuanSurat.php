<?php

namespace App\Filament\Resources\PengajuanSuratResource\Pages;

use App\Filament\Resources\PengajuanSuratResource;
use Filament\Resources\Pages\ManageRecords;

/**
 * Halaman manajemen pengajuan surat warga.
 *
 * Menampilkan seluruh permohonan surat dalam tampilan tabel dengan aksi
 * CRUD terintegrasi (edit, hapus) dan dukungan verifikasi (setujui/tolak
 * termasuk setujui massal) tanpa navigasi halaman terpisah.
 *
 * @see \App\Filament\Resources\PengajuanSuratResource
 * @see \App\Models\PengajuanSurat
 */
class ManagePengajuanSurat extends ManageRecords
{
    protected static string $resource = PengajuanSuratResource::class;
}
