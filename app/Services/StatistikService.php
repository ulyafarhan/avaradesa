<?php

namespace App\Services;

use App\Models\Penduduk;
use App\Models\Keluarga;
use App\Models\PengajuanSurat;
use App\Models\MutasiPenduduk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/**
 * Service untuk menyediakan data statistik kependudukan dan layanan desa.
 *
 * Mengelola agregasi data demografi (penduduk, keluarga, dusun, agama,
 * pendidikan, pekerjaan, usia) dan statistik layanan (pengajuan surat,
 * mutasi penduduk) dengan cache 1 jam.
 */
class StatistikService
{
    /**
     * Mengambil data demografi penduduk secara lengkap.
     *
     * Data di-cache selama 1 jam untuk menghindari perhitungan berulang.
     *
     * @return array  Assoc array berisi total_penduduk, total_keluarga, laki_laki, perempuan,
     *                per_dusun, per_agama, per_pendidikan, per_pekerjaan, per_usia
     */
    public function getDemografi(): array
    {
        return Cache::remember('statistik_demografi', 3600, function () {
            $totalPenduduk = Penduduk::aktif()->count();
            $totalKeluarga = Keluarga::count();

            return [
                'total_penduduk' => $totalPenduduk,
                'total_keluarga' => $totalKeluarga,
                'laki_laki' => Penduduk::aktif()->where('jenis_kelamin', 'L')->count(),
                'perempuan' => Penduduk::aktif()->where('jenis_kelamin', 'P')->count(),
                'per_dusun' => $this->getPerDusun(),
                'per_agama' => $this->getPerAgama(),
                'per_pendidikan' => $this->getPerPendidikan(),
                'per_pekerjaan' => $this->getPerPekerjaan(),
                'per_usia' => $this->getPerKelompokUsia(),
            ];
        });
    }

    /**
     * Menghitung jumlah kepala keluarga per dusun.
     *
     * @return array  Assoc array dengan nama dusun sebagai kunci dan jumlah sebagai nilai
     */
    protected function getPerDusun(): array
    {
        return Keluarga::select('dusun', DB::raw('count(*) as jumlah'))
            ->groupBy('dusun')
            ->get()
            ->pluck('jumlah', 'dusun')
            ->toArray();
    }

    /**
     * Menghitung jumlah penduduk per agama.
     *
     * @return array  Assoc array dengan nama agama sebagai kunci dan jumlah penduduk sebagai nilai
     */
    protected function getPerAgama(): array
    {
        return Penduduk::aktif()
            ->select('agama', DB::raw('count(*) as jumlah'))
            ->groupBy('agama')
            ->get()
            ->pluck('jumlah', 'agama')
            ->toArray();
    }

    /**
     * Menghitung jumlah penduduk per tingkat pendidikan.
     *
     * @return array  Assoc array dengan tingkat pendidikan sebagai kunci dan jumlah penduduk sebagai nilai
     */
    protected function getPerPendidikan(): array
    {
        return Penduduk::aktif()
            ->select('pendidikan', DB::raw('count(*) as jumlah'))
            ->groupBy('pendidikan')
            ->get()
            ->pluck('jumlah', 'pendidikan')
            ->toArray();
    }

    /**
     * Menghitung 10 jenis pekerjaan terbanyak penduduk.
     *
     * @return array  Assoc array dengan nama pekerjaan sebagai kunci dan jumlah penduduk sebagai nilai
     */
    protected function getPerPekerjaan(): array
    {
        return Penduduk::aktif()
            ->select('pekerjaan', DB::raw('count(*) as jumlah'))
            ->groupBy('pekerjaan')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get()
            ->pluck('jumlah', 'pekerjaan')
            ->toArray();
    }

