<?php

namespace App\Services;

use App\Models\Administrator;
use App\Models\AuditLog;
use App\Models\Penduduk;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Memproses login warga.
     */
    public function loginWarga(string $nik, string $noKk): array
    {
        $key = 'warga_login:' . $nik;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            Log::warning('Warga login locked', ['nik' => $nik]);
            abort(429, 'Terlalu banyak percobaan login. Coba lagi dalam 15 menit.');
        }

        $penduduk = Penduduk::where('nik', $nik)->where('no_kk', $noKk)->first();

        if (! $penduduk || $penduduk->status_mutasi !== 'Tetap') {
            RateLimiter::hit($key, 15 * 60);
            throw ValidationException::withMessages([
                'nik' => ['NIK, No KK, atau status warga tidak valid.'],
            ]);
        }

        RateLimiter::clear($key);
        $token = $penduduk->createToken('warga-token', ['warga'])->plainTextToken;
        AuditLog::log('warga', $penduduk->nik, 'login', 'penduduk', $penduduk->nik);

        return [
            'user' => $penduduk,
            'token' => $token,
            'has_pin' => !empty($penduduk->pin_hash),
            'has_biometric' => !empty($penduduk->biometric_key),
        ];
    }

    /**
     * Memproses login administrator.
     */
    public function loginAdmin(string $username, string $password, string $ip): array
    {
        $key = 'admin_login_ip:' . $ip;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            Log::warning('Admin account locked', ['username' => $username, 'ip' => $ip]);
            abort(429, 'Akun terkunci. Coba lagi dalam 15 menit.');
        }

        $admin = Administrator::where('username', $username)->first();

        if (! $admin || ! Hash::check($password, $admin->password)) {
            RateLimiter::hit($key, 15 * 60);
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        RateLimiter::clear($key);
        $token = $admin->createToken('admin-token', ['admin'])->plainTextToken;
        AuditLog::log('admin', $admin->id, 'login', 'administrators', $admin->id);

        return [
            'user' => $admin,
            'token' => $token,
        ];
    }

    /**
     * Mendaftarkan PIN 6-Digit baru.
     */
    public function registerPin(string $nik, string $noKk, string $pin): void
    {
        $penduduk = Penduduk::where('nik', $nik)->where('no_kk', $noKk)->first();

        if (! $penduduk) {
            throw ValidationException::withMessages([
                'nik' => ['NIK atau No. KK tidak cocok.'],
            ]);
        }

        $penduduk->update([
            'pin_hash' => Hash::make($pin),
            'pin_attempts' => 0,
            'locked_until' => null,
        ]);

        AuditLog::log('warga', $penduduk->nik, 'register_pin', 'penduduk', $penduduk->nik);
    }

    /**
     * Memproses login menggunakan PIN.
     */
    public function loginPin(string $nik, string $pin): array
    {
        $penduduk = Penduduk::where('nik', $nik)->first();

        if (! $penduduk || ! $penduduk->pin_hash) {
            throw ValidationException::withMessages([
                'pin' => ['PIN belum didaftarkan untuk NIK ini. Silakan daftar PIN terlebih dahulu.'],
            ]);
        }

        if ($penduduk->locked_until && now()->lessThan($penduduk->locked_until)) {
            $diff = now()->diffInMinutes($penduduk->locked_until) + 1;
            throw ValidationException::withMessages([
                'pin' => ["Akun terkunci karena salah PIN 5 kali. Coba lagi dalam {$diff} menit."],
            ]);
        }

        if (! Hash::check($pin, $penduduk->pin_hash)) {
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

        $penduduk->update([
            'pin_attempts' => 0,
            'locked_until' => null,
        ]);

        $token = $penduduk->createToken('warga-token', ['warga'])->plainTextToken;
        AuditLog::log('warga', $penduduk->nik, 'login_pin', 'penduduk', $penduduk->nik);

        return [
            'user' => $penduduk,
            'token' => $token,
            'has_pin' => true,
            'has_biometric' => !empty($penduduk->biometric_key),
        ];
    }

    /**
     * Mendaftarkan Kunci Biometrik.
     */
    public function registerBiometric(Penduduk $user, string $biometricKey): void
    {
        $user->update([
            'biometric_key' => Hash::make($biometricKey . $user->nik . config('app.key')),
        ]);

        AuditLog::log('warga', $user->nik, 'register_biometric', 'penduduk', $user->nik);
    }

    /**
     * Login menggunakan Kunci Biometrik.
     */
    public function loginBiometric(string $nik, string $biometricKey): array
    {
        $key = 'biometric_login:' . $nik;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            Log::warning('Biometric login locked', ['nik' => $nik]);
            abort(429, 'Terlalu banyak percobaan sidik jari. Coba lagi dalam 15 menit.');
        }

        $penduduk = Penduduk::where('nik', $nik)->first();

        if (! $penduduk || ! $penduduk->biometric_key || ! Hash::check($biometricKey . $penduduk->nik . config('app.key'), $penduduk->biometric_key)) {
            RateLimiter::hit($key, 15 * 60);
            throw ValidationException::withMessages([
                'biometric_key' => ['Verifikasi sidik jari gagal atau belum diaktifkan.'],
            ]);
        }

        RateLimiter::clear($key);
        $token = $penduduk->createToken('warga-token', ['warga'])->plainTextToken;
        AuditLog::log('warga', $penduduk->nik, 'login_biometric', 'penduduk', $penduduk->nik);

        return [
            'user' => $penduduk,
            'token' => $token,
            'has_pin' => !empty($penduduk->pin_hash),
            'has_biometric' => true,
        ];
    }

    /**
     * Reset PIN dengan Verifikasi Ulang.
     */
    public function resetPin(string $nik, string $noKk, string $pin): void
    {
        $penduduk = Penduduk::where('nik', $nik)->where('no_kk', $noKk)->first();

        if (! $penduduk) {
            throw ValidationException::withMessages([
                'nik' => ['NIK atau Nomor KK tidak cocok.'],
            ]);
        }

        $penduduk->update([
            'pin_hash' => Hash::make($pin),
            'pin_attempts' => 0,
            'locked_until' => null,
        ]);

        AuditLog::log('warga', $penduduk->nik, 'reset_pin', 'penduduk', $penduduk->nik);
    }
}
