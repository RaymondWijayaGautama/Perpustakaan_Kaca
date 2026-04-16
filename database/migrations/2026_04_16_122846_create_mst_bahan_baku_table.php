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
        Schema::create('mst_bahan_baku', function (Blueprint $table) {
            $table->integer('id_bahan_baku')->unique('mst_bahan_baku_pk');
            $table->string('nama_bahan', 100)->nullable();
            $table->double('kuantiti_stok_bahan')->nullable();
            $table->double('harga_beli')->nullable();
            $table->string('satuan_bahan_baku', 25)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_bahan_baku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_bahan_baku');
    }
};
