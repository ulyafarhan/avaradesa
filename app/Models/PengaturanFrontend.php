<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk merepresentasikan konfigurasi visual dan konten spesifik halaman frontend publik.
 *
 * Tabel: pengaturan_frontend
 * Primary key: kunci (string, non-otomatis)
 * Menyimpan pasangan kunci-nilai untuk pengaturan tampilan situs publik
 * (logo, nama desa, judul, warna, konten hero, dsb.).
 *
 * @property  string  $kunci  Kunci unik pengaturan
 * @property  string  $nilai  Nilai pengaturan (string)
 * @property  string  $tipe_data  Tipe data nilai (string, integer, boolean, json)
 * @property  string|null  $deskripsi  Deskripsi fungsi pengaturan
 */
class PengaturanFrontend extends Model
{
    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var  string
     */
    protected $table = 'pengaturan_frontend';

    /**
     * Nonaktifkan timestamps otomatis.
     *
     * @var  bool
     */
    public $timestamps = false;

    /**
     * Primary key model adalah kunci (bukan id).
     *
     * @var  string
     */
    protected $primaryKey = 'kunci';

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
        'kunci',
        'nilai',
        'tipe_data',
        'deskripsi',
    ];

    /**
     * Mengambil nilai pengaturan frontend berdasarkan kunci beserta casting tipe datanya.
     *
     * @param  string       $key     Kunci pengaturan yang dicari.
     * @param  mixed        $default Nilai default jika kunci tidak ditemukan.
     * @return  mixed       Nilai pengaturan dengan tipe data yang sesuai.
     */
    public static function get(string $key, $default = null)
    {
        $setting = static::where('kunci', $key)->first();
        
        if (!$setting) {
            return $default;
        }

        return match($setting->tipe_data) {
            'integer' => (int) $setting->nilai,
            'boolean' => filter_var($setting->nilai, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->nilai, true),
            default => $setting->nilai,
        };
    }

    /**
     * Menyimpan atau memperbarui nilai pengaturan frontend.
     *
     * Jika kunci sudah ada, nilainya diperbarui. Jika belum ada, record baru dibuat.
     * Nilai array akan di-encode otomatis ke JSON.
     *
     * @param  string  $key    Kunci pengaturan.
     * @param  mixed   $value  Nilai pengaturan.
     * @param  string  $type   Tipe data nilai (default: string).
     * @return  void
     */
    public static function set(string $key, $value, string $type = 'string'): void
    {
        $nilai = is_array($value) ? json_encode($value) : $value;
        
        static::updateOrCreate(
            ['kunci' => $key],
            [
                'nilai' => $nilai,
                'tipe_data' => $type,
            ]
        );
    }
}
