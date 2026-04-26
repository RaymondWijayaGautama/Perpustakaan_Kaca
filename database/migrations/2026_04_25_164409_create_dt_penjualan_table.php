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
            $table->integer('ID_DT_PENJUALAN', true)->unique('dt_penjualan_pk');
            $table->integer('ID_TR_PENJUALAN')->nullable()->index('relation_105_fk');
            $table->integer('ID_MENU_COFFEESHOP')->nullable()->index('relation_104_fk');
            $table->integer('ID_PROMO')->nullable()->index('relation_6922_fk');
            $table->integer('JML_ITEM_PENJUALAN')->nullable();
            $table->double('SUBTOTAL_PENJUALAN')->nullable();

            $table->primary(['ID_DT_PENJUALAN']);
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
