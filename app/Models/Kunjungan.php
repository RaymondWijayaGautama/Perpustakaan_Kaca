<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model; // Ini wajib ada agar fungsi database bisa dipakai

class Kunjungan extends Model // Tulisan "extends Model" ini adalah kuncinya
{
    protected $table = 'tr_kunjungan_perpus'; 
    protected $primaryKey = 'id_kunjungan_perpus'; 
    public $timestamps = false;
}