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
        Schema::create('mst_koleksi_buku', function (Blueprint $table) {
            $table->string('isbn', 25)->unique('mst_koleksi_buku_pk');
            $table->integer('id_ref_koleksi')->nullable()->index('relation_1116_fk');
            $table->string('judul_koleksi', 255)->nullable();
            $table->string('pengarang', 25)->nullable();
            $table->string('penerbit', 25)->nullable();
            $table->char('tahun', 4)->nullable();
            $table->integer('nb_koleksi')->nullable();
            $table->dateTime('tgl_masuk_koleksi')->nullable();
            $table->integer('jumlah_eksemplar')->nullable();
            $table->integer('jumlah_halaman')->nullable();
            $table->string('ukuran_buku', 25)->nullable();
            $table->integer('jumlah_ekslempar')->nullable();
            $table->string('bibliografi')->nullable();
            $table->integer('indeks_awal_akhir')->nullable();
            $table->string('keterangan_buku')->nullable();
            $table->string('no_rak_buku', 100)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['isbn']);
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
