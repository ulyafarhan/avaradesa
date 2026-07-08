<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InventarisFasilitas extends Model
{
    use HasUlids, SoftDeletes, HasFactory;

    protected $table = 'inventaris_fasilitas';

    protected $fillable = [
        'nama_fasilitas',
        'jenis_fasilitas',
        'deskripsi',
        'lokasi',
        'kondisi',
        'tahun_dibangun',
        'foto',
        'latitude',
        'longitude',
        'status_penggunaan',
        'is_publik',
    ];

    protected function casts(): array
    {
        return [
            'tahun_dibangun' => 'integer',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_publik' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function jenisFasilitasRef(): BelongsTo
    {
        return $this->belongsTo(\App\Models\RefJenisFasilitas::class, 'jenis_fasilitas_id');
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (empty($this->foto)) {
            return null;
        }
        if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
            return $this->foto;
        }
        return Storage::url($this->foto);
    }
}
