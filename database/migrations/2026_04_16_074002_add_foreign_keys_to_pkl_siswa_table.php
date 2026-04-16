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
        Schema::table('pkl_siswa', function (Blueprint $table) {
            $table->foreign(['id_pendaf_pkl'], 'pkl_siswa_ibfk_1')->references(['id_pendaf_pkl'])->on('pendaf_pkl')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_siswa_tetap'], 'pkl_siswa_ibfk_2')->references(['id_siswa_tetap'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_mitra_pkl'], 'pkl_siswa_ibfk_3')->references(['id_mitra_pkl'])->on('mitra_pkl')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['nip_karyawan'], 'pkl_siswa_ibfk_4')->references(['nip_karyawan'])->on('mst_karyawan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkl_siswa', function (Blueprint $table) {
            $table->dropForeign('pkl_siswa_ibfk_1');
            $table->dropForeign('pkl_siswa_ibfk_2');
            $table->dropForeign('pkl_siswa_ibfk_3');
            $table->dropForeign('pkl_siswa_ibfk_4');
        });
    }
};
