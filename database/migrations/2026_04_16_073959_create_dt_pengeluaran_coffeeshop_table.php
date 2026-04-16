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
        Schema::create('dt_pengeluaran_coffeeshop', function (Blueprint $table) {
            $table->integer('id_dt_pengeluaran')->unique('dt_pengeluaran_coffeeshop_pk');
            $table->integer('id_tr_pengeluaran')->nullable()->index('relation_6932_fk');
            $table->string('link_nota_pengeluaran')->nullable();
            $table->double('subtotal_pengeluaran')->nullable();
            $table->string('deskripsi_pengeluaran')->nullable();

            $table->primary(['id_dt_pengeluaran']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_pengeluaran_coffeeshop');
    }
};
