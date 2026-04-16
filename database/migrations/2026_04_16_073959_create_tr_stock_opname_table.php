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
            $table->integer('id_stock_opname')->primary();
            $table->integer('id_inventaris')->nullable()->index('relation_1207_fk');
            $table->dateTime('tgl_stock_opname')->nullable();
            $table->string('kondisi_aktual')->nullable();
            $table->string('kondisi_di_sistem')->nullable();
            $table->string('tindak_lanjut')->nullable();

            $table->unique(['id_stock_opname'], 'tr_stock_opname_pk');
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
