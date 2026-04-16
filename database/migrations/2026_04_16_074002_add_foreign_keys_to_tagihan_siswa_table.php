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
            $table->foreign(['id_siswa_tetap'], 'tagihan_siswa_ibfk_1')->references(['id_siswa_tetap'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_jenis_pembayaran'], 'tagihan_siswa_ibfk_2')->references(['id_jenis_pembayaran'])->on('ref_jenis_pembayaran')->onUpdate('restrict')->onDelete('restrict');
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
