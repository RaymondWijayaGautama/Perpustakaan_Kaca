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
            $table->integer('ID_TR_CICILAN', true);
            $table->integer('ID_PEMBAYARAN')->nullable()->index('relation_6904_fk');
            $table->dateTime('TGL_CICILAN')->nullable();
            $table->double('JUMLAH_CICILAN')->nullable();
            $table->integer('CICILAN_KE')->nullable();

            $table->unique(['ID_TR_CICILAN'], 'tr_cicilan_pk');
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
