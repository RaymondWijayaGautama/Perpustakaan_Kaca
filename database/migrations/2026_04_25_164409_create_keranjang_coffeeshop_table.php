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
        Schema::create('keranjang_coffeeshop', function (Blueprint $table) {
            $table->integer('ID_KERANJANG', true)->unique('keranjang_coffeeshop_pk');
            $table->integer('ID_MENU_COFFEESHOP')->nullable()->index('relation_8883_fk');
            $table->integer('JML_ITEM_KERANJANG')->nullable();
            $table->double('SUBTOTAL_KERANJANG')->nullable();

            $table->primary(['ID_KERANJANG']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keranjang_coffeeshop');
    }
};
