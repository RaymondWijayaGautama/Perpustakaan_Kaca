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
            $table->integer('ID_DT_PENGELUARAN')->unique('dt_pengeluaran_coffeeshop_pk');
            $table->integer('ID_TR_PENGELUARAN')->nullable()->index('relation_6932_fk');
            $table->string('LINK_NOTA_PENGELUARAN')->nullable();
            $table->double('SUBTOTAL_PENGELUARAN')->nullable();
            $table->string('DESKRIPSI_PENGELUARAN')->nullable();

            $table->primary(['ID_DT_PENGELUARAN']);
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
