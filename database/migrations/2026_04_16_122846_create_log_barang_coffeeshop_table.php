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
        Schema::create('log_barang_coffeeshop', function (Blueprint $table) {
            $table->integer('id_log_barang_coffeeshop')->unique('log_barang_coffeeshop_pk');
            $table->integer('id_inventaris_coffeeshop')->nullable()->index('relation_552_fk');
            $table->dateTime('tgl_cek_barang_coffeeshop')->nullable();
            $table->string('kondisi_barang_coffeeshop', 25)->nullable();
            $table->string('ket_barang_coffeeshop', 100)->nullable();

            $table->primary(['id_log_barang_coffeeshop']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_barang_coffeeshop');
    }
};
