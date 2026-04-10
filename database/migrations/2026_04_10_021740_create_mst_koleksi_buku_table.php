<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void {
       Schema::create('mst_koleksi_buku', function (Blueprint $table) {
            $table->string('ISBN', 25)->primary();
            $table->string('judul_koleksi', 255);
            $table->string('pengarang', 25);
            $table->string('penerbit', 25);
            $table->char('tahun', 4);
            $table->integer('nb_koleksi');
            $table->date('tgl_masuk_koleksi');
            $table->integer('jumlah_ekslempar');
            $table->integer('jumlah_halaman');
            $table->string('ukuran_buku', 25);
            $table->string('bibliografi', 255);
            $table->integer('indeks_awal_akhir');
            $table->string('keterangan_buku', 255);
            $table->string('no_rak_buku', 100);
            $table->tinyInteger('is_delete');
            $table->integer('id_ref_koleksi');

            $table->foreign('id_ref_koleksi')->references('id_ref_koleksi')->on('ref_koleksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_koleksi_buku');
    }
};
