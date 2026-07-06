<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->index(['nik_pemohon', 'created_at'], 'idx_pengajuan_nik_created');
        });

        Schema::table('tracking_pengajuan_surat', function (Blueprint $table) {
            $table->index(['pengajuan_surat_id', 'created_at'], 'idx_tracking_surat_created');
        });

        Schema::table('penduduk', function (Blueprint $table) {
            $table->index(['status_mutasi', 'pendidikan'], 'idx_penduduk_status_pendidikan');
            $table->index(['status_mutasi', 'pekerjaan'], 'idx_penduduk_status_pekerjaan');
        });

        Schema::table('keluarga', function (Blueprint $table) {
            $table->index('dusun', 'idx_keluarga_dusun');
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_surat', fn(Blueprint $t) => $t->dropIndex('idx_pengajuan_nik_created'));
        Schema::table('tracking_pengajuan_surat', fn(Blueprint $t) => $t->dropIndex('idx_tracking_surat_created'));
        Schema::table('penduduk', fn(Blueprint $t) => $t->dropIndex(['idx_penduduk_status_pendidikan', 'idx_penduduk_status_pekerjaan']));
        Schema::table('keluarga', fn(Blueprint $t) => $t->dropIndex('idx_keluarga_dusun'));
    }
};
