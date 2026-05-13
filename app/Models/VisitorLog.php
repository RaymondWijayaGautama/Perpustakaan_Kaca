<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    use HasFactory;

    protected $table = 'visitor_logs'; // Nama tabel lo

    protected $fillable = [
        'username',
        'purpose',
        'visited_at'
    ];

    // Matikan timestamps otomatis jika lo hanya pakai visited_at
    // Tapi kalau di migration lo ada $table->timestamps(), biarin aja true.
    public $timestamps = true; 
}