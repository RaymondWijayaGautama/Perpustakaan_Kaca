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
            $table->integer('id_dt_jurnal_manajemen')->unique('dt_jurnal_manajemen_pk');
            $table->integer('id_jurnal_manajemen')->nullable()->index('relation_8796_fk');
            $table->dateTime('dt_tgl_jurnal_manajemen')->nullable();
            $table->string('dt_program_manajemen')->nullable();
            $table->string('dt_kegiatan_manajemen')->nullable();
            $table->string('dt_indikator_manajemen')->nullable();
            $table->string('dt_sasaran_manajemen')->nullable();
            $table->string('dt_target_manajemen')->nullable();
            $table->string('dt_kendala_manajemen')->nullable();
            $table->string('dt_saran_manajemen')->nullable();
            $table->string('dt_solusi_manajemen')->nullable();
            $table->string('dt_ket_manajemen')->nullable();

            $table->primary(['id_dt_jurnal_manajemen']);
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
