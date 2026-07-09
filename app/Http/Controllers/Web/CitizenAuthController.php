<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller untuk menangani autentikasi warga menggunakan NIK dan Nomor KK.
 *
 * Menyediakan layanan login dan logout bagi warga yang terdaftar
 * dalam database kependudukan desa.
 */
class CitizenAuthController extends Controller
{
    /**
     * Menampilkan halaman formulir login warga.
     *
     * Pada environment lokal, menampilkan kredensial uji (test credentials)
     * untuk mempermudah pengembangan dan pengujian.
     *
     * @return \Inertia\Response  Halaman formulir login warga
     */
    public function create(): Response
    {
        $testCredentials = Penduduk::where('nik', '1118060512900001')->first(['nik', 'no_kk']);

        return Inertia::render('Auth/Login', [
            'testCredentials' => $testCredentials
        ]);
    }

    /**
     * Memproses permintaan autentikasi masuk (login) warga.
 *
     * Memvalidasi NIK dan Nomor KK, memeriksa keberadaan data penduduk
     * dan status mutasi 'Tetap', lalu melakukan login menggunakan
     * guard 'penduduk'.
     *
     * @param  \Illuminate\Http\Request  $request  Request yang berisi field 'nik' dan 'no_kk'
     * @return \Illuminate\Http\RedirectResponse  Redirect ke dasbor warga jika berhasil
     * @throws \Illuminate\Validation\ValidationException  Jika NIK/KK tidak valid atau status warga bukan 'Tetap'
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'nik' => ['required', 'string', 'size:16'],
            'no_kk' => ['required', 'string', 'size:16'],
        ]);

        $penduduk = Penduduk::query()
            ->where('nik', $credentials['nik'])
            ->where('no_kk', $credentials['no_kk'])
            ->first();

        if (! $penduduk || $penduduk->status_mutasi !== 'Tetap') {
            throw ValidationException::withMessages([
                'nik' => 'NIK, No KK, atau status warga tidak valid.',
            ]);
        }

        Auth::guard('penduduk')->login($penduduk);
        $request->session()->regenerate();

        return redirect()->intended(route('warga.dashboard'));
    }

    /**
     * Memproses permintaan keluar (logout) warga.
     *
     * Melakukan logout dari guard 'penduduk', menginvalidasi sesi,
     * dan meregenerasi token CSRF untuk keamanan.
     *
     * @param  \Illuminate\Http\Request  $request  Request saat ini
     * @return \Illuminate\Http\RedirectResponse  Redirect ke beranda dengan pesan sukses
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('penduduk')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda berhasil keluar.');
    }
}
