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
            $table->foreign(['id_role_coffeeshop'], 'jadwal_coffeeshop_ibfk_1')->references(['id_role_coffeeshop'])->on('role_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_siswa_tetap'], 'jadwal_coffeeshop_ibfk_2')->references(['id_siswa_tetap'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['nip_karyawan'], 'jadwal_coffeeshop_ibfk_3')->references(['nip_karyawan'])->on('mst_karyawan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_nilai_kinerja'], 'jadwal_coffeeshop_ibfk_4')->references(['id_nilai_kinerja'])->on('nilai_kinerja_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
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
