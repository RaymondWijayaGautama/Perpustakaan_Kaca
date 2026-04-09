<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Karyawan extends Authenticatable
{
    use HasApiTokens, Notifiable;
    public $timestamps = false;

    protected $table = 'mst_karyawan'; 
    protected $primaryKey = 'nip_karyawan'; 
    public $incrementing = false; 
    protected $keyType = 'string';

    protected $fillable = [
        'nip_karyawan',
        'nama_karyawan',
        'email_karyawan',
        'password_karyawan',
        
    ];

    protected $hidden = [
        'password_karyawan',
    ];

    
    public function getAuthPassword()
    {
        return $this->password_karyawan;
    }
}