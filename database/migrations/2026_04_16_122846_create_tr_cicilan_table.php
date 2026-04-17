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
        Schema::create('tr_cicilan', function (Blueprint $table) {
            $table->integer('id_tr_cicilan')->primary();
            $table->integer('id_pembayaran')->nullable()->index('relation_6904_fk');
            $table->dateTime('tgl_cicilan')->nullable();
            $table->double('jumlah_cicilan')->nullable();
            $table->integer('cicilan_ke')->nullable();

            $table->unique(['id_tr_cicilan'], 'tr_cicilan_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_cicilan');
    }
};
