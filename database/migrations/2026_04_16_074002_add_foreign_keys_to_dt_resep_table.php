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
        Schema::table('dt_resep', function (Blueprint $table) {
            $table->foreign(['ID_BAHAN_BAKU'], 'dt_resep_ibfk_1')->references(['ID_BAHAN_BAKU'])->on('mst_bahan_baku')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_MENU_COFFEESHOP'], 'dt_resep_ibfk_2')->references(['ID_MENU_COFFEESHOP'])->on('menu_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dt_resep', function (Blueprint $table) {
            $table->dropForeign('dt_resep_ibfk_1');
            $table->dropForeign('dt_resep_ibfk_2');
        });
    }
};
