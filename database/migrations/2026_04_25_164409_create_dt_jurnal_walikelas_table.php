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
            $table->integer('ID_DT_JURNAL_WALI', true)->unique('dt_jurnal_walikelas_pk');
            $table->integer('ID_JURNAL_WALI')->nullable()->index('relation_8810_fk');
            $table->dateTime('DT_TGL_JURNAL_WALI')->nullable();
            $table->string('DT_PROGRAM_WALI')->nullable();
            $table->string('DT_KEGIATAN_WALI')->nullable();
            $table->string('DT_INDIKATOR_WALI')->nullable();
            $table->string('DT_SASARAN_WALI')->nullable();
            $table->string('DT_TARGET_WALI')->nullable();
            $table->string('DT_KENDALA_WALI')->nullable();
            $table->string('DT_SARAN_WALI')->nullable();
            $table->string('DT_SOLUSI_WALI')->nullable();
            $table->string('DT_KET_WALI')->nullable();

            $table->primary(['ID_DT_JURNAL_WALI']);
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
