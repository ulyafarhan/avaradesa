<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE administrators MODIFY COLUMN role ENUM('kepala_desa', 'sekdes', 'operator') DEFAULT 'operator'");
        }

        DB::table('pengaturan_desa')
            ->where('kunci', 'nama_kepala_desa')
            ->update(['deskripsi' => 'Nama lengkap Kepala Desa']);

        DB::table('pengaturan_desa')
            ->where('kunci', 'nip_kepala_desa')
            ->update(['deskripsi' => 'NIP Kepala Desa (jika PNS)']);

        DB::table('pengaturan_frontend')
            ->where('kunci', 'foto_kepala_desa')
            ->update(['deskripsi' => 'Foto Resmi Kepala Desa']);
    }

    public function down(): void
    {
        DB::table('pengaturan_desa')
            ->where('kunci', 'nama_kepala_desa')
            ->update(['deskripsi' => 'Nama lengkap Kepala Desa']);
    }
};
