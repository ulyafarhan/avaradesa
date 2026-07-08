<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefAgama extends Model
{
    protected $table = 'ref_agama';
    protected $fillable = ['nama'];
    public $timestamps = true;
}
