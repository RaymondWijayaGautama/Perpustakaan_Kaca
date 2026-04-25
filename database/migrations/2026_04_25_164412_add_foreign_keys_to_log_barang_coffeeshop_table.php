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
        Schema::table('log_barang_coffeeshop', function (Blueprint $table) {
            $table->foreign(['ID_INVENTARIS_COFFEESHOP'], 'log_barang_coffeeshop_ibfk_1')->references(['ID_INVENTARIS_COFFEESHOP'])->on('inventaris_barang_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('log_barang_coffeeshop', function (Blueprint $table) {
            $table->dropForeign('log_barang_coffeeshop_ibfk_1');
        });
    }
};
