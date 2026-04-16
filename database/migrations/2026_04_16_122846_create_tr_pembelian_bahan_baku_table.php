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
            $table->integer('id_tr_pembelian_bahan')->primary();
            $table->integer('id_jadwal_coffeeshop')->nullable()->index('relation_3382_fk');
            $table->dateTime('tgl_pembelian_bahan')->nullable();
            $table->double('total_pembelian')->nullable();
            $table->string('link_nota_pembelian')->nullable();

            $table->unique(['id_tr_pembelian_bahan'], 'tr_pembelian_bahan_baku_pk');
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
