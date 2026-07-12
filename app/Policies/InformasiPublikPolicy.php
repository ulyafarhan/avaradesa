<?php

namespace App\Policies;

use App\Models\Administrator;

class InformasiPublikPolicy
{
    public function viewAny(): bool
    {
        return true;
    }

    public function create(Administrator $admin): bool
    {
        return in_array($admin->role, ['kepala_desa', 'sekdes', 'operator']);
    }

    public function update(Administrator $admin): bool
    {
        return in_array($admin->role, ['kepala_desa', 'sekdes', 'operator']);
    }

    public function delete(Administrator $admin): bool
    {
        return in_array($admin->role, ['kepala_desa', 'sekdes']);
    }
}
