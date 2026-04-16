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
            $table->integer('id_dt_jurnal_karyawan')->unique('dt_jurnal_karyawan_pk');
            $table->integer('id_tr_jurnal_karyawan')->nullable()->index('relation_8825_fk');
            $table->dateTime('tgl_dt_karyawan')->nullable();
            $table->time('waktu_mulai_karyawan')->nullable();
            $table->time('waktu_selesai_karyawan')->nullable();
            $table->string('kegiatan_karyawan')->nullable();
            $table->string('indikator_karyawan')->nullable();
            $table->string('status_kegiatan_karyawan')->nullable();
            $table->string('saran_kegiatan_karyawan')->nullable();
            $table->string('solusi_kegiatan_karyawan')->nullable();
            $table->string('keterangan_kegiatan_karyawan')->nullable();

            $table->primary(['id_dt_jurnal_karyawan']);
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
