<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriSurat;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use Illuminate\Http\Request;

/**
 * Controller untuk menangani API Dasbor (Dashboard) Warga.
 */
class DashboardController extends Controller
{
    /**
     * Mengambil data dasbor warga termasuk profil, kelengkapan biodata,
     * status keluarga, dan ringkasan pengajuan surat.
     *
     * @authenticated
     */
    public function index(Request $request)
    {
        $warga = $request->user();
        
        if (!$warga instanceof Penduduk) {
            return response()->json(['message' => 'Hanya untuk warga'], 403);
        }

        $warga->load('keluarga');
        $keluarga = $warga->keluarga;

        $isKepalaKeluarga = $keluarga && $keluarga->kepala_keluarga_nik === $warga->nik;

        $requiredFields = ['nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'agama', 'pendidikan', 'pekerjaan', 'status_perkawinan'];
        $filled = collect($requiredFields)->filter(fn ($field) => !blank($warga->{$field}))->count();
        $biodataComplete = $filled === count($requiredFields);
        $biodataCompleteness = round(($filled / count($requiredFields)) * 100);

        $anggotaKeluarga = $keluarga
            ? Penduduk::where('no_kk', $keluarga->no_kk)
                ->aktif()
                ->select(['nik', 'nama_lengkap', 'jenis_kelamin', 'status_keluarga', 'tanggal_lahir'])
                ->get()
                ->map(fn ($p) => [
                    'nik' => $p->nik,
                    'nama_lengkap' => $p->nama_lengkap,
                    'jenis_kelamin' => $p->jenis_kelamin,
                    'status_keluarga' => $p->status_keluarga,
                    'umur' => $p->umur, // accessor
                ])
            : collect();

        $familyNiks = $isKepalaKeluarga
            ? $anggotaKeluarga->pluck('nik')->toArray()
            : [$warga->nik];

        return response()->json([
            'warga' => $warga,
            'summary' => [
                'pending' => PengajuanSurat::query()->whereIn('nik_pemohon', $familyNiks)->where('status', 'Pending')->count(),
                'diproses' => PengajuanSurat::query()->whereIn('nik_pemohon', $familyNiks)->where('status', 'Diproses')->count(),
                'selesai' => PengajuanSurat::query()->whereIn('nik_pemohon', $familyNiks)->whereIn('status', ['Disetujui', 'Selesai'])->count(),
            ],
            'biodataComplete' => $biodataComplete,
            'biodataCompleteness' => $biodataCompleteness,
            'isKepalaKeluarga' => $isKepalaKeluarga,
            'anggotaKeluarga' => $anggotaKeluarga,
        ]);
    }
}
