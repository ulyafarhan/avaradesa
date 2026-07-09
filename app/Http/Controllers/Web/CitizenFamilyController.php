<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller untuk mengelola data anggota keluarga (Kartu Keluarga).
 *
 * Menyediakan layanan untuk menampilkan daftar anggota keluarga
 * dan memperbarui informasi profil anggota tertentu oleh Kepala Keluarga.
 */
class CitizenFamilyController extends Controller
{
    /**
     * Menampilkan daftar anggota keluarga yang terdaftar dalam KK yang sama.
     *
     * Mengambil semua penduduk aktif yang memiliki nomor KK yang sama,
     * diurutkan berdasarkan status keluarga (Kepala Keluarga, Istri, Anak, lainnya).
     * Menentukan apakah pengguna saat ini adalah kepala keluarga.
     *
     * @param  \Illuminate\Http\Request  $request  Request yang mengandung data warga terautentikasi
     * @return \Inertia\Response  Halaman daftar anggota keluarga
     */
    public function index(Request $request): Response
    {
        $warga = $request->user('penduduk');
        $keluarga = $warga->keluarga;

        $isKepalaKeluarga = $keluarga && $keluarga->kepala_keluarga_nik === $warga->nik;

        $anggota = $keluarga
            ? Penduduk::where('no_kk', $keluarga->no_kk)
                ->aktif()
                ->orderByRaw("CASE status_keluarga WHEN 'Kepala Keluarga' THEN 1 WHEN 'Istri' THEN 2 WHEN 'Anak' THEN 3 ELSE 4 END")
                ->get()
                ->map(fn ($p) => [
                    'nik' => $p->nik,
                    'nama_lengkap' => $p->nama_lengkap,
                    'jenis_kelamin' => $p->jenis_kelamin,
                    'tempat_lahir' => $p->tempat_lahir,
                    'tanggal_lahir' => $p->tanggal_lahir?->format('Y-m-d'),
                    'umur' => $p->umur,
                    'agama' => $p->agama,
                    'pendidikan' => $p->pendidikan,
                    'pekerjaan' => $p->pekerjaan,
                    'status_perkawinan' => $p->status_perkawinan,
                    'status_keluarga' => $p->status_keluarga,
                    'status_mutasi' => $p->status_mutasi,
                ])
            : collect();

        return Inertia::render('Citizen/Family', [
            'keluarga' => $keluarga,
            'anggota' => $anggota,
            'isKepalaKeluarga' => $isKepalaKeluarga,
        ]);
    }

    /**
     * Memperbarui informasi profil anggota keluarga tertentu (Hanya boleh diakses Kepala Keluarga).
     *
     * Memeriksa apakah pengguna adalah kepala keluarga, memvalidasi NIK anggota
     * dalam KK yang sama, lalu memperbarui field yang diizinkan
     * (pendidikan, pekerjaan, status perkawinan).
     *
     * @param  \Illuminate\Http\Request  $request  Request yang berisi field yang akan diperbarui
     * @param  string  $nik  NIK anggota keluarga yang akan diperbarui datanya
     * @return \Illuminate\Http\RedirectResponse  Redirect kembali dengan pesan sukses
     * @throws \Symfony\Component\HttpKernel\Exception\HttpNotFoundException  Jika anggota dengan NIK tidak ditemukan dalam KK
     * @throws \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException  Jika pengguna bukan kepala keluarga
     */
    public function update(Request $request, string $nik): RedirectResponse
    {
        $warga = $request->user('penduduk');
        $keluarga = $warga->keluarga;

        abort_unless(
            $keluarga && $keluarga->kepala_keluarga_nik === $warga->nik,
            403,
            'Hanya Kepala Keluarga yang dapat mengedit data anggota.'
        );

        $anggota = Penduduk::where('nik', $nik)
            ->where('no_kk', $keluarga->no_kk)
            ->firstOrFail();

        $validated = $request->validate([
            'pendidikan' => ['nullable', 'string', 'max:50'],
            'pekerjaan' => ['nullable', 'string', 'max:50'],
            'status_perkawinan' => ['nullable', 'string', 'max:20'],
        ]);

        $anggota->update($validated);

        return back()->with('success', 'Data anggota keluarga berhasil diperbarui.');
    }
}
