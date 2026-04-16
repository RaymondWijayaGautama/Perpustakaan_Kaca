<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tr_pemusnahan_buku', function (Blueprint $table) {
            // Gunakan integer biasa sesuai SQL asli
            $table->integer('id_pemusnahan_buku')->autoIncrement();
            
            // --- KOLOM FOREIGN KEY (Wajib integer biasa) ---
            $table->integer('id_cp_koleksi')->nullable(); 
            
            $table->string('ket_pemusnahan_buku', 255)->nullable();
            $table->dateTime('tgl_pemusnahan_buku')->nullable();
            $table->boolean('is_delete')->default(0)->nullable();

            // --- DEKLARASI RELASI ---
            $table->foreign('id_cp_koleksi')
                  ->references('id_cp_koleksi')
                  ->on('cp_koleksi')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tr_pemusnahan_buku');
    }
};