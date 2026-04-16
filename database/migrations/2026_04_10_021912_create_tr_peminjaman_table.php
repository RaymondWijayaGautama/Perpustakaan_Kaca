<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tr_peminjaman', function (Blueprint $table) {
            $table->integer('id_peminjaman')->autoIncrement(); 
            
            $table->date('tgl_peminjaman')->nullable();
            $table->date('tgl_harus_kembali')->nullable();
            $table->date('tgl_kembali')->nullable(); 
            $table->string('status_peminjaman', 100)->nullable();
            $table->string('kondisi_buku', 25)->nullable();
            $table->string('keterangan_peminjaman', 255)->nullable();
            $table->double('denda_peminjaman')->default(0)->nullable();

            // --- KOLOM FOREIGN KEY (Menyesuaikan tabel asal) ---
            
            // 1. Dari tabel cp_koleksi (pakai integer biasa)
            $table->integer('id_cp_koleksi')->nullable(); 
            
            // 2. Dari tabel mst_siswa (pakai unsignedBigInteger karena kamu pakai $table->id())
            $table->unsignedBigInteger('id_siswa_tetap')->nullable(); 
            
            // 3. Dari tabel mst_karyawan (pakai string 20)
            $table->string('nip_karyawan', 20)->nullable(); 

            // --- DEKLARASI RELASI ---
            $table->foreign('id_cp_koleksi')
                  ->references('id_cp_koleksi')
                  ->on('cp_koleksi')
                  ->onDelete('set null');

            $table->foreign('id_siswa_tetap')
                  ->references('id_siswa_tetap')
                  ->on('mst_siswa')
                  ->onDelete('set null');

            $table->foreign('nip_karyawan')
                  ->references('nip_karyawan')
                  ->on('mst_karyawan')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tr_peminjaman');
    }
};