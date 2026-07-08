<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

/**
 * Model untuk mencatat log kunjungan/traffic website.
 *
 * Tabel: traffic_logs
 * Mencatat setiap request HTTP yang masuk ke situs untuk analitik
 * kunjungan, termasuk deteksi bot/crawler.
 *
 * @property  string  $id  ULID unik catatan traffic
 * @property  string  $ip_address  Alamat IP pengunjung
 * @property  string|null  $user_agent  User-agent browser/perangkat
 * @property  string  $path  Path URL yang diakses
 * @property  string  $method  HTTP method (GET, POST, PUT, DELETE)
 * @property  string|null  $referer  URL referer (sumber kunjungan)
 * @property  bool    $is_bot  Apakah pengunjung terdeteksi sebagai bot/crawler
 * @property  \Carbon\Carbon|null  $created_at  Waktu request diterima
 */
class TrafficLog extends Model
{
    use HasUlids;

    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var  string
     */
    protected $table = 'traffic_logs';

    /**
     * Nonaktifkan timestamps otomatis.
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
        'ip_address',
        'user_agent',
        'path',
        'method',
        'referer',
        'is_bot',
    ];

    /**
     * Casting atribut ke tipe data native PHP.
     *
     * @return  array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_bot' => 'boolean',
            'created_at' => 'datetime',
        ];
    }
}
