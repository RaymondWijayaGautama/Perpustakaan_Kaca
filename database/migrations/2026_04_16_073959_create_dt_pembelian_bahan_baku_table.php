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
            $table->integer('ID_DT_PEMBELIAN_BAHAN')->unique('dt_pembelian_bahan_baku_pk');
            $table->integer('ID_BAHAN_BAKU')->nullable()->index('relation_3425_fk');
            $table->integer('ID_TR_PEMBELIAN_BAHAN')->nullable()->index('relation_3426_fk');
            $table->float('JUMLAH_PEMBELIAN')->nullable();
            $table->string('SATUAN_PEMBELIAN_BAHAN_BAKU', 25)->nullable();

            $table->primary(['ID_DT_PEMBELIAN_BAHAN']);
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
