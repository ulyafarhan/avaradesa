<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefPendidikan extends Model
{
    protected $table = 'ref_pendidikan';
    protected $fillable = ['nama'];
    public $timestamps = true;
}
