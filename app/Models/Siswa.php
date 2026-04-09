<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Siswa extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'mst_siswa';
    protected $primaryKey = 'id_siswa_tetap'; 

    protected $fillable = [
        'nisn_siswa',
        'nama_siswa_tetap',
        'password_siswa',
        'is_delete'
    ];

    protected $hidden = [
        'password_siswa',
    ];

    
    public function getAuthPassword()
    {
        return $this->password_siswa;
    }
}