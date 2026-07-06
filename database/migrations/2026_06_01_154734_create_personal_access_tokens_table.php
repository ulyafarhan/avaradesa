<?php

/**
 * MIGRASI — Tabel Personal Access Tokens (Sanctum)
 *
 * Tabel bawaan Laravel Sanctum untuk menyimpan token API.
 * Digunakan untuk autentikasi warga (via NIK) dan admin
 * (via username/password). Menggunakan ULID sebagai morph key.
 *
 * @see https://laravel.com/docs/sanctum
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `personal_access_tokens`.
     *
     * - id             : Auto-increment (PK)
     * - tokenable_type : Model pemilik token (polymorphic: Penduduk / Administrator)
     * - tokenable_id   : ULID pemilik token (menggunakan ulidMorphs)
     * - name           : Nama token (contoh: 'auth-token-warga', 'auth-token-admin')
     * - token          : Hash token 64 karakter (unique)
     * - abilities      : JSON — daftar kemampuan token (nullable, '*' untuk semua)
     * - last_used_at   : Waktu terakhir token digunakan (nullable)
     * - expires_at     : Waktu kadaluwarsa token (nullable, ter-index)
     * - timestamps     : created_at & updated_at
     */
    public function up(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->ulidMorphs('tokenable');
            $table->text('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Hapus tabel `personal_access_tokens`.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_access_tokens');
    }
};
