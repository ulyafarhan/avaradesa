<?php

namespace App\Policies;

use App\Models\Administrator;
use App\Models\Penduduk;

class PendudukPolicy
{
    public function view($user, Penduduk $target): bool
    {
        if ($user instanceof Administrator) {
            return true;
        }
        return $user instanceof Penduduk && $user->nik === $target->nik;
    }

    public function update($user, Penduduk $target): bool
    {
        if ($user instanceof Administrator) {
            return true;
        }
        return $user instanceof Penduduk && $user->nik === $target->nik;
    }
}
