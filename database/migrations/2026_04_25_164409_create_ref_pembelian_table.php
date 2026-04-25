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
            $table->integer('ID_REF_PEMBELIAN', true);
            $table->string('DESKRIPSI_PEMBELIAN')->nullable();
            $table->char('KODE_COA', 10)->nullable();

            $table->unique(['ID_REF_PEMBELIAN'], 'ref_pembelian_pk');
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
