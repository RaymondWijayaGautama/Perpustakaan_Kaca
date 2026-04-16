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
        Schema::create('dt_penjualan', function (Blueprint $table) {
            $table->integer('id_dt_penjualan')->unique('dt_penjualan_pk');
            $table->integer('id_tr_penjualan')->nullable()->index('relation_105_fk');
            $table->integer('id_menu_coffeeshop')->nullable()->index('relation_104_fk');
            $table->integer('id_promo')->nullable()->index('relation_6922_fk');
            $table->integer('jml_item_penjualan')->nullable();
            $table->double('subtotal_penjualan')->nullable();

            $table->primary(['id_dt_penjualan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_penjualan');
    }
};
