<?php

namespace App\Policies;

use App\Models\Administrator;

class AdministratorPolicy
{
    public function viewAny(Administrator $admin): bool
    {
        return $admin->role === 'kepala_desa';
    }

    public function create(Administrator $admin): bool
    {
        return $admin->role === 'kepala_desa';
    }

    public function update(Administrator $admin): bool
    {
        return $admin->role === 'kepala_desa';
    }

    public function delete(Administrator $admin): bool
    {
        return $admin->role === 'kepala_desa';
    }
}
