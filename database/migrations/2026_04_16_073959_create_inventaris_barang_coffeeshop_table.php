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
        Schema::create('inventaris_barang_coffeeshop', function (Blueprint $table) {
            $table->integer('ID_INVENTARIS_COFFEESHOP')->unique('inventaris_barang_coffeeshop_p');
            $table->string('NAMA_BARANG_COFFEESHOP', 100)->nullable();
            $table->dateTime('TGL_BELI_BARANG_COFFEESHOP')->nullable();
            $table->double('HARGA_BARANG_COFFEESHOP')->nullable();
            $table->integer('JML_BARANG_COFFEESHOP')->nullable();
            $table->string('STATUS_BARANG_COFFEESHOP', 100)->nullable();

            $table->primary(['ID_INVENTARIS_COFFEESHOP']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris_barang_coffeeshop');
    }
};
