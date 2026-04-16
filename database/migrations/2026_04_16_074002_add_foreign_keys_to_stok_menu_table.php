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
        Schema::table('stok_menu', function (Blueprint $table) {
            $table->foreign(['ID_MENU_COFFEESHOP'], 'stok_menu_ibfk_1')->references(['ID_MENU_COFFEESHOP'])->on('menu_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_menu', function (Blueprint $table) {
            $table->dropForeign('stok_menu_ibfk_1');
        });
    }
};
