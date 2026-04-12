<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MstKoleksiBuku extends Model
{
    protected $table = 'mst_koleksi_buku';
    protected $primaryKey = 'ISBN'; 
    public $incrementing = false; 
    protected $keyType = 'string';

    protected $guarded = [];

    public $timestamps = false;
}