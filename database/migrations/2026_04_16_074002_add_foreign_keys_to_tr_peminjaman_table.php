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
        Schema::table('tr_peminjaman', function (Blueprint $table) {
            $table->foreign(['ID_SISWA_TETAP'], 'tr_peminjaman_ibfk_1')->references(['ID_SISWA_TETAP'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_CP_KOLEKSI'], 'tr_peminjaman_ibfk_2')->references(['ID_CP_KOLEKSI'])->on('cp_koleksi')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['NIP_KARYAWAN'], 'tr_peminjaman_ibfk_3')->references(['NIP_KARYAWAN'])->on('mst_karyawan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_peminjaman', function (Blueprint $table) {
            $table->dropForeign('tr_peminjaman_ibfk_1');
            $table->dropForeign('tr_peminjaman_ibfk_2');
            $table->dropForeign('tr_peminjaman_ibfk_3');
        });
    }
};
