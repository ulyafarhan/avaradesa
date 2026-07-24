<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;

/**
 * Model untuk merepresentasikan template/kategori layanan persuratan desa.
 *
 * Tabel: kategori_surat
 * Menyimpan definisi jenis-jenis surat yang dapat diajukan warga,
 * termasuk template view, schema isian form, dan syarat dokumen pendukung.
 *
 * @property  string  $id  ULID unik kategori surat
 * @property  string  $kode_surat  Kode singkat jenis surat (misal: SKTM, DOMISILI)
 * @property  string  $nama_surat  Nama lengkap jenis surat
 * @property  string  $template_view  Nama file Blade view template surat
 * @property  string|null  $body_content  Body surat dengan placeholder {nama_desa} dll
 * @property  array   $schema_isian  Skema field isian form (JSON)
 * @property  array   $syarat_dokumen  Daftar syarat dokumen pendukung (JSON)
 * @property  bool    $is_active  Status aktif/tidaknya layanan surat ini
 */
class KategoriSurat extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var  string
     */
    protected $table = 'kategori_surat';

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
        'kode_surat',
        'nama_surat',
        'template_view',
        'body_content',
        'schema_isian',
        'syarat_dokumen',
        'is_active',
    ];

    /**
     * Casting atribut ke tipe data native PHP.
     *
     * @return  array<string, string>
     */
    protected function casts(): array
    {
        return [
            'schema_isian' => 'array',
            'syarat_dokumen' => 'array',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi ke seluruh pengajuan surat yang menggunakan kategori ini.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pengajuan()
    {
        return $this->hasMany(PengajuanSurat::class, 'kategori_surat_id');
    }

    /**
     * Scope query untuk memfilter hanya template surat yang sedang aktif pelayanan.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Builder Eloquent.
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
