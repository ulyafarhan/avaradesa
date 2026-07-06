<?php

/**
 * MIGRASI — Tabel Audit Logs
 *
 * Mencatat semua aktivitas CRUD yang dilakukan oleh admin maupun
 * warga di dalam sistem, sebagai jejak audit untuk keamanan dan
 * akuntabilitas.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `audit_logs`.
     *
     * - id            : ULID (PK)
     * - user_type     : Jenis pelaku — 'admin' | 'warga'
     * - user_id       : ID pengguna (ULID admin atau NIK warga), nullable
     * - tindakan      : Aksi yang dilakukan (contoh: 'CREATE', 'UPDATE', 'DELETE')
     * - nama_tabel    : Nama tabel yang dimodifikasi
     * - record_id     : ID record yang dimodifikasi (nullable)
     * - data_lama     : JSON — nilai record sebelum perubahan (nullable)
     * - data_baru     : JSON — nilai record setelah perubahan (nullable)
     * - ip_address    : Alamat IP pelaku (IPv4/IPv6, 45 char)
     * - user_agent    : Informasi browser/klien (nullable)
     * - created_at    : Waktu kejadian
     *
     * Index: (nama_tabel, record_id), created_at
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->enum('user_type', ['admin', 'warga']);
            $table->string('user_id', 50)->nullable();
            $table->string('tindakan', 50);
            $table->string('nama_tabel', 50);
            $table->string('record_id', 50)->nullable();
            $table->json('data_lama')->nullable();
            $table->json('data_baru')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['nama_tabel', 'record_id']);
            $table->index('created_at');
        });
    }

    /**
     * Hapus tabel `audit_logs`.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
