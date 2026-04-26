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
            $table->integer('ID_BAHAN_BAKU', true)->unique('mst_bahan_baku_pk');
            $table->string('NAMA_BAHAN', 100)->nullable();
            $table->float('KUANTITI_STOK_BAHAN')->nullable();
            $table->double('HARGA_BELI')->nullable();
            $table->string('SATUAN_BAHAN_BAKU', 25)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_BAHAN_BAKU']);
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
