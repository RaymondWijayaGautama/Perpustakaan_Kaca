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
        Schema::create('tr_prestasi_pelatihan_karyawan', function (Blueprint $table) {
            $table->integer('ID_PRESTASI_PELATIHAN')->primary();
            $table->string('NIP_KARYAWAN', 20)->nullable()->index('relation_7011_fk');
            $table->string('JENIS_PRESTASI_PELATIHAN', 100)->nullable();
            $table->string('NAMA_PRETASI_PELATIHAN', 100)->nullable();
            $table->string('TEMPAT_PRESTASI_PELATIHAN', 100)->nullable();
            $table->dateTime('TGL_PRESTASI_PELATIHAN')->nullable();
            $table->string('KET_PRESTASI_PELATIHAN')->nullable();
            $table->dateTime('TGL_LAPOR')->nullable();
            $table->string('STATUS_PRESTASI_PELATIHAN')->nullable();

            $table->unique(['ID_PRESTASI_PELATIHAN'], 'tr_prestasi_pelatihan_karyawan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_prestasi_pelatihan_karyawan');
    }
};
