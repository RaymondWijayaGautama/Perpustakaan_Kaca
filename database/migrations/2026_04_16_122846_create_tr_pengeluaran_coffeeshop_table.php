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
        Schema::create('tr_pengeluaran_coffeeshop', function (Blueprint $table) {
            $table->integer('id_tr_pengeluaran')->primary();
            $table->dateTime('tgl_tr_pengeluaran')->nullable();
            $table->double('total_pengeluaran')->nullable();
            $table->string('ket_pengeluaran')->nullable();

            $table->unique(['id_tr_pengeluaran'], 'tr_pengeluaran_coffeeshop_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_pengeluaran_coffeeshop');
    }
};
