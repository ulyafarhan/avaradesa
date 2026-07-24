<?php

namespace App\Services\AppApi;

use App\Models\Administrator;
use App\Models\AuditLog;
use App\Models\Penduduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

/**
 * Business logic untuk autentikasi App API.
 */
class AuthService
{
    // ── Token management ────────────────────────────────────────────

    public function createWargaToken(Penduduk $penduduk): string
    {
        return $penduduk->createToken('app-warga', ['warga'])->plainTextToken;
    }

    public function createAdminToken(Administrator $admin): string
    {
        return $admin->createToken('app-admin', ['admin'])->plainTextToken;
    }

    public function revokeCurrentToken(Request $request): void
    {
        $request->user()->currentAccessToken()->delete();
    }

    // ── Auth flows ──────────────────────────────────────────────────

    /**
     * Login warga via NIK + No KK (passwordless).
     *
     * @return array{token: string, user: Penduduk}
     */
    public function loginWarga(string $nik, string $noKk): array
    {
        $penduduk = Penduduk::where('nik', $nik)
            ->where('no_kk', $noKk)
            ->first();

        if (! $penduduk || $penduduk->status_mutasi !== 'Tetap') {
            throw ValidationException::withMessages([
                'nik' => ['NIK, No KK, atau status warga tidak valid.'],
            ]);
        }

        $token = $this->createWargaToken($penduduk);

        AuditLog::log('warga', $penduduk->nik, 'login_app', 'penduduk', $penduduk->nik);

        return ['token' => $token, 'user' => $penduduk];
    }

    /**
     * Login admin via username + password.
     *
     * @return array{token: string, user: Administrator}
     */
    public function loginAdmin(string $username, string $password, string $ip): array
    {
        $key = 'app_admin_login_ip:' . $ip;
        if (RateLimiter::tooManyAttempts($key, 5)) {
            abort(429, 'Terlalu banyak percobaan login. Coba lagi dalam 15 menit.');
        }

        $admin = Administrator::where('username', $username)->first();

        if (! $admin || ! Hash::check($password, $admin->password)) {
            RateLimiter::hit($key, 15 * 60);
            throw ValidationException::withMessages([
                'username' => ['Username atau password salah.'],
            ]);
        }

        RateLimiter::clear($key);
        $token = $this->createAdminToken($admin);

        AuditLog::log('admin', $admin->id, 'login_app', 'administrators', $admin->id);

        return ['token' => $token, 'user' => $admin];
    }

    /**
     * Registrasi PIN 6 digit untuk warga.
     */
    public function registerPin(Penduduk $penduduk, string $pin): void
    {
        $penduduk->update([
            'pin_hash' => Hash::make($pin),
            'pin_attempts' => 0,
            'locked_until' => null,
        ]);

        AuditLog::log('warga', $penduduk->nik, 'register_pin', 'penduduk', $penduduk->nik);
    }

    /**
     * Login warga via NIK + PIN.
     *
     * @return array{token: string, user: Penduduk}
     */
    public function loginPin(string $nik, string $pin): array
    {
        $penduduk = Penduduk::where('nik', $nik)->first();

        if (! $penduduk || $penduduk->status_mutasi !== 'Tetap') {
            throw ValidationException::withMessages([
                'nik' => ['NIK tidak ditemukan atau warga tidak aktif.'],
            ]);
        }

        // Brute-force lock check
        if ($penduduk->locked_until && now()->lt($penduduk->locked_until)) {
            throw ValidationException::withMessages([
                'pin' => ['Akun terkunci. Coba lagi setelah ' . $penduduk->locked_until->format('H:i') . '.'],
            ]);
        }

        if (! $penduduk->pin_hash || ! Hash::check($pin, $penduduk->pin_hash)) {
            $attempts = $penduduk->pin_attempts + 1;
            $update = ['pin_attempts' => $attempts];

            // ponytail: lock 15 menit setelah 5x gagal, tuning kalau perlu
            if ($attempts >= 5) {
                $update['locked_until'] = now()->addMinutes(15);
                $update['pin_attempts'] = 0;
            }

            $penduduk->update($update);

            throw ValidationException::withMessages([
                'pin' => ['PIN salah.'],
            ]);
        }

        // Reset attempts on success
        $penduduk->update(['pin_attempts' => 0, 'locked_until' => null]);

        $token = $this->createWargaToken($penduduk);

        AuditLog::log('warga', $penduduk->nik, 'login_pin_app', 'penduduk', $penduduk->nik);

        return ['token' => $token, 'user' => $penduduk];
    }

    /**
     * Reset PIN via verifikasi NIK + No KK.
     */
    public function resetPin(string $nik, string $noKk, string $newPin): void
    {
        $penduduk = Penduduk::where('nik', $nik)
            ->where('no_kk', $noKk)
            ->first();

        if (! $penduduk) {
            throw ValidationException::withMessages([
                'nik' => ['NIK atau No KK tidak valid.'],
            ]);
        }

        $penduduk->update([
            'pin_hash' => Hash::make($newPin),
            'pin_attempts' => 0,
            'locked_until' => null,
        ]);

        AuditLog::log('warga', $penduduk->nik, 'reset_pin', 'penduduk', $penduduk->nik);
    }
}
