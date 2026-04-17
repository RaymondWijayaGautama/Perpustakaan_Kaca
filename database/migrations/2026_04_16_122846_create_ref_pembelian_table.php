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
        Schema::create('ref_pembelian', function (Blueprint $table) {
            $table->integer('id_ref_pembelian')->primary();
            $table->string('deskripsi_pembelian')->nullable();
            $table->char('kode_coa', 10)->nullable();

            $table->unique(['id_ref_pembelian'], 'ref_pembelian_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_pembelian');
    }
};
