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
        Schema::create('tr_stock_opname', function (Blueprint $table) {
            $table->integer('ID_STOCK_OPNAME', true);
            $table->integer('ID_INVENTARIS')->nullable()->index('relation_1207_fk');
            $table->dateTime('TGL_STOCK_OPNAME')->nullable();
            $table->string('KONDISI_AKTUAL')->nullable();
            $table->string('KONDISI_DI_SISTEM')->nullable();
            $table->string('TINDAK_LANJUT')->nullable();

            $table->unique(['ID_STOCK_OPNAME'], 'tr_stock_opname_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_stock_opname');
    }
};
