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
            $table->integer('id_prestasi_calon')->unique('prestasi_calon_siswa_pk');
            $table->char('kode_calon', 20)->nullable()->index('relation_2286_fk');
            $table->string('jenis_prestasi', 100)->nullable();
            $table->string('nama_prestasi')->nullable();

            $table->primary(['id_prestasi_calon']);
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
