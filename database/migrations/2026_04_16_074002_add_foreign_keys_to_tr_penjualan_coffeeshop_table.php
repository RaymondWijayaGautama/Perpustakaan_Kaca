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
            $table->foreign(['id_jadwal_coffeeshop'], 'tr_penjualan_coffeeshop_ibfk_1')->references(['id_jadwal_coffeeshop'])->on('jadwal_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_diskon'], 'tr_penjualan_coffeeshop_ibfk_2')->references(['id_diskon'])->on('diskon')->onUpdate('restrict')->onDelete('restrict');
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
