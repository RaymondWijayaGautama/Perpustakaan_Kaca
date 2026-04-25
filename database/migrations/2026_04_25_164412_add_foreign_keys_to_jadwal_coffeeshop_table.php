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
        Schema::table('jadwal_coffeeshop', function (Blueprint $table) {
            $table->foreign(['ID_ROLE_COFFEESHOP'], 'jadwal_coffeeshop_ibfk_1')->references(['ID_ROLE_COFFEESHOP'])->on('role_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_SISWA_TETAP'], 'jadwal_coffeeshop_ibfk_2')->references(['ID_SISWA_TETAP'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['NIP_KARYAWAN'], 'jadwal_coffeeshop_ibfk_3')->references(['NIP_KARYAWAN'])->on('mst_karyawan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_NILAI_KINERJA'], 'jadwal_coffeeshop_ibfk_4')->references(['ID_NILAI_KINERJA'])->on('nilai_kinerja_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_coffeeshop', function (Blueprint $table) {
            $table->dropForeign('jadwal_coffeeshop_ibfk_1');
            $table->dropForeign('jadwal_coffeeshop_ibfk_2');
            $table->dropForeign('jadwal_coffeeshop_ibfk_3');
            $table->dropForeign('jadwal_coffeeshop_ibfk_4');
        });
    }
};
