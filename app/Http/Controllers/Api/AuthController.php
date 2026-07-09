<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Administrator;
use App\Models\AuditLog;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Controller untuk menangani API Autentikasi (warga & admin) berbasis Token.
 *
 * @group Autentikasi
 */
class AuthController extends Controller
{
    /**
     * Memproses login warga menggunakan NIK dan Nomor Kartu Keluarga.
     */
    public function loginWarga(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|size:16',
            'no_kk' => 'required|string|size:16',
        ]);

        $penduduk = Penduduk::where('nik', $request->nik)
            ->where('no_kk', $request->no_kk)
            ->first();

        if (! $penduduk || $penduduk->status_mutasi !== 'Tetap') {
            throw ValidationException::withMessages([
                'nik' => ['NIK, No KK, atau status warga tidak valid.'],
            ]);
        }

        $token = $penduduk->createToken('warga-token', ['warga'])->plainTextToken;

        AuditLog::log('warga', $penduduk->nik, 'login', 'penduduk', $penduduk->nik);

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $penduduk,
            'token' => $token,
            'has_pin' => !empty($penduduk->pin_hash),
            'has_biometric' => !empty($penduduk->biometric_key),
        ]);
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

        $admin = Administrator::where('username', $request->username)->first();

        if (! $admin || ! Hash::check($request->password, $admin->password)) {
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        $token = $admin->createToken('admin-token', ['admin'])->plainTextToken;

        AuditLog::log('admin', $admin->id, 'login', 'administrators', $admin->id);

        return response()->json([
            'message' => 'Login berhasil',
            'user' => $admin,
            'token' => $token,
        ]);
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
            'has_pin' => !empty($user->pin_hash),
            'has_biometric' => !empty($user->biometric_key),
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

        AuditLog::log('warga', $user->nik, 'bind_telegram', 'penduduk', $user->nik);

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
            'nik'   => 'required|string|size:16|exists:penduduk,nik',
            'no_kk' => 'required|string|size:16',
            'pin'   => 'required|string|digits:6|confirmed',
        ]);

        $penduduk = Penduduk::where('nik', $request->nik)
            ->where('no_kk', $request->no_kk)
            ->first();

        if (! $penduduk) {
            throw ValidationException::withMessages([
                'nik' => ['NIK atau No. KK tidak cocok.'],
            ]);
        }

        $hash = Hash::make($request->pin);
        $penduduk->update([
            'pin_hash' => $hash,
            'pin_attempts' => 0,
            'locked_until' => null,
        ]);

        AuditLog::log('warga', $penduduk->nik, 'register_pin', 'penduduk', $penduduk->nik);

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
            'nik' => 'required|string|size:16|exists:penduduk,nik',
            'pin' => 'required|string|digits:6',
        ]);

        $penduduk = Penduduk::where('nik', $request->nik)->first();

        if (! $penduduk || ! $penduduk->pin_hash) {
            throw ValidationException::withMessages([
                'pin' => ['PIN belum didaftarkan untuk NIK ini. Silakan daftar PIN terlebih dahulu.'],
            ]);
        }

        // Cek status terkunci
        if ($penduduk->locked_until && now()->lessThan($penduduk->locked_until)) {
            $diff = now()->diffInMinutes($penduduk->locked_until) + 1;
            throw ValidationException::withMessages([
                'pin' => ["Akun terkunci karena salah PIN 5 kali. Coba lagi dalam {$diff} menit."],
            ]);
        }

        // Cek verifikasi hash PIN
        if (! Hash::check($request->pin, $penduduk->pin_hash)) {
            $attempts = $penduduk->pin_attempts + 1;

            if ($attempts >= 5) {
                $penduduk->update([
                    'pin_attempts' => 0,
                    'locked_until' => now()->addMinutes(15),
                ]);
                throw ValidationException::withMessages([
                    'pin' => ['PIN salah 5 kali. Akun Anda dikunci sementara selama 15 menit.'],
                ]);
            }

            $penduduk->update(['pin_attempts' => $attempts]);
            $remaining = 5 - $attempts;

            throw ValidationException::withMessages([
                'pin' => ["PIN salah. Kesempatan tersisa {$remaining} kali."],
            ]);
        }

        // Reset percobaan jika sukses
        $penduduk->update([
            'pin_attempts' => 0,
            'locked_until' => null,
        ]);

        $token = $penduduk->createToken('warga-token', ['warga'])->plainTextToken;

        AuditLog::log('warga', $penduduk->nik, 'login_pin', 'penduduk', $penduduk->nik);

        return response()->json([
            'message' => 'Login PIN berhasil',
            'user' => $penduduk,
            'token' => $token,
            'has_pin' => true,
            'has_biometric' => !empty($penduduk->biometric_key),
        ]);
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

        $user->update([
            'biometric_key' => $request->biometric_key,
        ]);

        AuditLog::log('warga', $user->nik, 'register_biometric', 'penduduk', $user->nik);

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
            'nik'           => 'required|string|size:16|exists:penduduk,nik',
            'biometric_key' => 'required|string',
        ]);

        $penduduk = Penduduk::where('nik', $request->nik)->first();

        if (! $penduduk || ! $penduduk->biometric_key || $penduduk->biometric_key !== $request->biometric_key) {
            throw ValidationException::withMessages([
                'biometric_key' => ['Verifikasi sidik jari gagal atau belum diaktifkan.'],
            ]);
        }

        $token = $penduduk->createToken('warga-token', ['warga'])->plainTextToken;

        AuditLog::log('warga', $penduduk->nik, 'login_biometric', 'penduduk', $penduduk->nik);

        return response()->json([
            'message' => 'Login Sidik Jari Berhasil',
            'user'    => $penduduk,
            'token'   => $token,
            'has_pin' => !empty($penduduk->pin_hash),
            'has_biometric' => true,
        ]);
    }

    /**
     * Reset PIN dengan Verifikasi Ulang NIK + No. KK.
     */
    public function resetPin(Request $request)
    {
        $request->validate([
            'nik'   => 'required|string|size:16|exists:penduduk,nik',
            'no_kk' => 'required|string|size:16',
            'pin'   => 'required|string|digits:6|confirmed',
        ]);

        $penduduk = Penduduk::where('nik', $request->nik)
            ->where('no_kk', $request->no_kk)
            ->first();

        if (! $penduduk) {
            throw ValidationException::withMessages([
                'nik' => ['NIK atau Nomor KK tidak cocok.'],
            ]);
        }

        $penduduk->update([
            'pin_hash' => Hash::make($request->pin),
            'pin_attempts' => 0,
            'locked_until' => null,
        ]);

        AuditLog::log('warga', $penduduk->nik, 'reset_pin', 'penduduk', $penduduk->nik);

        return response()->json([
            'message' => 'PIN berhasil di-reset',
        ]);
    }
}
