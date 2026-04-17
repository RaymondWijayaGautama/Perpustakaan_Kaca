<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mst_koleksi_laporan', function (Blueprint $table) {
            // Gunakan integer biasa + autoIncrement agar 100% cocok dengan relasi KACA
            $table->integer('id_mst_laporan')->autoIncrement();
            
            // INI ADALAH KOLOM JEMBATANNYA (Sangat Penting!)
            $table->integer('id_pkl_siswa')->nullable(); 
            
            $table->boolean('is_delete')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_koleksi_laporan');
    }
};