<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keluarga', function (Blueprint $table) {
            $table->index('kepala_keluarga_nik');
        });

        Schema::table('mutasi_penduduk', function (Blueprint $table) {
            $table->index('nik');
            $table->index('diverifikasi_oleh');
            $table->index('created_at');
            $table->index(['status_verifikasi', 'created_at']);
            $table->index(['nik', 'created_at']);
        });

        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->index('kategori_surat_id');
            $table->index('diverifikasi_oleh');
            $table->index(['status', 'created_at']);
            $table->index(['kategori_surat_id', 'created_at']);
        });

        Schema::table('informasi_publik', function (Blueprint $table) {
            $table->index('author_id');
            $table->index('created_at');
            $table->index(['is_published', 'created_at']);
            $table->index(['is_published', 'kategori', 'created_at']);
        });

        Schema::table('tracking_pengajuan_surat', function (Blueprint $table) {
            $table->index('diupdate_oleh');
        });

        Schema::table('telegram_broadcast_queue', function (Blueprint $table) {
            $table->index('created_by');
        });

        Schema::table('bot_knowledges', function (Blueprint $table) {
            $table->index('is_aktif');
        });

        Schema::table('inventaris_fasilitas', function (Blueprint $table) {
            $table->index('is_publik');
            $table->index('jenis_fasilitas');
            $table->index('created_at');
            $table->index(['is_publik', 'jenis_fasilitas']);
        });

        Schema::table('traffic_logs', function (Blueprint $table) {
            $table->index('is_bot');
        });

        Schema::table('chatbot_logs', function (Blueprint $table) {
            $table->index(['telegram_chat_id', 'created_at']);
        });

        Schema::table('penduduk', function (Blueprint $table) {
            $table->index(['no_kk', 'nik']);
        });
    }

    public function down(): void
    {
        Schema::table('keluarga', function (Blueprint $table) {
            $table->dropIndex(['kepala_keluarga_nik']);
        });

        Schema::table('mutasi_penduduk', function (Blueprint $table) {
            $table->dropIndex(['nik']);
            $table->dropIndex(['diverifikasi_oleh']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['status_verifikasi', 'created_at']);
            $table->dropIndex(['nik', 'created_at']);
        });

        Schema::table('pengajuan_surat', function (Blueprint $table) {
            $table->dropIndex(['kategori_surat_id']);
            $table->dropIndex(['diverifikasi_oleh']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['kategori_surat_id', 'created_at']);
        });

        Schema::table('informasi_publik', function (Blueprint $table) {
            $table->dropIndex(['author_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_published', 'created_at']);
            $table->dropIndex(['is_published', 'kategori', 'created_at']);
        });

        Schema::table('tracking_pengajuan_surat', function (Blueprint $table) {
            $table->dropIndex(['diupdate_oleh']);
        });

        Schema::table('telegram_broadcast_queue', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
        });

        Schema::table('bot_knowledges', function (Blueprint $table) {
            $table->dropIndex(['is_aktif']);
        });

        Schema::table('inventaris_fasilitas', function (Blueprint $table) {
            $table->dropIndex(['is_publik']);
            $table->dropIndex(['jenis_fasilitas']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_publik', 'jenis_fasilitas']);
        });

        Schema::table('traffic_logs', function (Blueprint $table) {
            $table->dropIndex(['is_bot']);
        });

        Schema::table('chatbot_logs', function (Blueprint $table) {
            $table->dropIndex(['telegram_chat_id', 'created_at']);
        });

        Schema::table('penduduk', function (Blueprint $table) {
            $table->dropIndex(['no_kk', 'nik']);
        });
    }
};
