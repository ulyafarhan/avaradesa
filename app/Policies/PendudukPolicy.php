<?php

namespace App\Policies;

use App\Models\Penduduk;

class PendudukPolicy
{
    public function view(Penduduk $user, Penduduk $target): bool
    {
        return $user->nik === $target->nik;
    }

    public function update(Penduduk $user, Penduduk $target): bool
    {
        return $user->nik === $target->nik;
    }
}
