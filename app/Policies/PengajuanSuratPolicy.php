<?php

namespace App\Policies;

use App\Models\Administrator;
use App\Models\Penduduk;
use App\Models\PengajuanSurat;

class PengajuanSuratPolicy
{
    public function viewAny(Penduduk|Administrator $user): bool
    {
        if ($user instanceof Administrator) {
            return in_array($user->role, ['kepala_desa', 'sekdes', 'operator']);
        }

        return true;
    }

    public function view(Penduduk|Administrator $user, PengajuanSurat $pengajuan): bool
    {
        if ($user instanceof Administrator) {
            return in_array($user->role, ['kepala_desa', 'sekdes', 'operator']);
        }

        return $pengajuan->nik_pemohon === $user->nik;
    }

    public function create(Penduduk|Administrator $user): bool
    {
        return true;
    }

    public function approve(Administrator $admin): bool
    {
        return in_array($admin->role, ['kepala_desa', 'sekdes', 'operator']);
    }

    public function reject(Administrator $admin): bool
    {
        return in_array($admin->role, ['kepala_desa', 'sekdes', 'operator']);
    }
}
