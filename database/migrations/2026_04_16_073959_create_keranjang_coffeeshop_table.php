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
            $table->integer('id_keranjang')->unique('keranjang_coffeeshop_pk');
            $table->integer('id_menu_coffeeshop')->nullable()->index('relation_8883_fk');
            $table->integer('jml_item_keranjang')->nullable();
            $table->double('subtotal_keranjang')->nullable();

            $table->primary(['id_keranjang']);
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
