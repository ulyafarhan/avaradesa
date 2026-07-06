<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom no_hp ke tabel penduduk.
 *
 * Kolom ini digunakan untuk menyimpan nomor WhatsApp warga dalam format
 * internasional (contoh: 62812xxxx) sebagai target notifikasi WhatsApp.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            $table->string('no_hp', 20)->nullable()->after('telegram_chat_id');
        });
    }

    public function down(): void
    {
        Schema::table('penduduk', function (Blueprint $table) {
            $table->dropColumn('no_hp');
        });
    }
};
