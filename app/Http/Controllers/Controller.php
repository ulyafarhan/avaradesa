<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * Kelas dasar controller untuk aplikasi web.
 *
 * Semua controller aplikasi harus mewarisi kelas ini untuk mendapatkan
 * fungsionalitas dasar dan middleware default yang telah dikonfigurasi
 * di app/Http/Kernel.php.
 */
abstract class Controller
{
    use AuthorizesRequests;

    protected function requireAdminRole(array $roles = ['kepala_desa', 'sekdes']): void
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, $roles)) {
            abort(403, 'Hanya kepala desa dan sekretaris desa yang dapat melakukan operasi ini.');
        }
    }
}
