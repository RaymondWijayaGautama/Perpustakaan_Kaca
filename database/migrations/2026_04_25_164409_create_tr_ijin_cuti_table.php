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
        Schema::create('tr_ijin_cuti', function (Blueprint $table) {
            $table->integer('ID_TR_IC', true);
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_470_fk');
            $table->dateTime('TGL_AWAL')->nullable();
            $table->dateTime('TGL_SELESAI')->nullable();
            $table->string('KETERANGAN_IJIN_CUTI')->nullable();
            $table->string('LINK_BUKTI_IJIN_CUTI')->nullable();
            $table->string('STATUS_IJIN_CUTI')->nullable();

            $table->unique(['ID_TR_IC'], 'tr_ijin_cuti_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_ijin_cuti');
    }
};
