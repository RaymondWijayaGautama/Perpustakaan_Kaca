<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpKoleksi extends Model
{
    protected $table = 'cp_koleksi'; 
    protected $primaryKey = 'id_cp_koleksi'; 
    public $timestamps = false; 
    protected $fillable = [
        'status_buku',
        'ISBN',
        'id_mst_laporan'
    ];
}