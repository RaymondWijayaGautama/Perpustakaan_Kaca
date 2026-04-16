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
            $table->integer('id_jurnal_wali')->primary();
            $table->string('nip_karyawan', 20)->nullable()->index('relation_8811_fk');
            $table->string('ketugasan_wali')->nullable();
            $table->string('minggu_wali')->nullable();
            $table->string('tgl_penyerahan_wali')->nullable();
            $table->string('nip_validator_wali', 20)->nullable();
            $table->string('status_jurnal_wali')->nullable();

            $table->unique(['id_jurnal_wali'], 'tr_jurnal_walikelas_pk');
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
