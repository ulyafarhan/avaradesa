<?php

namespace App\Contracts\AppApi;

use App\Models\Administrator;
use App\Models\Penduduk;
use Illuminate\Http\Request;

interface TokenServiceInterface
{
    public function createWargaToken(Penduduk $penduduk): string;

    public function createAdminToken(Administrator $admin): string;

    public function revokeCurrentToken(Request $request): void;
}
