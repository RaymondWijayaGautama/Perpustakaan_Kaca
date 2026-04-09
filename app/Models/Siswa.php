<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'mst_siswa';
    protected $primaryKey = 'id_siswa_tetap';
    public $timestamps = false;

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'id_siswa_tetap', 'id_siswa_tetap');
    }
}