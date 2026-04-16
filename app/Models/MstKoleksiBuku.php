<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstKoleksiBuku extends Model
{
    protected $table = 'mst_koleksi_buku';

    protected $primaryKey = 'ISBN'; 
    public $incrementing = false; 
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'ISBN',
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'kategori'
    ];

    public function koleksi()
    {
        return $this->hasMany(CpKoleksi::class, 'ISBN', 'ISBN');
    }
}