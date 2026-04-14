<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $table = 'tr_kunjungan_perpus'; 
    protected $primaryKey = 'id_kunjungan_perpus'; 
    public $timestamps = false;
}