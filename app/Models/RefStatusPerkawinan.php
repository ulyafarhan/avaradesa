<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefStatusPerkawinan extends Model
{
    protected $table = 'ref_status_perkawinan';
    protected $fillable = ['nama'];
    public $timestamps = true;
}
