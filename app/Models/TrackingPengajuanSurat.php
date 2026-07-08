<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

/**
 * Model untuk merepresentasikan log riwayat perubahan status (tracking) dari pengajuan surat warga.
 *
 * Tabel: tracking_pengajuan_surat
 * Primary key: ULID (bukan auto-increment)
 * Mencatat setiap transisi status pengajuan surat beserta keterangan
 * dan identitas administrator yang melakukan perubahan.
 *
 * @property  string  $id  ULID unik catatan tracking
 * @property  string  $pengajuan_surat_id  ULID pengajuan surat terkait
 * @property  string|null  $status_sebelumnya  Status sebelum perubahan
 * @property  string  $status_baru  Status setelah perubahan
 * @property  string|null  $keterangan_update  Catatan/keterangan perubahan
 * @property  string|null  $diupdate_oleh  ULID administrator yang melakukan update
 * @property  \Carbon\Carbon|null  $created_at  Waktu pencatatan perubahan
 */
class TrackingPengajuanSurat extends Model
{
    use HasUlids;

    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var  string
     */
    protected $table = 'tracking_pengajuan_surat';

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
        'pengajuan_surat_id',
        'status_sebelumnya',
        'status_baru',
        'keterangan_update',
        'diupdate_oleh',
    ];

    /**
     * Casting atribut ke tipe data native PHP.
     *
     * @return  array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke data berkas pengajuan surat yang ditracking.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pengajuan()
    {
        return $this->belongsTo(PengajuanSurat::class, 'pengajuan_surat_id');
    }

    /**
     * Relasi ke data administrator yang melakukan perubahan status.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updater()
    {
        return $this->belongsTo(Administrator::class, 'diupdate_oleh');
    }
}
