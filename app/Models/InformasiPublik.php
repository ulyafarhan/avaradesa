<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Support\Str;
use App\Jobs\SendNewsTelegramNotificationJob;
use App\Jobs\SendNewsWhatsappNotificationJob;

/**
 * Model untuk merepresentasikan artikel berita dan pengumuman desa.
 *
 * Tabel: informasi_publik
 * Menyimpan konten artikel, berita, maupun pengumuman yang dipublikasikan
 * oleh perangkat desa dan dapat diakses oleh publik di halaman depan situs.
 *
 * @property  string  $id  ULID unik artikel
 * @property  string  $judul  Judul artikel
 * @property  string  $slug  Slug URL ramah SEO
 * @property  string  $konten  Isi konten artikel (HTML/Markdown)
 * @property  string  $kategori  Kategori artikel (berita, pengumuman, dsb.)
 * @property  string|null  $cover_image  Path atau URL gambar sampul
 * @property  string|null  $meta_description  Deskripsi meta untuk SEO
 * @property  string|null  $kata_kunci  Kata kunci SEO (comma-separated)
 * @property  bool    $is_published  Status publikasi artikel
 * @property  string  $author_id  ULID administrator penulis artikel
 * @property  \Carbon\Carbon|null  $created_at  Waktu pembuatan artikel
 */
class InformasiPublik extends Model
{
    use HasFactory, HasUlids, SoftDeletes;

    /**
     * Nama tabel database yang terhubung dengan model ini.
     *
     * @var  string
     */
    protected $table = 'informasi_publik';

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
        'judul',
        'slug',
        'konten',
        'kategori',
        'cover_image',
        'meta_description',
        'kata_kunci',
        'is_published',
        'author_id',
    ];

    /**
     * Casting atribut ke tipe data native PHP.
     *
     * @return  array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Accessor untuk mendapatkan URL cover image secara dinamis.
     *
     * Jika nilai sudah berupa URL absolut, dikembalikan apa adanya.
     * Jika berupa path relatif, dikonversi menggunakan Storage::url().
     *
     * @param  string|null  $value  Path atau URL gambar sampul.
     * @return  string|null  URL absolut gambar sampul.
     */
    public function getCoverImageAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        // Build URL manually to avoid triggering Flysystem/FinfoMimeTypeDetector
        // which crashes when PHP fileinfo extension is not installed.
        return rtrim(config('app.url'), '/') . '/storage/' . ltrim($value, '/');
    }

    /**
     * Relasi ke administrator/perangkat desa yang mempublikasikan artikel ini.
     *
     * @return  \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function author()
    {
        return $this->belongsTo(Administrator::class, 'author_id');
    }

    /**
     * Scope query untuk hanya menyaring artikel yang telah dipublikasikan.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query  Builder Eloquent.
     * @return  \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Boot model — registrasi event listener untuk auto-slug, notifikasi Telegram, dan WhatsApp.
     *
     * @return  void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->judul);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('judul') && empty($model->slug)) {
                $model->slug = Str::slug($model->judul);
            }
        });

        static::saved(function ($model) {
            if ($model->wasRecentlyCreated && $model->is_published) {
                SendNewsTelegramNotificationJob::dispatch($model->id);
                SendNewsWhatsappNotificationJob::dispatch($model->id);
                return;
            }

            if (!$model->wasRecentlyCreated && $model->is_published && !$model->getOriginal('is_published')) {
                SendNewsTelegramNotificationJob::dispatch($model->id);
                SendNewsWhatsappNotificationJob::dispatch($model->id);
            }
        });
    }
}
