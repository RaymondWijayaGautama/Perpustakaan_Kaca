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
        Schema::table('mst_prota_prosem', function (Blueprint $table) {
            $table->foreign(['ID_ATP'], 'mst_prota_prosem_ibfk_1')->references(['ID_ATP'])->on('mst_arah_tujuan_pembelajaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['KODE_MAPEL'], 'mst_prota_prosem_ibfk_2')->references(['KODE_MAPEL'])->on('mst_mapel')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_prota_prosem', function (Blueprint $table) {
            $table->dropForeign('mst_prota_prosem_ibfk_1');
            $table->dropForeign('mst_prota_prosem_ibfk_2');
        });
    }
};
