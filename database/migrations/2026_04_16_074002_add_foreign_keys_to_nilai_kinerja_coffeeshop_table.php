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
        Schema::table('nilai_kinerja_coffeeshop', function (Blueprint $table) {
            $table->foreign(['id_siswa_tetap'], 'nilai_kinerja_coffeeshop_ibfk_1')->references(['id_siswa_tetap'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_jadwal_coffeeshop'], 'nilai_kinerja_coffeeshop_ibfk_2')->references(['id_jadwal_coffeeshop'])->on('jadwal_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nilai_kinerja_coffeeshop', function (Blueprint $table) {
            $table->dropForeign('nilai_kinerja_coffeeshop_ibfk_1');
            $table->dropForeign('nilai_kinerja_coffeeshop_ibfk_2');
        });
    }
};
