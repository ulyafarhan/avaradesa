<?php

/**
 * SEEDER — Administrator Awal
 *
 * Mengisi data administrator default untuk sistem SIG-Udeung.
 * Terdapat tiga role: Kepala Desa (kepala desa), Sekdes (sekretaris),
 * dan Operator (petugas administrasi).
 *
 * @see \App\Models\Administrator
 */

namespace Database\Seeders;

use App\Models\Administrator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdministratorSeeder extends Seeder
{
    /**
     * Jalankan seeder — buat 3 akun admin default.
     *
     * - kepala desa  : Kepala Desa, akses penuh semua fitur
     * - sekdes   : Sekretaris Desa, akses verifikasi dan pengelolaan
     * - operator : Petugas administrasi, akses input data dan pelayanan
     *
     * Semua password default: 'password123'
     * Segera ubah password setelah login pertama.
     */
    public function run(): void
    {
        $administrators = [
            [
                'username' => 'kepala desa',
                'password' => Hash::make('password123'),
                'role' => 'kepala_desa',
            ],
            [
                'username' => 'sekdes',
                'password' => Hash::make('password123'),
                'role' => 'sekdes',
            ],
            [
                'username' => 'operator',
                'password' => Hash::make('password123'),
                'role' => 'operator',
            ],
        ];

        foreach ($administrators as $admin) {
            Administrator::create($admin);
        }
    }
}
