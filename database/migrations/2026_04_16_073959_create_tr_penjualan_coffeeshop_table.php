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
            $table->integer('id_tr_penjualan')->primary();
            $table->integer('id_jadwal_coffeeshop')->nullable()->index('relation_3381_fk');
            $table->integer('id_diskon')->nullable()->index('relation_3413_fk');
            $table->dateTime('tgl_tr_penjualan')->nullable();
            $table->string('nama_pembeli', 25)->nullable();
            $table->dateTime('tgl_pemesanan')->nullable();
            $table->string('jenis_penjualan', 100)->nullable();
            $table->double('dp_penjualan')->nullable();
            $table->string('metode_bayar', 100)->nullable();
            $table->string('alamat_pembeli', 100)->nullable();
            $table->double('total_penjualan')->nullable();
            $table->double('potongan_diskon')->nullable();
            $table->string('status_tr_penjualan')->nullable();

            $table->unique(['id_tr_penjualan'], 'tr_penjualan_coffeeshop_pk');
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
