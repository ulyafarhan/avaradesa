<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Model untuk merepresentasikan data Penduduk (warga) Desa.
 *
 * Tabel: penduduk
 * Primary key: nik (string, non-otomatis)
 * Menyimpan data kependudukan lengkap termasuk identitas, domisili,
 * status perkawinan, dan dokumen identitas (KTP, KK, foto profil).
 *
 * @property  string  $nik  Nomor Induk Kependudukan
 * @property  string  $no_kk  Nomor Kartu Keluarga
 * @property  string  $nama_lengkap  Nama lengkap penduduk
 * @property  string  $tempat_lahir  Tempat lahir
 * @property  \Carbon\Carbon  $tanggal_lahir  Tanggal lahir
 * @property  string  $jenis_kelamin  Jenis kelamin (L/P)
 * @property  string  $agama  Agama
 * @property  string  $pendidikan  Tingkat pendidikan terakhir
 * @property  string  $pekerjaan  Pekerjaan/kegiatan
 * @property  string  $status_perkawinan  Status perkawinan (Belum/Kawin/Cerai)
 * @property  string  $status_keluarga  Hubungan dalam keluarga (Kepala/Istri/Anak/dsb.)
 * @property  string  $status_mutasi  Status kependudukan (Tetap/Pindah/Meninggal)
 * @property  string|null  $telegram_chat_id  ID chat Telegram (tersembunyi)
 * @property  string|null  $foto_profil  Path/URL foto profil
 * @property  string|null  $foto_ktp  Path/URL foto KTP
 * @property  string|null  $foto_kk  Path/URL foto KK
 * @property  \Carbon\Carbon|null  $created_at  Waktu pembuatan record
 * @property  \Carbon\Carbon|null  $updated_at  Waktu pembaruan record
 */
class Penduduk extends Authenticatable
{
    use HasApiTokens, SoftDeletes;

    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var  string
     */
    protected $table = 'penduduk';

    /**
     * Primary key model adalah nik (bukan id).
     *
     * @var  string
     */
    protected $primaryKey = 'nik';

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
        'nik',
        'no_kk',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'agama',
        'pendidikan',
        'pekerjaan',
        'status_perkawinan',
        'status_keluarga',
        'status_mutasi',
        'telegram_chat_id',
        'no_hp',
        'foto_profil',
        'foto_ktp',
        'foto_kk',
        'locked_until',
        'pin_attempts',
        'pin_hash',
        'biometric_key',
        'is_kepala_keluarga',
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi JSON.
     *
     * @var  array<int, string>
     */
    protected $hidden = [
        'telegram_chat_id',
    ];

    /**
     * Casting atribut ke tipe data native PHP.
     *
     * @return  array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Accessor untuk mendapatkan URL foto profil warga secara dinamis.
     *
     * @param  string|null  $value  Path atau URL foto profil.
     * @return  string|null  URL absolut foto profil.
     */
    public function getFotoProfilAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        return \Illuminate\Support\Facades\Storage::url($value);
    }

    /**
     * Accessor untuk mendapatkan URL dokumen KTP warga secara dinamis.
     *
     * @param  string|null  $value  Path atau URL foto KTP.
     * @return  string|null  URL absolut foto KTP.
     */
    public function getFotoKtpAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        return \Illuminate\Support\Facades\Storage::url($value);
    }

    /**
     * Accessor untuk mendapatkan URL dokumen Kartu Keluarga (KK) warga secara dinamis.
     *
     * @param  string|null  $value  Path atau URL foto KK.
     * @return  string|null  URL absolut foto KK.
     */
    public function getFotoKkAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        return \Illuminate\Support\Facades\Storage::url($value);
    }

    /**
     * Relasi ke data Keluarga (Kartu Keluarga) yang menaungi warga ini.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    
    public function agamaRef() { return $this->belongsTo(\App\Models\RefAgama::class, 'agama_id'); }
    public function pendidikanRef() { return $this->belongsTo(\App\Models\RefPendidikan::class, 'pendidikan_id'); }
    public function pekerjaanRef() { return $this->belongsTo(\App\Models\RefPekerjaan::class, 'pekerjaan_id'); }
    public function statusPerkawinanRef() { return $this->belongsTo(\App\Models\RefStatusPerkawinan::class, 'status_perkawinan_id'); }
    public function statusKeluargaRef() { return $this->belongsTo(\App\Models\RefStatusKeluarga::class, 'status_keluarga_id'); }
    public function keluarga()
    {
        return $this->belongsTo(Keluarga::class, 'no_kk', 'no_kk');
    }

    /**
     * Relasi ke seluruh data riwayat mutasi warga ini (kelahiran/kematian/datang/pindah).
     *
     * @return  \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mutasi()
    {
        return $this->hasMany(MutasiPenduduk::class, 'nik', 'nik');
    }

    /**
     * Relasi ke seluruh riwayat pengajuan surat pelayanan oleh warga ini.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pengajuanSurat()
    {
        return $this->hasMany(PengajuanSurat::class, 'nik_pemohon', 'nik');
    }

    /**
     * Accessor untuk mendapatkan umur penduduk secara dinamis berdasarkan tanggal lahir.
     *
     * @return  int  Umur dalam tahun.
     */
    public function getUmurAttribute()
    {
        return $this->tanggal_lahir->age;
    }

    /**
     * Scope query untuk menyaring penduduk tetap yang aktif (tidak meninggal/pindah).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Builder Eloquent.
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAktif($query)
    {
        return $query->where('status_mutasi', 'Tetap');
    }

    /**
     * Scope query untuk menyaring penduduk berjenis kelamin laki-laki.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Builder Eloquent.
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLakiLaki($query)
    {
        return $query->where('jenis_kelamin', 'L');
    }

    /**
     * Scope query untuk menyaring penduduk berjenis kelamin perempuan.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Builder Eloquent.
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function scopePerempuan($query)
    {
        return $query->where('jenis_kelamin', 'P');
    }

    /**
     * Scope query untuk menyaring penduduk berdasarkan wilayah dusun melalui relasi keluarga.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Builder Eloquent.
     * @param  string  $dusun  Nama dusun yang dicari.
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByDusun($query, string $dusun)
    {
        return $query->whereHas('keluarga', function ($q) use ($dusun) {
            $q->where('dusun', $dusun);
        });
    }
}
