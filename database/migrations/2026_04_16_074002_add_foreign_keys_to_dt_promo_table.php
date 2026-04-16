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
        Schema::table('dt_promo', function (Blueprint $table) {
            $table->foreign(['id_promo'], 'dt_promo_ibfk_1')->references(['id_promo'])->on('promo')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_menu_coffeeshop'], 'dt_promo_ibfk_2')->references(['id_menu_coffeeshop'])->on('menu_coffeeshop')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dt_promo', function (Blueprint $table) {
            $table->dropForeign('dt_promo_ibfk_1');
            $table->dropForeign('dt_promo_ibfk_2');
        });
    }
};
