<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migrasi untuk menambahkan indeks performa pada kolom created_at di tabel chatbot_logs
 * agar pembersihan data berkala (system cleanup) berjalan cepat dan tidak mengunci tabel.
 */
return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        try {
            Schema::table('chatbot_logs', function (Blueprint $table) {
                $table->index('created_at');
            });
        } catch (\Exception $e) {}
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        try {
            Schema::table('chatbot_logs', function (Blueprint $table) {
                $table->dropIndex(['created_at']);
            });
        } catch (\Exception $e) {}
    }
};
