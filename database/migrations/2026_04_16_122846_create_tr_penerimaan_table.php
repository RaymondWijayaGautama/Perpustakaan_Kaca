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
            $table->integer('id_tr_penerimaan')->primary();
            $table->integer('id_ref_penerimaan')->nullable()->index('relation_5991_fk');
            $table->integer('id_ref_dana')->nullable()->index('relation_6900_fk');
            $table->string('deskripsi_tr_penerimaan', 100)->nullable();
            $table->dateTime('tanggal_tr_penerimaan')->nullable();
            $table->double('jumlah_tr_penerimaan')->nullable();
            $table->string('nip_penerima', 20)->nullable();

            $table->unique(['id_tr_penerimaan'], 'tr_penerimaan_pk');
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
