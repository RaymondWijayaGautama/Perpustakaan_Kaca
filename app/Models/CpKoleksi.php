<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpKoleksi extends Model
{
    // Beritahu Laravel nama tabel aslinya
    protected $table = 'cp_koleksi'; 
    
    // Beritahu Laravel primary key-nya
    protected $primaryKey = 'id_cp_koleksi'; 
    
    // Matikan fitur timestamps karena di tabelmu tidak ada kolom created_at & updated_at
    public $timestamps = false; 
    
    // Kolom apa saja yang boleh diisi (disimpan) oleh sistem
    protected $fillable = [
        'status_buku',
        'ISBN',
        'id_mst_laporan'
    ];
}