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
            $table->integer('ID_DT_JURNAL_GURU', true)->unique('dt_jurnal_harian_guru_pk');
            $table->integer('ID_JURNAL_MENGAJAR')->nullable()->index('relation_8765_fk');
            $table->integer('ID_LESSON_PLAN')->nullable()->index('relation_8766_fk');
            $table->dateTime('DT_TGL_JURNAL_GURU')->nullable();
            $table->time('DT_WAKTU_MULAI_GURU')->nullable();
            $table->time('DT_WAKTU_SELESAI_GURU')->nullable();
            $table->string('DT_KEGIATAN_GURU')->nullable();
            $table->string('DT_INDIKATOR_GURU')->nullable();
            $table->string('DT_TARGET_GURU')->nullable();
            $table->string('DT_KENDALA_GURU')->nullable();
            $table->string('DT_SOLUSI_GURU')->nullable();
            $table->string('DT_KET_GURU')->nullable();

            $table->primary(['ID_DT_JURNAL_GURU']);
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
