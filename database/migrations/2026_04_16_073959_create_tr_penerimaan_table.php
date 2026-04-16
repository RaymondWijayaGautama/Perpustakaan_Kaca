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
        Schema::create('tr_penerimaan', function (Blueprint $table) {
            $table->integer('ID_TR_PENERIMAAN')->primary();
            $table->integer('ID_REF_PENERIMAAN')->nullable()->index('relation_5991_fk');
            $table->integer('ID_REF_DANA')->nullable()->index('relation_6900_fk');
            $table->string('DESKRIPSI_TR_PENERIMAAN', 100)->nullable();
            $table->dateTime('TANGGAL_TR_PENERIMAAN')->nullable();
            $table->double('JUMLAH_TR_PENERIMAAN')->nullable();
            $table->string('NIP_PENERIMA', 20)->nullable();

            $table->unique(['ID_TR_PENERIMAAN'], 'tr_penerimaan_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_penerimaan');
    }
};
