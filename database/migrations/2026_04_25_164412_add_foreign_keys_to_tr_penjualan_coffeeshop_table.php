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
        Schema::table('tr_penjualan_coffeeshop', function (Blueprint $table) {
            $table->foreign(['ID_JADWAL_COFFEESHOP'], 'tr_penjualan_coffeeshop_ibfk_1')->references(['ID_JADWAL_COFFEESHOP'])->on('jadwal_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_DISKON'], 'tr_penjualan_coffeeshop_ibfk_2')->references(['ID_DISKON'])->on('diskon')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_penjualan_coffeeshop', function (Blueprint $table) {
            $table->dropForeign('tr_penjualan_coffeeshop_ibfk_1');
            $table->dropForeign('tr_penjualan_coffeeshop_ibfk_2');
        });
    }
};
