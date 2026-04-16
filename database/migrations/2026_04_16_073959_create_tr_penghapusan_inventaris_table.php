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
        Schema::create('tr_penghapusan_inventaris', function (Blueprint $table) {
            $table->integer('ID_PENGHAPUSAN_INV')->primary();
            $table->integer('ID_INVENTARIS')->nullable()->index('relation_1223_fk');
            $table->dateTime('TGL_PENGHAPUSAN_INV')->nullable();
            $table->string('KET_PENGHAPUSAN_INV')->nullable();
            $table->double('NOMINAL_PENJUALAN')->nullable();
            $table->dateTime('TGL_PENJUALAN_INV')->nullable();
            $table->string('NIP_VALIDATOR_PENGHAPUSAN', 20)->nullable();

            $table->unique(['ID_PENGHAPUSAN_INV'], 'tr_penghapusan_inventaris_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_penghapusan_inventaris');
    }
};
