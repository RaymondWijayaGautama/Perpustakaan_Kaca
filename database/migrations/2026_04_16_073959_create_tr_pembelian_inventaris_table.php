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
        Schema::create('tr_pembelian_inventaris', function (Blueprint $table) {
            $table->integer('ID_PEMBELIAN')->primary();
            $table->integer('ID_REF_PEMBELIAN')->nullable()->index('relation_1205_fk');
            $table->string('LINK_NOTA_BELI_INV')->nullable();
            $table->dateTime('TGL_PESAN_INV')->nullable();
            $table->dateTime('TGL_DATANG_INV')->nullable();
            $table->string('STATUS_PEMBELIAN', 100)->nullable();

            $table->unique(['ID_PEMBELIAN'], 'tr_pembelian_inventaris_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_pembelian_inventaris');
    }
};
