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
            $table->integer('id_program_kerja')->unique('mst_program_kerja_pk');
            $table->integer('id_ta_anggaran')->nullable()->index('relation_1264_fk');
            $table->integer('id_unit')->nullable()->index('relation_1265_fk');
            $table->integer('id_tan')->nullable()->index('relation_1721_fk');
            $table->integer('id_master_coa')->nullable()->index('relation_1722_fk');
            $table->integer('id_kegiatan')->nullable()->index('relation_1727_fk');
            $table->double('nominal')->nullable();
            $table->string('indikator', 100)->nullable();
            $table->string('sasaran', 100)->nullable();
            $table->dateTime('waktu_awal')->nullable();
            $table->dateTime('waktu_akhir')->nullable();
            $table->string('keluaran_progker', 100)->nullable();
            $table->string('program_kerja')->nullable();
            $table->string('nip_penanggung_jawab', 20)->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_program_kerja']);
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
