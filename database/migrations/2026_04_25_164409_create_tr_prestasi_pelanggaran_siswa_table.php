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
            $table->integer('ID_PRESTASI_PELANGGARAN_SISWA', true);
            $table->integer('KODE_TA')->nullable()->index('relation_2369_fk');
            $table->integer('ID_SISWA_TERCATAT')->nullable();
            $table->string('NAMA_PRESTASI_SISWA')->nullable();
            $table->integer('POIN_PRESTASI_SISWA')->nullable();
            $table->string('NAMA_PELANGGARAN_SISWA')->nullable();
            $table->integer('POIN_PELANGGARAN')->nullable();

            $table->unique(['ID_PRESTASI_PELANGGARAN_SISWA'], 'tr_prestasi_pelanggaran_siswa_');
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
