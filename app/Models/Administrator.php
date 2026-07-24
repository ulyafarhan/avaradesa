<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Laravel\Sanctum\HasApiTokens;

/**
 * Model untuk merepresentasikan data Administrator (perangkat desa) dalam sistem desa.
 *
 * Tabel: administrators
 *
 * @property  string  $id  ULID unik administrator
 * @property  string  $username  Username untuk login ke panel admin
 * @property  string  $password  Hashed password autentikasi
 * @property  string  $role  Peran administrator (kepala desa, sekdes, operator)
 * @property  \Carbon\Carbon|null  $created_at  Waktu pembuatan akun
 */
class Administrator extends Authenticatable implements FilamentUser
{
    use HasFactory, HasApiTokens, Notifiable, HasUlids;

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var  array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'role',
    ];

    /**
     * Atribut yang disembunyikan saat serialisasi JSON.
     *
     * @var  array<int, string>
     */
    protected $hidden = [
        'password',
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
     * Relasi ke seluruh pengajuan surat yang diverifikasi oleh administrator ini.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pengajuanSurat()
    {
        return $this->hasMany(PengajuanSurat::class, 'diverifikasi_oleh');
    }

    /**
     * Relasi ke seluruh laporan mutasi penduduk yang diverifikasi oleh administrator ini.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function mutasiPenduduk()
    {
        return $this->hasMany(MutasiPenduduk::class, 'diverifikasi_oleh');
    }

    /**
     * Relasi ke seluruh artikel informasi publik yang ditulis oleh administrator ini.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function informasiPublik()
    {
        return $this->hasMany(InformasiPublik::class, 'author_id');
    }

    /**
     * Relasi ke seluruh antrean pesan siaran Telegram yang dibuat oleh administrator ini.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function telegramBroadcasts()
    {
        return $this->hasMany(TelegramBroadcastQueue::class, 'created_by');
    }

    /**
     * Menentukan hak akses administrator untuk masuk ke panel admin Filament.
     *
     * @param  \Filament\Panel  $panel  Panel Filament yang sedang diakses.
     * @return  bool  True jika role termasuk kepala desa/sekdes/operator.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return in_array($this->role, ['kepala_desa', 'sekdes', 'operator'], true);
    }

    /**
     * Accessor untuk nama tampilan administrator.
     *
     * @return  string  Username sebagai nama tampilan.
     */
    public function getNameAttribute(): string
    {
        return $this->username;
    }
}
