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
        Schema::create('tr_pembelian_bahan_baku', function (Blueprint $table) {
            $table->integer('ID_TR_PEMBELIAN_BAHAN')->primary();
            $table->integer('ID_JADWAL_COFFEESHOP')->nullable()->index('relation_3382_fk');
            $table->dateTime('TGL_PEMBELIAN_BAHAN')->nullable();
            $table->double('TOTAL_PEMBELIAN')->nullable();
            $table->string('LINK_NOTA_PEMBELIAN')->nullable();

            $table->unique(['ID_TR_PEMBELIAN_BAHAN'], 'tr_pembelian_bahan_baku_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_pembelian_bahan_baku');
    }
};
