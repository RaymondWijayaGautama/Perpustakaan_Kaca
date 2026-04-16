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
            $table->integer('id_pembelian')->primary();
            $table->integer('id_ref_pembelian')->nullable()->index('relation_1205_fk');
            $table->string('link_nota_beli_inv')->nullable();
            $table->dateTime('tgl_pesan_inv')->nullable();
            $table->dateTime('tgl_datang_inv')->nullable();
            $table->string('status_pembelian', 100)->nullable();

            $table->unique(['id_pembelian'], 'tr_pembelian_inventaris_pk');
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
