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
        Schema::create('tr_peminjaman', function (Blueprint $table) {
            $table->integer('id_peminjaman')->autoIncrement()->primary();
            $table->date('tgl_peminjaman');
            $table->date('tgl_harus_kembali');
            $table->date('tgl_kembali')->nullable();
            $table->string('status_peminjaman', 100);
            $table->string('kondisi_buku', 25);
            $table->string('keterangan_peminjaman', 255);
            $table->double('denda_peminjaman');
            $table->integer('id_cp_koleksi');
            $table->integer('id_siswa_tetap');
            $table->string('nip_karyawan', 20);

            $table->foreign('id_cp_koleksi')->references('id_cp_koleksi')->on('cp_koleksi');
            $table->foreign('id_siswa_tetap')->references('id_siswa_tetap')->on('mst_siswa');
            $table->foreign('nip_karyawan')->references('nip_karyawan')->on('mst_karyawan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_peminjaman');
    }
};
