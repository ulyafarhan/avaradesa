<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Migrasi untuk menambahkan indeks performa pada kolom yang sering dicari,
 * disaring, atau dikelompokkan guna mengoptimalkan query database.
 * Menggunakan pengaman (try-catch) untuk menghindari error DDL parsial dari jalannya migrasi sebelumnya.
 */
return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambahkan indeks.
     */
    public function up(): void
    {
        // 1. Tabel Penduduk
        try {
            Schema::table('penduduk', function (Blueprint $table) {
                $table->index('status_mutasi');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('penduduk', function (Blueprint $table) {
                $table->index('jenis_kelamin');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('penduduk', function (Blueprint $table) {
                $table->index('tanggal_lahir');
            });
        } catch (\Exception $e) {}

        // 2. Tabel Keluarga
        try {
            Schema::table('keluarga', function (Blueprint $table) {
                $table->index('dusun');
            });
        } catch (\Exception $e) {}

        // 3. Tabel Pengajuan Surat
        try {
            Schema::table('pengajuan_surat', function (Blueprint $table) {
                $table->index('created_at');
            });
        } catch (\Exception $e) {}

        // 4. Tabel Mutasi Penduduk
        try {
            Schema::table('mutasi_penduduk', function (Blueprint $table) {
                $table->index('jenis_mutasi');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('mutasi_penduduk', function (Blueprint $table) {
                $table->index('tanggal_mutasi');
            });
        } catch (\Exception $e) {}
    }

    /**
     * Batalkan migrasi untuk menghapus indeks.
     */
    public function down(): void
    {
        try {
            Schema::table('penduduk', function (Blueprint $table) {
                $table->dropIndex(['status_mutasi']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('penduduk', function (Blueprint $table) {
                $table->dropIndex(['jenis_kelamin']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('penduduk', function (Blueprint $table) {
                $table->dropIndex(['tanggal_lahir']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('keluarga', function (Blueprint $table) {
                $table->dropIndex(['dusun']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('pengajuan_surat', function (Blueprint $table) {
                $table->dropIndex(['created_at']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('mutasi_penduduk', function (Blueprint $table) {
                $table->dropIndex(['jenis_mutasi']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('mutasi_penduduk', function (Blueprint $table) {
                $table->dropIndex(['tanggal_mutasi']);
            });
        } catch (\Exception $e) {}
    }
};
