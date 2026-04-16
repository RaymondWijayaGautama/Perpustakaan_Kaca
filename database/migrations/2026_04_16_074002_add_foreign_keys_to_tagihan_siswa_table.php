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
        Schema::table('tagihan_siswa', function (Blueprint $table) {
            $table->foreign(['ID_SISWA_TETAP'], 'tagihan_siswa_ibfk_1')->references(['ID_SISWA_TETAP'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_JENIS_PEMBAYARAN'], 'tagihan_siswa_ibfk_2')->references(['ID_JENIS_PEMBAYARAN'])->on('ref_jenis_pembayaran')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_siswa', function (Blueprint $table) {
            $table->dropForeign('tagihan_siswa_ibfk_1');
            $table->dropForeign('tagihan_siswa_ibfk_2');
        });
    }
};
