<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefPekerjaan extends Model
{
    protected $table = 'ref_pekerjaan';
    protected $fillable = ['nama'];
    public $timestamps = true;
}
