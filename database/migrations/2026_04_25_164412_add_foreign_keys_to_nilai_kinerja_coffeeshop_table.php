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
            $table->foreign(['ID_SISWA_TETAP'], 'nilai_kinerja_coffeeshop_ibfk_1')->references(['ID_SISWA_TETAP'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_JADWAL_COFFEESHOP'], 'nilai_kinerja_coffeeshop_ibfk_2')->references(['ID_JADWAL_COFFEESHOP'])->on('jadwal_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
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
