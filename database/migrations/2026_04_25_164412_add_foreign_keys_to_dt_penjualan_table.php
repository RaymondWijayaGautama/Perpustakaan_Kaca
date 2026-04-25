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
        Schema::table('dt_penjualan', function (Blueprint $table) {
            $table->foreign(['ID_TR_PENJUALAN'], 'dt_penjualan_ibfk_1')->references(['ID_TR_PENJUALAN'])->on('tr_penjualan_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_MENU_COFFEESHOP'], 'dt_penjualan_ibfk_2')->references(['ID_MENU_COFFEESHOP'])->on('menu_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_PROMO'], 'dt_penjualan_ibfk_3')->references(['ID_PROMO'])->on('promo')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dt_penjualan', function (Blueprint $table) {
            $table->dropForeign('dt_penjualan_ibfk_1');
            $table->dropForeign('dt_penjualan_ibfk_2');
            $table->dropForeign('dt_penjualan_ibfk_3');
        });
    }
};
