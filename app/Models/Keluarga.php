<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk merepresentasikan data Kartu Keluarga (KK).
 *
 * Tabel: keluarga
 * Primary key: no_kk (string, non-otomatis)
 * Menyimpan data kepemilikan Kartu Keluarga beserta alamat dan
 * informasi dusun/RT-RW wilayah domisili keluarga.
 *
 * @property  string  $no_kk  Nomor Kartu Keluarga
 * @property  string  $alamat  Alamat lengkap rumah
 * @property  string  $dusun  Nama dusun/kepala desa
 * @property  string  $rt_rw  Nomor RT/RW
 * @property  string  $kepala_keluarga_nik  NIK kepala keluarga
 */
class Keluarga extends Model
{
    use HasFactory;

    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var  string
     */
    protected $table = 'keluarga';

    /**
     * Primary key model adalah no_kk (bukan id).
     *
     * @var  string
     */
    protected $primaryKey = 'no_kk';

    /**
     * Primary key bukan auto-incrementing (string).
     *
     * @var  bool
     */
    public $incrementing = false;

    /**
     * Tipe primary key adalah string.
     *
     * @var  string
     */
    protected $keyType = 'string';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var  array<int, string>
     */
    protected $fillable = [
        'no_kk',
        'alamat',
        'dusun',
        'rt_rw',
        'kepala_keluarga_nik',
    ];

    /**
     * Relasi ke seluruh anggota keluarga (warga) yang terdaftar dalam KK ini.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function anggota()
    {
        return $this->hasMany(Penduduk::class, 'no_kk', 'no_kk');
    }

    /**
     * Relasi ke penduduk yang bertindak sebagai Kepala Keluarga.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kepalaKeluarga()
    {
        return $this->belongsTo(Penduduk::class, 'kepala_keluarga_nik', 'nik');
    }

    /**
     * Scope query untuk memfilter keluarga berdasarkan wilayah dusun.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Builder Eloquent.
     * @param  string  $dusun  Nama dusun yang dicari.
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDusun($query, string $dusun)
    {
        return $query->where('dusun', $dusun);
    }
}
