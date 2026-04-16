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
            $table->foreign(['id_tujuan_pemb'], 'mst_arah_tujuan_pembelajaran_ibfk_1')->references(['id_tujuan_pemb'])->on('mst_tujuan_pembelajaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_guru_mapel'], 'mst_arah_tujuan_pembelajaran_ibfk_2')->references(['id_guru_mapel'])->on('guru_mapel')->onUpdate('restrict')->onDelete('restrict');
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
