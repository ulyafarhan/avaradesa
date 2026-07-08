<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk merepresentasikan master data referensi wilayah administrasi negara.
 *
 * Tabel: referensi_wilayah
 * Primary key: kode_wilayah (string, non-otomatis)
 * Menyimpan data hierarki wilayah dari tingkat provinsi hingga desa/kelurahan,
 * dengan relasi self-referencing untuk parent dan children.
 *
 * @property  string  $kode_wilayah  Kode wilayah unik (BPS/BPSDM)
 * @property  string  $nama_wilayah  Nama wilayah administrasi
 * @property  int     $level  Level hierarki (1=provinsi, 2=kabupaten, 3=kecamatan, 4=desa)
 * @property  string|null  $parent_kode  Kode wilayah induk (null untuk level tertinggi)
 */
class ReferensiWilayah extends Model
{
    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var  string
     */
    protected $table = 'referensi_wilayah';

    /**
     * Primary key model adalah kode_wilayah (bukan id).
     *
     * @var  string
     */
    protected $primaryKey = 'kode_wilayah';

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
        'kode_wilayah',
        'nama_wilayah',
        'level',
        'parent_kode',
    ];

    /**
     * Relasi ke wilayah atasan (parent wilayah).
     *
     * @return  \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(ReferensiWilayah::class, 'parent_kode', 'kode_wilayah');
    }

    /**
     * Relasi ke seluruh sub-wilayah bawahan (children wilayah).
     *
     * @return  \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children()
    {
        return $this->hasMany(ReferensiWilayah::class, 'parent_kode', 'kode_wilayah');
    }
}
