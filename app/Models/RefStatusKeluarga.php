<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefStatusKeluarga extends Model
{
    protected $table = 'ref_status_keluarga';
    protected $fillable = ['nama'];
    public $timestamps = true;
}
