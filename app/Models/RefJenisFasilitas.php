<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefJenisFasilitas extends Model
{
    protected $table = 'ref_jenis_fasilitas';
    protected $fillable = ['nama'];
    public $timestamps = true;
}
