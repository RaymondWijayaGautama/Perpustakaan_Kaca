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
        Schema::create('dt_pembelian_bahan_baku', function (Blueprint $table) {
            $table->integer('id_dt_pembelian_bahan')->unique('dt_pembelian_bahan_baku_pk');
            $table->integer('id_bahan_baku')->nullable()->index('relation_3425_fk');
            $table->integer('id_tr_pembelian_bahan')->nullable()->index('relation_3426_fk');
            $table->double('jumlah_pembelian')->nullable();
            $table->string('satuan_pembelian_bahan_baku', 25)->nullable();

            $table->primary(['id_dt_pembelian_bahan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_pembelian_bahan_baku');
    }
};
