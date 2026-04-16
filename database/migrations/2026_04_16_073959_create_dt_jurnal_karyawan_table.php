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
        Schema::create('dt_jurnal_karyawan', function (Blueprint $table) {
            $table->integer('ID_DT_JURNAL_KARYAWAN')->unique('dt_jurnal_karyawan_pk');
            $table->integer('ID_TR_JURNAL_KARYAWAN')->nullable()->index('relation_8825_fk');
            $table->dateTime('TGL_DT_KARYAWAN')->nullable();
            $table->time('WAKTU_MULAI_KARYAWAN')->nullable();
            $table->time('WAKTU_SELESAI_KARYAWAN')->nullable();
            $table->string('KEGIATAN_KARYAWAN')->nullable();
            $table->string('INDIKATOR_KARYAWAN')->nullable();
            $table->string('STATUS_KEGIATAN_KARYAWAN')->nullable();
            $table->string('SARAN_KEGIATAN_KARYAWAN')->nullable();
            $table->string('SOLUSI_KEGIATAN_KARYAWAN')->nullable();
            $table->string('KETERANGAN_KEGIATAN_KARYAWAN')->nullable();

            $table->primary(['ID_DT_JURNAL_KARYAWAN']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_jurnal_karyawan');
    }
};
