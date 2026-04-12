<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Models
{
    protected $table = 'tr_kunjungan_perpus';
    protected $primaryKey = 'id_kunjungan';
    public $timestamps = false;
}