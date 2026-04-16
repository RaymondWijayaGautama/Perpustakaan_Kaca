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
        Schema::create('cp_koleksi', function (Blueprint $table) {
            // 1. Primary Key pakai integer biasa agar cocok dengan database lama
            $table->integer('id_cp_koleksi')->autoIncrement();
            
            $table->string('status_buku', 100)->nullable();
            
            // 2. ISBN pakai string 25 karakter
            $table->string('ISBN', 25)->nullable();
            
            // 3. Gunakan integer() biasa, BUKAN foreignId(). Wajib nullable().
            $table->integer('id_mst_laporan')->nullable(); 

            // 4. Definisikan Foreign Key secara eksplisit/manual
            $table->foreign('id_mst_laporan')
                  ->references('id_mst_laporan')
                  ->on('mst_koleksi_laporan')
                  ->onDelete('set null'); // Jika laporan dihapus, buku fisiknya tetap ada tapi id_laporannya jadi null

            $table->foreign('ISBN')
                  ->references('ISBN')
                  ->on('mst_koleksi_buku')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_koleksi');
    }
};