    /**
     * Menghitung jumlah penduduk per kelompok usia.
     *
     * Mendukung driver database MySQL, SQLite, dan PostgreSQL.
     * Kelompok usia: 0-5, 6-12, 13-17, 18-25, 26-40, 41-60, 60+.
     *
     * @return array  Assoc array dengan rentang usia sebagai kunci dan jumlah penduduk sebagai nilai
     */
    protected function getPerKelompokUsia(): array
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'sqlite') {
            $ageSql = "(strftime('%Y', 'now') - strftime('%Y', tanggal_lahir))";
        } elseif ($driver === 'mysql') {
            $ageSql = "(YEAR(CURDATE()) - YEAR(tanggal_lahir) - (DATE_FORMAT(CURDATE(), '%m%d') < DATE_FORMAT(tanggal_lahir, '%m%d')))";
        } else {
            $ageSql = "EXTRACT(YEAR FROM AGE(tanggal_lahir))";
        }

        $results = Penduduk::aktif()
            ->select(DB::raw("
                COUNT(CASE WHEN $ageSql <= 5 THEN 1 END) as age_0_5,
                COUNT(CASE WHEN $ageSql > 5 AND $ageSql <= 12 THEN 1 END) as age_6_12,
                COUNT(CASE WHEN $ageSql > 12 AND $ageSql <= 17 THEN 1 END) as age_13_17,
                COUNT(CASE WHEN $ageSql > 17 AND $ageSql <= 25 THEN 1 END) as age_18_25,
                COUNT(CASE WHEN $ageSql > 25 AND $ageSql <= 40 THEN 1 END) as age_26_40,
                COUNT(CASE WHEN $ageSql > 40 AND $ageSql <= 60 THEN 1 END) as age_41_60,
                COUNT(CASE WHEN $ageSql > 60 THEN 1 END) as age_60_plus
            "))
            ->first();

        return [
            '0-5' => (int) ($results->age_0_5 ?? 0),
            '6-12' => (int) ($results->age_6_12 ?? 0),
            '13-17' => (int) ($results->age_13_17 ?? 0),
            '18-25' => (int) ($results->age_18_25 ?? 0),
            '26-40' => (int) ($results->age_26_40 ?? 0),
            '41-60' => (int) ($results->age_41_60 ?? 0),
            '60+' => (int) ($results->age_60_plus ?? 0),
        ];
    }

    /**
     * Mengambil statistik layanan administrasi desa.
     *
     * Mencakup data pengajuan surat dan mutasi penduduk berdasarkan status,
     * di-cache selama 1 jam.
     *
     * @return array  Assoc array berisi pengajuan_surat, mutasi_penduduk, per_jenis_surat
     */
    public function getLayanan(): array
    {
        return Cache::remember('statistik_layanan', 3600, function () {
            return [
                'pengajuan_surat' => [
                    'total' => PengajuanSurat::count(),
                    'pending' => PengajuanSurat::where('status', 'Pending')->count(),
                    'diproses' => PengajuanSurat::where('status', 'Diproses')->count(),
                    'selesai' => PengajuanSurat::where('status', 'Selesai')->count(),
                    'ditolak' => PengajuanSurat::where('status', 'Ditolak')->count(),
                ],
                'mutasi_penduduk' => [
                    'total' => MutasiPenduduk::count(),
                    'pending' => MutasiPenduduk::where('status_verifikasi', 'Pending')->count(),
                    'disetujui' => MutasiPenduduk::where('status_verifikasi', 'Disetujui')->count(),
                    'ditolak' => MutasiPenduduk::where('status_verifikasi', 'Ditolak')->count(),
                ],
                'per_jenis_surat' => $this->getPerJenisSurat(),
            ];
        });
    }

    /**
     * Menghitung jumlah pengajuan surat per kategori.
     *
     * @return array  Assoc array dengan nama kategori surat sebagai kunci dan jumlah pengajuan sebagai nilai
     */
    protected function getPerJenisSurat(): array
    {
        return PengajuanSurat::select('kategori_surat_id', DB::raw('count(*) as jumlah'))
            ->with('kategori:id,nama_surat')
            ->groupBy('kategori_surat_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [optional($item->kategori)->nama_surat ?? 'Lainnya' => $item->jumlah];
            })
            ->toArray();
    }

    /**
     * Membersihkan cache data statistik demografi dan layanan.
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('statistik_demografi');
        Cache::forget('statistik_layanan');
    }
}
