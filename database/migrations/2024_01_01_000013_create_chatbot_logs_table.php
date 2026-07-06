<?php

/**
 * MIGRASI — Tabel Log Chatbot
 *
 * Mencatat riwayat percakapan antara warga dan Asisten Virtual
 * Desa melalui Telegram, termasuk pesan, balasan AI, dan
 * pemakaian token.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Buat tabel `chatbot_logs`.
     *
     * - id               : ULID (PK)
     * - telegram_chat_id : ID chat Telegram pengirim (contoh: '123456789')
     * - pesan_masuk      : Pesan yang diketik warga ke bot
     * - balasan_ai       : Jawaban yang dihasilkan oleh AI (Gemini/OpenAI)
     * - tokens_used      : Jumlah token AI yang terpakai untuk sesi ini (default: 0)
     * - created_at       : Waktu percakapan
     *
     * Index: telegram_chat_id
     */
    public function up(): void
    {
        Schema::create('chatbot_logs', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('telegram_chat_id', 50);
            $table->text('pesan_masuk');
            $table->text('balasan_ai');
            $table->integer('tokens_used')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->index('telegram_chat_id');
        });
    }

    /**
     * Hapus tabel `chatbot_logs`.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_logs');
    }
};
