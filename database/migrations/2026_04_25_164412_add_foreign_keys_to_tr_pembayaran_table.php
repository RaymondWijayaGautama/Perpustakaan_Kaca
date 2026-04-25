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
            $table->foreign(['ID_SISWA_TETAP'], 'tr_pembayaran_ibfk_1')->references(['ID_SISWA_TETAP'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['KODE_TA'], 'tr_pembayaran_ibfk_2')->references(['KODE_TA'])->on('ref_ta')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_JENIS_PEMBAYARAN'], 'tr_pembayaran_ibfk_3')->references(['ID_JENIS_PEMBAYARAN'])->on('ref_jenis_pembayaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_TAGIHAN_SISWA'], 'tr_pembayaran_ibfk_4')->references(['ID_TAGIHAN_SISWA'])->on('tagihan_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['REF_ID_JENIS_PEMBAYARAN'], 'tr_pembayaran_ibfk_5')->references(['ID_JENIS_PEMBAYARAN'])->on('ref_jenis_pembayaran')->onUpdate('restrict')->onDelete('restrict');
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
