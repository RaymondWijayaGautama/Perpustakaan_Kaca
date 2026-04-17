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
        Schema::table('keranjang_coffeeshop', function (Blueprint $table) {
            $table->foreign(['id_menu_coffeeshop'], 'keranjang_coffeeshop_ibfk_1')->references(['id_menu_coffeeshop'])->on('menu_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keranjang_coffeeshop', function (Blueprint $table) {
            $table->dropForeign('keranjang_coffeeshop_ibfk_1');
        });
    }
};
