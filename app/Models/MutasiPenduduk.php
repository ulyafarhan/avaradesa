<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

/**
 * Model untuk merepresentasikan data pelaporan mutasi warga (kelahiran, kematian, datang, pindah).
 *
 * Tabel: mutasi_penduduk
 * Primary key: ULID (bukan auto-increment)
 * Mencatat setiap perubahan status kependudukan beserta dokumen bukti
 * dan status verifikasi oleh perangkat desa.
 *
 * @property  string  $id  ULID unik laporan mutasi
 * @property  string  $nik  NIK penduduk yang bermutasi
 * @property  string  $jenis_mutasi  Jenis mutasi (kelahiran, kematian, datang, pindah)
 * @property  \Carbon\Carbon  $tanggal_mutasi  Tanggal kejadian mutasi
 * @property  string|null  $keterangan  Keterangan tambahan
 * @property  string|null  $dokumen_bukti  Path file dokumen bukti
 * @property  string  $status_verifikasi  Status verifikasi (Pending, Disetujui, Ditolak)
 * @property  string|null  $diverifikasi_oleh  ULID administrator verifikator
 * @property  \Carbon\Carbon|null  $created_at  Waktu pencatatan laporan
 */
class MutasiPenduduk extends Model
{
    use HasUlids;

    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var  string
     */
    protected $table = 'mutasi_penduduk';

    /**
     * Primary key bukan auto-incrementing (ULID string).
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
        'nik',
        'jenis_mutasi',
        'tanggal_mutasi',
        'keterangan',
        'dokumen_bukti',
        'status_verifikasi',
        'diverifikasi_oleh',
    ];

    /**
     * Casting atribut ke tipe data native PHP.
     *
     * @return  array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mutasi' => 'date',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke data penduduk yang bersangkutan dengan laporan mutasi ini.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function penduduk()
    {
        return $this->belongsTo(Penduduk::class, 'nik', 'nik');
    }

    /**
     * Relasi ke administrator yang memproses verifikasi laporan mutasi ini.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function verifikator()
    {
        return $this->belongsTo(Administrator::class, 'diverifikasi_oleh');
    }

    /**
     * Scope query untuk menyaring laporan mutasi yang masih berstatus pending.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Builder Eloquent.
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status_verifikasi', 'Pending');
    }
}
