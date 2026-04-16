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
        Schema::create('prestasi_calon_siswa', function (Blueprint $table) {
            $table->integer('ID_PRESTASI_CALON')->unique('prestasi_calon_siswa_pk');
            $table->char('KODE_CALON', 20)->nullable()->index('relation_2286_fk');
            $table->string('JENIS_PRESTASI', 100)->nullable();
            $table->string('NAMA_PRESTASI')->nullable();

            $table->primary(['ID_PRESTASI_CALON']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prestasi_calon_siswa');
    }
};
