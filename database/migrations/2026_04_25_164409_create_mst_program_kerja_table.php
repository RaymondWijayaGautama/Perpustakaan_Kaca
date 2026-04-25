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
        Schema::create('mst_program_kerja', function (Blueprint $table) {
            $table->integer('ID_PROGRAM_KERJA', true)->unique('mst_program_kerja_pk');
            $table->integer('ID_TA_ANGGARAN')->nullable()->index('relation_1264_fk');
            $table->integer('ID_UNIT')->nullable()->index('relation_1265_fk');
            $table->integer('ID_TAN')->nullable()->index('relation_1721_fk');
            $table->integer('ID_MASTER_COA')->nullable()->index('relation_1722_fk');
            $table->integer('ID_KEGIATAN')->nullable()->index('relation_1727_fk');
            $table->double('NOMINAL')->nullable();
            $table->string('INDIKATOR', 100)->nullable();
            $table->string('SASARAN', 100)->nullable();
            $table->dateTime('WAKTU_AWAL')->nullable();
            $table->dateTime('WAKTU_AKHIR')->nullable();
            $table->string('KELUARAN_PROGKER', 100)->nullable();
            $table->string('PROGRAM_KERJA')->nullable();
            $table->string('NIP_PENANGGUNG_JAWAB', 20)->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_PROGRAM_KERJA']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_program_kerja');
    }
};
