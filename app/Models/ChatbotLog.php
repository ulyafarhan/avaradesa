<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

/**
 * Model untuk mencatat riwayat interaksi percakapan warga dengan chatbot Telegram.
 *
 * Tabel: chatbot_logs
 * Menyimpan log lengkap setiap pesan masuk dan balasan AI yang dihasilkan,
 * termasuk penggunaan token untuk pemantauan konsumsi API.
 *
 * @property  string  $id  ULID unik catatan percakapan
 * @property  string  $telegram_chat_id  ID chat Telegram pengguna
 * @property  string  $pesan_masuk  Teks pesan yang dikirim warga
 * @property  string  $balasan_ai  Teks balasan yang dihasilkan AI
 * @property  int     $tokens_used  Jumlah token API yang terpakai
 * @property  \Carbon\Carbon|null  $created_at  Waktu pesan diterima
 */
class ChatbotLog extends Model
{
    use HasUlids;

    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var  string
     */
    protected $table = 'chatbot_logs';

    /**
     * Nonaktifkan timestamps otomatis (created_at di-set manual).
     *
     * @var  bool
     */
    public $timestamps = false;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var  array<int, string>
     */
    protected $fillable = [
        'telegram_chat_id',
        'pesan_masuk',
        'balasan_ai',
        'tokens_used',
    ];

    /**
     * Casting atribut ke tipe data native PHP.
     *
     * @return  array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tokens_used' => 'integer',
            'created_at' => 'datetime',
        ];
    }
}
