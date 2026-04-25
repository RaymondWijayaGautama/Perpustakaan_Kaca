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
        Schema::create('tr_penjualan_coffeeshop', function (Blueprint $table) {
            $table->integer('ID_TR_PENJUALAN', true);
            $table->integer('ID_JADWAL_COFFEESHOP')->nullable()->index('relation_3381_fk');
            $table->integer('ID_DISKON')->nullable()->index('relation_3413_fk');
            $table->dateTime('TGL_TR_PENJUALAN')->nullable();
            $table->string('NAMA_PEMBELI', 25)->nullable();
            $table->dateTime('TGL_PEMESANAN')->nullable();
            $table->string('JENIS_PENJUALAN', 100)->nullable();
            $table->double('DP_PENJUALAN')->nullable();
            $table->string('METODE_BAYAR', 100)->nullable();
            $table->string('ALAMAT_PEMBELI', 100)->nullable();
            $table->double('TOTAL_PENJUALAN')->nullable();
            $table->double('POTONGAN_DISKON')->nullable();
            $table->string('STATUS_TR_PENJUALAN')->nullable();

            $table->unique(['ID_TR_PENJUALAN'], 'tr_penjualan_coffeeshop_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_penjualan_coffeeshop');
    }
};
