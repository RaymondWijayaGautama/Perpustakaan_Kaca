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
            $table->integer('ID_LOG_BARANG_COFFEESHOP', true)->unique('log_barang_coffeeshop_pk');
            $table->integer('ID_INVENTARIS_COFFEESHOP')->nullable()->index('relation_552_fk');
            $table->dateTime('TGL_CEK_BARANG_COFFEESHOP')->nullable();
            $table->string('KONDISI_BARANG_COFFEESHOP', 25)->nullable();
            $table->string('KET_BARANG_COFFEESHOP', 100)->nullable();

            $table->primary(['ID_LOG_BARANG_COFFEESHOP']);
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
