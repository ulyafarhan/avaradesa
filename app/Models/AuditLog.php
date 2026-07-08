<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

/**
 * Model untuk mencatat log riwayat audit aktivitas pengguna/sistem (audit trails).
 *
 * Tabel: audit_logs
 * Tidak menggunakan timestamps bawaan (created_at di-set manual).
 *
 * @property  string  $id  ULID unik catatan audit
 * @property  string|null  $user_type  Tipe pengguna (admin, warga, system)
 * @property  string|null  $user_id  ID pengguna yang melakukan aksi
 * @property  string  $tindakan  Jenis tindakan (create, update, delete)
 * @property  string  $nama_tabel  Nama tabel database yang terdampak
 * @property  string|null  $record_id  ID record yang dimanipulasi
 * @property  array|null  $data_lama  Data sebelum perubahan (JSON)
 * @property  array|null  $data_baru  Data setelah perubahan (JSON)
 * @property  string|null  $ip_address  Alamat IP pengguna
 * @property  string|null  $user_agent  User-agent browser/perangkat
 * @property  \Carbon\Carbon|null  $created_at  Waktu pencatatan audit
 */
class AuditLog extends Model
{
    use HasUlids;

    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var  string
     */
    protected $table = 'audit_logs';

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
        'user_type',
        'user_id',
        'tindakan',
        'nama_tabel',
        'record_id',
        'data_lama',
        'data_baru',
        'ip_address',
        'user_agent',
    ];

    /**
     * Casting atribut ke tipe data native PHP.
     *
     * @return  array<string, string>
     */
    protected function casts(): array
    {
        return [
            'data_lama' => 'array',
            'data_baru' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Membuat catatan log aktivitas audit baru secara statis.
     *
     * @param  string       $userType   Tipe pengguna ('admin', 'warga', atau 'system').
     * @param  string|null  $userId     ID pengguna yang melakukan aksi.
     * @param  string       $tindakan   Jenis tindakan (create, update, delete).
     * @param  string       $namaTabel  Nama tabel database yang terdampak.
     * @param  string|null  $recordId   ID record yang dimanipulasi.
     * @param  array|null   $dataLama   Data sebelum perubahan.
     * @param  array|null   $dataBaru   Data setelah perubahan.
     * @return  void
     */
    public static function log(
        string $userType,
        ?string $userId,
        string $tindakan,
        string $namaTabel,
        ?string $recordId = null,
        ?array $dataLama = null,
        ?array $dataBaru = null
    ): void {
        static::create([
            'user_type' => $userType,
            'user_id' => $userId,
            'tindakan' => $tindakan,
            'nama_tabel' => $namaTabel,
            'record_id' => $recordId,
            'data_lama' => $dataLama,
            'data_baru' => $dataBaru,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
