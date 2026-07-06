<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jalankan migrasi standarisasi kunci pengaturan desa.
     */
    public function up(): void
    {
        // Pastikan deskripsi dan kunci pengaturan desa terstandarisasi nasional
        DB::table('pengaturan_desa')
            ->where('kunci', 'jam_pelayanan')
            ->update([
                'deskripsi' => 'Jam pelayanan kantor desa'
            ]);

        DB::table('pengaturan_desa')
            ->where('kunci', 'nomor_rekening')
            ->update([
                'deskripsi' => 'Nomor rekening kas desa'
            ]);

        DB::table('pengaturan_desa')
            ->where('kunci', 'nama_bank')
            ->update([
                'deskripsi' => 'Nama bank kas desa'
            ]);
    }

    /**
     * Kembalikan migrasi.
     */
    public function down(): void
    {
        DB::table('pengaturan_desa')
            ->where('kunci', 'nama_desa')
            ->update([
                'deskripsi' => 'Nama resmi desa'
            ]);
    }
};
