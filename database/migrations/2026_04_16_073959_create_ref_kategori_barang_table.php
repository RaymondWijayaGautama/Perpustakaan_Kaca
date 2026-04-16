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
        Schema::create('ref_kategori_barang', function (Blueprint $table) {
            $table->integer('ID_KAT_BARANG')->primary();
            $table->string('NAMA_KAT_BARANG', 100)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->unique(['ID_KAT_BARANG'], 'ref_kategori_barang_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_kategori_barang');
    }
};
