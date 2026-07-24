<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penduduk;
use App\Services\AuthService;
use Illuminate\Http\Request;

/**
 * Controller untuk menangani API Autentikasi (warga & admin) berbasis Token.
 *
 * @group Autentikasi
 */
class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Memproses login warga menggunakan NIK dan Nomor Kartu Keluarga.
     */
    public function loginWarga(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16',
            'no_kk' => 'required|string|size:16',
        ]);

        $data = $this->authService->loginWarga($request->nik, $request->no_kk);

        return response()->json(array_merge(['message' => 'Login berhasil'], $data));
    }

    /**
     * Memproses login administrator menggunakan Username dan Password.
     */
    public function loginAdmin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $data = $this->authService->loginAdmin($request->username, $request->password, $request->ip());

        return response()->json(array_merge(['message' => 'Login berhasil'], $data));
    }

    /**
     * Memproses keluar log (logout).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    /**
     * Detail profil pengguna saat ini.
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        if ($user instanceof Penduduk) {
            $user->load('keluarga');
        }

        return response()->json([
            'user' => $user,
            'has_pin' => $user instanceof \App\Models\Penduduk ? !empty($user->pin_hash) : null,
            'has_biometric' => $user instanceof \App\Models\Penduduk ? !empty($user->biometric_key) : null,
        ]);
    }

    /**
     * Bind Telegram.
     */
    public function bindTelegram(Request $request)
    {
        $request->validate([
            'telegram_chat_id' => 'required|string|unique:penduduk,telegram_chat_id',
        ]);

        $user = $request->user();

        if (! $user instanceof Penduduk) {
            return response()->json([
                'message' => 'Hanya warga yang dapat bind Telegram',
            ], 403);
        }

        $user->update([
            'telegram_chat_id' => $request->telegram_chat_id,
        ]);

        \App\Models\AuditLog::log('warga', $user->nik, 'bind_telegram', 'penduduk', $user->nik);

        return response()->json([
            'message' => 'Telegram berhasil terhubung',
        ]);
    }

    /**
     * Mendaftarkan PIN 6-Digit baru untuk Warga (Aktivasi Pertama Kali).
     */
    public function registerPin(Request $request)
    {
        $request->validate([
            'nik'   => 'required|string|size:16',
            'no_kk' => 'required|string|size:16',
            'pin'   => 'required|string|digits:6|confirmed',
        ]);

        $this->authService->registerPin($request->nik, $request->no_kk, $request->pin);

        return response()->json([
            'message' => 'PIN 6-digit berhasil didaftarkan! Anda sekarang dapat login menggunakan NIK + PIN.',
        ]);
    }

    /**
     * Login Cepat menggunakan NIK + PIN 6-Digit dengan proteksi percobaan (Lockout).
     */
    public function loginPin(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16',
            'pin' => 'required|string|digits:6',
        ]);

        $data = $this->authService->loginPin($request->nik, $request->pin);

        return response()->json(array_merge(['message' => 'Login PIN berhasil'], $data));
    }

    /**
     * Mendaftarkan Kunci Biometrik (Sidik Jari / TouchID / FaceID) untuk HP Warga.
     */
    public function registerBiometric(Request $request)
    {
        $request->validate([
            'biometric_key' => 'required|string|min:16',
        ]);

        $user = $request->user();

        if (! $user instanceof Penduduk) {
            return response()->json(['message' => 'Hanya warga yang dapat mengaktifkan sidik jari'], 403);
        }

        $this->authService->registerBiometric($user, $request->biometric_key);

        return response()->json([
            'message' => 'Sidik jari berhasil dihubungkan dengan akun Anda',
        ]);
    }

    /**
     * Login Instan menggunakan Kunci Biometrik (Sidik Jari).
     */
    public function loginBiometric(Request $request)
    {
        $request->validate([
            'nik'           => 'required|string|size:16',
            'biometric_key' => 'required|string',
        ]);

        $data = $this->authService->loginBiometric($request->nik, $request->biometric_key);

        return response()->json(array_merge(['message' => 'Login Sidik Jari Berhasil'], $data));
    }

    /**
     * Reset PIN dengan Verifikasi Ulang NIK + No. KK.
     */
    public function resetPin(Request $request)
    {
        $request->validate([
            'nik'   => 'required|string|size:16',
            'no_kk' => 'required|string|size:16',
            'pin'   => 'required|string|digits:6|confirmed',
        ]);

        $this->authService->resetPin($request->nik, $request->no_kk, $request->pin);

        return response()->json([
            'message' => 'PIN berhasil di-reset',
        ]);
    }
}
