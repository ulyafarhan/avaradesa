<?php

namespace App\Filament\Widgets;

use App\Models\InformasiPublik;
use App\Models\Keluarga;
use App\Models\MutasiPenduduk;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/**
 * Widget dasbor untuk menampilkan ikhtisar statistik cepat (warga, surat, mutasi, berita).
 */
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Isolate;

#[Lazy]
#[Isolate]
class AdminStatsOverview extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '15s';

    protected int|string|array $columnSpan = 'full';

    protected $listeners = [
        'echo:pengajuan,PengajuanStatusUpdated' => '$refresh',
        'echo:mutasi,MutasiStatusUpdated' => '$refresh',
    ];

    /**
     * Membangun daftar komponen statistik (cards) untuk ditampilkan di dasbor.
     */
    protected function getStats(): array
    {
        $pendudukAktif = Penduduk::query()->aktif()->count();
        $totalKeluarga = Keluarga::query()->count();
        $pengajuanPending = PengajuanSurat::query()->pending()->count();
        $pengajuanTotal = PengajuanSurat::query()->count();
        $pengajuanSelesai = PengajuanSurat::query()->where('status', 'Selesai')->count();
        $mutasiPending = MutasiPenduduk::query()->where('status_verifikasi', 'Pending')->count();
        $mutasiTotal = MutasiPenduduk::query()->count();
        $informasiTerbit = InformasiPublik::query()->published()->count();
        $informasiTotal = InformasiPublik::query()->count();

        return [
            Stat::make('Penduduk Aktif', number_format($pendudukAktif))
                ->description('Warga terdaftar dengan status tetap')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('Kartu Keluarga', number_format($totalKeluarga))
                ->description('Total keluarga terdaftar')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('info')
                ->chart([3, 5, 2, 4, 6, 3, 4]),

            Stat::make('Surat Pending', number_format($pengajuanPending))
                ->description($pengajuanSelesai . ' selesai dari ' . $pengajuanTotal . ' total')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pengajuanPending > 0 ? 'warning' : 'success')
                ->chart([2, 4, 6, 3, $pengajuanPending, 4, 2]),

            Stat::make('Mutasi Pending', number_format($mutasiPending))
                ->description($mutasiTotal . ' total mutasi tercatat')
                ->descriptionIcon('heroicon-m-arrow-path-rounded-square')
                ->color($mutasiPending > 0 ? 'warning' : 'success')
                ->chart([1, 3, 2, $mutasiPending, 2, 3, 1]),

            Stat::make('Informasi Terbit', number_format($informasiTerbit))
                ->description($informasiTotal . ' artikel, ' . $informasiTerbit . ' sudah dipublikasi')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('success')
                ->chart([2, 3, 4, 3, 5, 4, 3]),
        ];
    }
}
