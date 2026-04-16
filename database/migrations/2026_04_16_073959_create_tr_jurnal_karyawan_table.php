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
        Schema::create('tr_jurnal_karyawan', function (Blueprint $table) {
            $table->integer('ID_TR_JURNAL_KARYAWAN')->primary();
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_8826_fk');
            $table->string('KETUGASAN_KARYAWAN')->nullable();
            $table->string('MINGGU_KARYAWAN')->nullable();
            $table->dateTime('TGL_PENYERAHAN_KARYAWAN')->nullable();
            $table->string('NIP_VALIDATOR_KARYAWAN', 20)->nullable();
            $table->string('STATUS_J_KARYAWAN')->nullable();

            $table->unique(['ID_TR_JURNAL_KARYAWAN'], 'tr_jurnal_karyawan_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_jurnal_karyawan');
    }
};
