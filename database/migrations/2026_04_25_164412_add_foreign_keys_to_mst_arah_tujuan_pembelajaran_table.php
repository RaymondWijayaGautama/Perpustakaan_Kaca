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
        Schema::table('mst_arah_tujuan_pembelajaran', function (Blueprint $table) {
            $table->foreign(['ID_TUJUAN_PEMB'], 'mst_arah_tujuan_pembelajaran_ibfk_1')->references(['ID_TUJUAN_PEMB'])->on('mst_tujuan_pembelajaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_GURU_MAPEL'], 'mst_arah_tujuan_pembelajaran_ibfk_2')->references(['ID_GURU_MAPEL'])->on('guru_mapel')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_arah_tujuan_pembelajaran', function (Blueprint $table) {
            $table->dropForeign('mst_arah_tujuan_pembelajaran_ibfk_1');
            $table->dropForeign('mst_arah_tujuan_pembelajaran_ibfk_2');
        });
    }
};
