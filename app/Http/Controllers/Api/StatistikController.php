<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StatistikService;

/**
 * Controller untuk mengelola pengambilan data statistik kependudukan dan layanan desa lewat API.
 *
 * @group Statistik & Verifikasi
 */
class StatistikController extends Controller
{
    /**
     * Menginjeksi dependensi StatistikService.
     */
    public function __construct(
        protected StatistikService $statistik
    ) {}

    /**
     * Mengambil rangkuman data demografi kependudukan desa.
     *
     * @unauthenticated
     *
     * @responseField data object Data statistik demografi kependudukan.
     * @responseField data.total_penduduk int Total jumlah penduduk.
     * @responseField data.laki_laki int Jumlah penduduk laki-laki.
     * @responseField data.perempuan int Jumlah penduduk perempuan.
     * @responseField data.kelompok_usia array Distribusi penduduk berdasarkan kelompok usia.
     *
     * @response {
     *   "data": {
     *     "total_penduduk": 2500,
     *     "laki_laki": 1250,
     *     "perempuan": 1250,
     *     "kelompok_usia": [
     *       { "kelompok": "0-14", "jumlah": 600 },
     *       { "kelompok": "15-64", "jumlah": 1700 },
     *       { "kelompok": "65+", "jumlah": 200 }
     *     ]
     *   }
     * }
     */
    public function demografi()
    {
        $data = $this->statistik->getDemografi();

        return response()->json([
            'data' => $data,
        ]);
    }

    /**
     * Mengambil rangkuman data statistik layanan administrasi persuratan.
     *
     * @unauthenticated
     *
     * @responseField data object Data statistik layanan persuratan.
     * @responseField data.total_pengajuan int Total seluruh pengajuan surat.
     * @responseField data.menunggu int Jumlah pengajuan dengan status Pending.
     * @responseField data.disetujui int Jumlah pengajuan dengan status Disetujui.
     * @responseField data.ditolak int Jumlah pengajuan dengan status Ditolak.
     * @responseField data.selesai int Jumlah pengajuan dengan status Selesai.
     *
     * @response {
     *   "data": {
     *     "total_pengajuan": 150,
     *     "menunggu": 10,
     *     "disetujui": 50,
     *     "ditolak": 5,
     *     "selesai": 85
     *   }
     * }
     */
    public function layanan()
    {
        $data = $this->statistik->getLayanan();

        return response()->json([
            'data' => $data,
        ]);
    }

    /**
     * Membersihkan cache penyimpanan data statistik.
     *
     * @group Statistik & Verifikasi
     * @subgroup Admin
     * @authenticated
     *
     * @responseField message string Pesan hasil operasi.
     *
     * @response {
     *   "message": "Cache statistik berhasil dibersihkan"
     * }
     */
    public function clearCache()
    {
        $this->statistik->clearCache();

        return response()->json([
            'message' => 'Cache statistik berhasil dibersihkan',
        ]);
    }
}
