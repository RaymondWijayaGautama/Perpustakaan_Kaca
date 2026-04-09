<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'tr_peminjaman';
    protected $primaryKey = 'id_peminjaman';
    public $timestamps = false;

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa_tetap', 'id_siswa_tetap');
    }
}