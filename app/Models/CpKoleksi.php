<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpKoleksi extends Model
{
    protected $table = 'cp_koleksi';

    protected $primaryKey = 'id_cp_koleksi';

    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'id_koleksi',
        'ISBN',
        'status_buku',
        'lokasi_rak',
        'tanggal_masuk',
        'kondisi_buku',
        'is_delete'
    ];

    public function buku()
    {
        return $this->belongsTo(MstKoleksiBuku::class, 'ISBN', 'ISBN');
    }
}
