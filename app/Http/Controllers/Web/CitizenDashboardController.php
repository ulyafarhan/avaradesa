<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\KategoriSurat;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller untuk mengelola dasbor personal warga dan keluarga.
 *
 * Menyajikan ringkasan statistik pengajuan surat, kelengkapan biodata,
 * daftar anggota keluarga, dan kategori surat yang tersedia.
 */
class CitizenDashboardController extends Controller
{
    /**
     * Memproses dan menyajikan data statistik pengajuan serta kelengkapan profil warga.
     *
     * Menghitung persentase kelengkapan biodata, mengambil daftar pengajuan surat
     * (termasuk milik anggota keluarga jika warga adalah kepala keluarga),
     * serta merangkum status pengajuan (pending, diproses, selesai).
     *
     * @param  \Illuminate\Http\Request  $request  Request yang mengandung data warga terautentikasi
     * @return \Inertia\Response  Halaman dasbor warga dengan data statistik dan pengajuan
     */
    public function __invoke(Request $request): Response
    {
        $warga = $request->user('penduduk');
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
                    'umur' => $p->umur,
                ])
            : collect();

        $familyNiks = $isKepalaKeluarga
            ? $anggotaKeluarga->pluck('nik')->toArray()
            : [$warga->nik];

        return Inertia::render('Citizen/Dashboard', [
            'warga' => $warga->load('keluarga'),
            'kategoriSurat' => KategoriSurat::query()
                ->active()
                ->orderBy('nama_surat')
                ->get(),
            'pengajuan' => PengajuanSurat::query()
                ->whereIn('nik_pemohon', $familyNiks)
                ->with(['kategori:id,nama_surat,kode_surat', 'tracking', 'pemohon:nik,nama_lengkap'])
                ->latest()
                ->paginate(6),
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
