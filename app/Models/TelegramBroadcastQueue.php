<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

/**
 * Model untuk merepresentasikan antrean pengiriman pesan massal (broadcast) Telegram warga.
 *
 * Tabel: telegram_broadcast_queue
 * Menyimpan draf pesan siaran yang akan dikirim ke warga berdasarkan
 * kategori target (semua warga, per dusun, per RT, dsb.) sesuai jadwal.
 *
 * @property  string  $id  ULID unik antrean broadcast
 * @property  string  $pesan  Teks pesan siaran
 * @property  string  $kategori_target  Target penerima (semua, dusun_x, rt_x, dsb.)
 * @property  string  $status  Status antrean (Queued, Sending, Sent, Failed)
 * @property  \Carbon\Carbon|null  $jadwal_kirim  Waktu terjadwal pengiriman
 * @property  \Carbon\Carbon|null  $waktu_selesai  Waktu pengiriman selesai
 * @property  string  $created_by  ULID administrator pembuat pesan
 */
class TelegramBroadcastQueue extends Model
{
    use HasUlids;

    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var  string
     */
    protected $table = 'telegram_broadcast_queue';

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
        'pesan',
        'kategori_target',
        'status',
        'jadwal_kirim',
        'waktu_selesai',
        'created_by',
    ];

    /**
     * Casting atribut ke tipe data native PHP.
     *
     * @return  array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jadwal_kirim' => 'datetime',
            'waktu_selesai' => 'datetime',
        ];
    }

    /**
     * Relasi ke administrator pembuat draf pesan broadcast ini.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(Administrator::class, 'created_by');
    }

    /**
     * Scope query untuk menyaring draf pesan siaran yang masih masuk antrean.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Builder Eloquent.
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function scopeQueued($query)
    {
        return $query->where('status', 'Queued');
    }

    /**
     * Scope query untuk menyaring draf pesan siaran yang siap dikirim saat ini.
     *
     * Pesan dikatakan "ready" jika statusnya Queued dan jadwal kirim sudah terlewati.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Builder Eloquent.
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function scopeReady($query)
    {
        return $query->where('status', 'Queued')
                    ->where('jadwal_kirim', '<=', now());
    }
}
