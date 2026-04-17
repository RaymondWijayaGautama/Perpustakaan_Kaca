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
        Schema::create('dt_jurnal_walikelas', function (Blueprint $table) {
            $table->integer('id_dt_jurnal_wali')->unique('dt_jurnal_walikelas_pk');
            $table->integer('id_jurnal_wali')->nullable()->index('relation_8810_fk');
            $table->dateTime('dt_tgl_jurnal_wali')->nullable();
            $table->string('dt_program_wali')->nullable();
            $table->string('dt_kegiatan_wali')->nullable();
            $table->string('dt_indikator_wali')->nullable();
            $table->string('dt_sasaran_wali')->nullable();
            $table->string('dt_target_wali')->nullable();
            $table->string('dt_kendala_wali')->nullable();
            $table->string('dt_saran_wali')->nullable();
            $table->string('dt_solusi_wali')->nullable();
            $table->string('dt_ket_wali')->nullable();

            $table->primary(['id_dt_jurnal_wali']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_jurnal_walikelas');
    }
};
