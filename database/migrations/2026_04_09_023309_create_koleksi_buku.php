<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    // Tabel Referensi Kategori
    Schema::create('ref_koleksi', function (Blueprint $table) {
        $table->id('id_ref_koleksi');
        $table->string('deskripsi');
        $table->boolean('is_delete')->default(0);
        $table->timestamps();
    });

    // Tabel Utama Buku
    Schema::create('mst_koleksi_buku', function (Blueprint $table) {
        $table->string('ISBN')->primary();
        $table->string('judul_koleksi');
        $table->string('pengarang');
        $table->string('penerbit');
        $table->year('tahun');
        $table->integer('nb_koleksi');
        $table->date('tgl_masuk_koleksi');
        $table->integer('jumlah_ekslempar');
        $table->integer('jumlah_halaman');
        $table->string('ukuran_buku');
        $table->string('bibliografi')->default('-');
        $table->boolean('indeks_awal_akhir')->default(0);
        $table->text('keterangan_buku')->nullable();
        $table->string('no_rak_buku');
        $table->foreignId('id_ref_koleksi')->constrained('ref_koleksi', 'id_ref_koleksi');
        $table->boolean('is_delete')->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('koleksi_buku');
    }
};
