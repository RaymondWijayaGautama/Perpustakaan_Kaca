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
        Schema::create('dt_jurnal_harian_guru', function (Blueprint $table) {
            $table->integer('id_dt_jurnal_guru')->unique('dt_jurnal_harian_guru_pk');
            $table->integer('id_jurnal_mengajar')->nullable()->index('relation_8765_fk');
            $table->integer('id_lesson_plan')->nullable()->index('relation_8766_fk');
            $table->dateTime('dt_tgl_jurnal_guru')->nullable();
            $table->time('dt_waktu_mulai_guru')->nullable();
            $table->time('dt_waktu_selesai_guru')->nullable();
            $table->string('dt_kegiatan_guru')->nullable();
            $table->string('dt_indikator_guru')->nullable();
            $table->string('dt_target_guru')->nullable();
            $table->string('dt_kendala_guru')->nullable();
            $table->string('dt_solusi_guru')->nullable();
            $table->string('dt_ket_guru')->nullable();

            $table->primary(['id_dt_jurnal_guru']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_jurnal_harian_guru');
    }
};
