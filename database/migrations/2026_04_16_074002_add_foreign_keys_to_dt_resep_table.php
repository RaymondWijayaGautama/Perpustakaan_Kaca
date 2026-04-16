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
            $table->foreign(['id_bahan_baku'], 'dt_resep_ibfk_1')->references(['id_bahan_baku'])->on('mst_bahan_baku')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_menu_coffeeshop'], 'dt_resep_ibfk_2')->references(['id_menu_coffeeshop'])->on('menu_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
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
