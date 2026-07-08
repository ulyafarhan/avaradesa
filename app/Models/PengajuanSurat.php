<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

/**
 * Model untuk merepresentasikan data permohonan pengajuan surat warga.
 *
 * Tabel: pengajuan_surat
 * Primary key: ULID (bukan auto-increment)
 * Menyimpan seluruh berkas pengajuan surat beserta data isian form,
 * file syarat, status proses, dan nomor surat resmi yang dihasilkan.
 *
 * @property  string  $id  ULID unik pengajuan surat
 * @property  string  $nomor_registrasi  Nomor registrasi harian otomatis (YYYYMMDD-XXXX)
 * @property  string  $nik_pemohon  NIK pemohon/warga pengaju
 * @property  string  $kategori_surat_id  ULID kategori/jenis surat
 * @property  array   $data_isian  Data isian form pengajuan (JSON)
 * @property  array   $file_syarat  Daftar file syarat dokumen (JSON)
 * @property  string  $status  Status pengajuan (Pending, Diproses, Disetujui, Ditolak, Selesai)
 * @property  string|null  $catatan_penolakan  Alasan penolakan jika status Ditolak
 * @property  string|null  $qr_hash  Hash QR code verifikasi surat
 * @property  string|null  $file_pdf_url  URL file PDF surat jadi
 * @property  string|null  $nomor_surat  Nomor surat resmi yang diterbitkan
 * @property  string|null  $diverifikasi_oleh  ULID administrator verifikator
 * @property  \Carbon\Carbon|null  $created_at  Waktu pengajuan dibuat
 * @property  \Carbon\Carbon|null  $updated_at  Waktu pengajuan diperbarui
 */
class PengajuanSurat extends Model
{
    use HasUlids;

    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var  string
     */
    protected $table = 'pengajuan_surat';

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
     * Atribut yang dapat diisi secara massal.
     *
     * @var  array<int, string>
     */
    protected $fillable = [
        'nomor_registrasi',
        'nik_pemohon',
        'kategori_surat_id',
        'data_isian',
        'file_syarat',
        'status',
        'catatan_penolakan',
        'qr_hash',
        'file_pdf_url',
        'nomor_surat',
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
            'data_isian' => 'array',
            'file_syarat' => 'array',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Relasi ke data penduduk yang mengajukan surat ini.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pemohon()
    {
        return $this->belongsTo(Penduduk::class, 'nik_pemohon', 'nik');
    }

    /**
     * Relasi ke kategori/template surat yang diajukan.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kategori()
    {
        return $this->belongsTo(KategoriSurat::class, 'kategori_surat_id');
    }

    /**
     * Relasi ke administrator yang menolak/menyetujui pengajuan surat ini.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function verifikator()
    {
        return $this->belongsTo(Administrator::class, 'diverifikasi_oleh');
    }

    /**
     * Relasi ke kumpulan log riwayat perubahan status (tracking) pengajuan ini.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tracking()
    {
        return $this->hasMany(TrackingPengajuanSurat::class, 'pengajuan_surat_id');
    }

    /**
     * Scope query untuk menyaring surat yang masih berstatus pending.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Builder Eloquent.
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope query untuk menyaring surat yang sedang diproses.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Builder Eloquent.
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDiproses($query)
    {
        return $query->where('status', 'Diproses');
    }

    /**
     * Boot model — registrasi event listener untuk auto-generate nomor registrasi.
     *
     * @return  void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_registrasi)) {
                $model->nomor_registrasi = static::generateNomorRegistrasi();
            }
        });
    }

    /**
     * Menghasilkan nomor registrasi pengajuan surat harian secara otomatis.
     *
     * Format: YYYYMMDD-XXXX (contoh: 20260711-0001)
     * Nomor urut direset setiap hari.
     *
     * @return  string  Nomor registrasi unik.
     */
    public static function generateNomorRegistrasi(): string
    {
        $prefix = date('Ymd');
        $count = static::whereDate('created_at', today())->count() + 1;
        return sprintf('%s-%04d', $prefix, $count);
    }
}
