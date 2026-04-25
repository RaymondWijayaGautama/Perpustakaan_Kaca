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
            $table->integer('ID_TR_PENGELUARAN', true);
            $table->dateTime('TGL_TR_PENGELUARAN')->nullable();
            $table->double('TOTAL_PENGELUARAN')->nullable();
            $table->string('KET_PENGELUARAN')->nullable();

            $table->unique(['ID_TR_PENGELUARAN'], 'tr_pengeluaran_coffeeshop_pk');
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
