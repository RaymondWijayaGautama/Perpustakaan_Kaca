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
        Schema::create('dt_jurnal_manajemen', function (Blueprint $table) {
            $table->integer('ID_DT_JURNAL_MANAJEMEN')->unique('dt_jurnal_manajemen_pk');
            $table->integer('ID_JURNAL_MANAJEMEN')->nullable()->index('relation_8796_fk');
            $table->dateTime('DT_TGL_JURNAL_MANAJEMEN')->nullable();
            $table->string('DT_PROGRAM_MANAJEMEN')->nullable();
            $table->string('DT_KEGIATAN_MANAJEMEN')->nullable();
            $table->string('DT_INDIKATOR_MANAJEMEN')->nullable();
            $table->string('DT_SASARAN_MANAJEMEN')->nullable();
            $table->string('DT_TARGET_MANAJEMEN')->nullable();
            $table->string('DT_KENDALA_MANAJEMEN')->nullable();
            $table->string('DT_SARAN_MANAJEMEN')->nullable();
            $table->string('DT_SOLUSI_MANAJEMEN')->nullable();
            $table->string('DT_KET_MANAJEMEN')->nullable();

            $table->primary(['ID_DT_JURNAL_MANAJEMEN']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dt_jurnal_manajemen');
    }
};
