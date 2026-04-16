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
            $table->integer('id_inventaris_coffeeshop')->unique('inventaris_barang_coffeeshop_p');
            $table->string('nama_barang_coffeeshop', 100)->nullable();
            $table->dateTime('tgl_beli_barang_coffeeshop')->nullable();
            $table->double('harga_barang_coffeeshop')->nullable();
            $table->integer('jml_barang_coffeeshop')->nullable();
            $table->string('status_barang_coffeeshop', 100)->nullable();

            $table->primary(['id_inventaris_coffeeshop']);
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
