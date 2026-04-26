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
        Schema::create('tr_jurnal_walikelas', function (Blueprint $table) {
            $table->integer('ID_JURNAL_WALI', true);
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_8811_fk');
            $table->string('KETUGASAN_WALI')->nullable();
            $table->string('MINGGU_WALI')->nullable();
            $table->string('TGL_PENYERAHAN_WALI')->nullable();
            $table->string('NIP_VALIDATOR_WALI', 20)->nullable();
            $table->string('STATUS_JURNAL_WALI')->nullable();

            $table->unique(['ID_JURNAL_WALI'], 'tr_jurnal_walikelas_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_jurnal_walikelas');
    }
};
