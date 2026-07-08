<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KategoriInformasi extends Model
{
    protected $table = 'kategori_informasi';
    protected $fillable = ['nama', 'slug'];
    public $timestamps = true;
}
