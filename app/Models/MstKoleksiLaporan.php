<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstKoleksiLaporan extends Model
{
    protected $table = 'mst_koleksi_laporan';
    protected $primaryKey = 'id_mst_laporan';
    public $timestamps = false;

    protected $fillable = [
        'judul_laporan',
        'penulis_laporan',
        'tahun_laporan',
        'file_path',
        'is_delete'
    ];
}