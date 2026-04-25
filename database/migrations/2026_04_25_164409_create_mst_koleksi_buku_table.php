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
            $table->string('ISBN', 25)->unique('mst_koleksi_buku_pk');
            $table->integer('ID_REF_KOLEKSI')->nullable()->index('relation_1116_fk');
            $table->string('JUDUL_KOLEKSI', 25)->nullable();
            $table->string('PENGARANG', 25)->nullable();
            $table->string('PENERBIT', 25)->nullable();
            $table->char('TAHUN', 4)->nullable();
            $table->integer('NB_KOLEKSI')->nullable();
            $table->dateTime('TGL_MASUK_KOLEKSI')->nullable();
            $table->integer('JUMLAH_EKSEMPLAR')->nullable();
            $table->integer('JUMLAH_HALAMAN')->nullable();
            $table->string('UKURAN_BUKU', 25)->nullable();
            $table->string('BIBLIOGRAFI')->nullable();
            $table->integer('INDEKS_AWAL_AKHIR')->nullable();
            $table->string('KETERANGAN_BUKU')->nullable();
            $table->string('NO_RAK_BUKU', 100)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ISBN']);
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
