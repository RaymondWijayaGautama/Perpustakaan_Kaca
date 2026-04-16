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
            $table->foreign(['id_siswa_tetap'], 'tr_peminjaman_ibfk_1')->references(['id_siswa_tetap'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_cp_koleksi'], 'tr_peminjaman_ibfk_2')->references(['id_cp_koleksi'])->on('cp_koleksi')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['nip_karyawan'], 'tr_peminjaman_ibfk_3')->references(['nip_karyawan'])->on('mst_karyawan')->onUpdate('restrict')->onDelete('restrict');
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
