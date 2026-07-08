<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Facades\Cache;

/**
 * Model untuk merepresentasikan basis pengetahuan (knowledge base) chatbot Telegram.
 *
 * Tabel: bot_knowledges
 * Menyimpan pasangan pertanyaan/jawaban serta kata kunci yang digunakan
 * chatbot untuk merespons pesan warga secara otomatis.
 *
 * @property  string  $id  ULID unik catatan pengetahuan
 * @property  string  $kunci  Kunci unik entri pengetahuan
 * @property  string  $tipe  Tipe pengetahuan (faq, perintah, info, dsb.)
 * @property  string  $pertanyaan_atau_topik  Pertanyaan atau topik terkait
 * @property  array   $kata_kunci  Daftar kata kunci pencocokan (JSON)
 * @property  string  $jawaban_atau_konten  Teks jawaban yang dikirim bot
 * @property  bool    $is_aktif  Status aktif/tidaknya entri ini
 * @property  \Carbon\Carbon|null  $created_at  Waktu pembuatan entri
 * @property  \Carbon\Carbon|null  $updated_at  Waktu pembaruan entri
 */
class BotKnowledge extends Model
{
    use HasUlids;

    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var  string
     */
    protected $table = 'bot_knowledges';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var  array<int, string>
     */
    protected $fillable = [
        'kunci',
        'tipe',
        'pertanyaan_atau_topik',
        'kata_kunci',
        'jawaban_atau_konten',
        'is_aktif',
    ];

    /**
     * Boot model — registrasi event listener untuk invalidasi cache.
     *
     * @return  void
     */
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('telegram_bot_knowledges'));
        static::deleted(fn () => Cache::forget('telegram_bot_knowledges'));
    }

    /**
     * Casting atribut ke tipe data native PHP.
     *
     * @return  array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kata_kunci' => 'array',
            'is_aktif' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }
}
