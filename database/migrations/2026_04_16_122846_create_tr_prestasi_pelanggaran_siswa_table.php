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
        Schema::create('tr_prestasi_pelanggaran_siswa', function (Blueprint $table) {
            $table->integer('id_prestasi_pelanggaran_siswa')->primary();
            $table->integer('kode_ta')->nullable()->index('relation_2369_fk');
            $table->integer('id_siswa_tercatat')->nullable();
            $table->string('nama_prestasi_siswa')->nullable();
            $table->integer('poin_prestasi_siswa')->nullable();
            $table->string('nama_pelanggaran_siswa')->nullable();
            $table->integer('poin_pelanggaran')->nullable();

            $table->unique(['id_prestasi_pelanggaran_siswa'], 'tr_prestasi_pelanggaran_siswa_');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_prestasi_pelanggaran_siswa');
    }
};
