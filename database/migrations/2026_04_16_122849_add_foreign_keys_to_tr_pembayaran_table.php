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
        Schema::table('tr_pembayaran', function (Blueprint $table) {
            $table->foreign(['id_siswa_tetap'], 'tr_pembayaran_ibfk_1')->references(['id_siswa_tetap'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['kode_ta'], 'tr_pembayaran_ibfk_2')->references(['kode_ta'])->on('ref_ta')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_jenis_pembayaran'], 'tr_pembayaran_ibfk_3')->references(['id_jenis_pembayaran'])->on('ref_jenis_pembayaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_tagihan_siswa'], 'tr_pembayaran_ibfk_4')->references(['id_tagihan_siswa'])->on('tagihan_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ref_id_jenis_pembayaran'], 'tr_pembayaran_ibfk_5')->references(['id_jenis_pembayaran'])->on('ref_jenis_pembayaran')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_pembayaran', function (Blueprint $table) {
            $table->dropForeign('tr_pembayaran_ibfk_1');
            $table->dropForeign('tr_pembayaran_ibfk_2');
            $table->dropForeign('tr_pembayaran_ibfk_3');
            $table->dropForeign('tr_pembayaran_ibfk_4');
            $table->dropForeign('tr_pembayaran_ibfk_5');
        });
    }
};
