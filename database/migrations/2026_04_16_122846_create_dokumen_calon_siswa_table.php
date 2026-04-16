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
        Schema::create('dokumen_calon_siswa', function (Blueprint $table) {
            $table->integer('id_dokumen')->unique('dokumen_calon_siswa_pk');
            $table->integer('id_pendaftaran')->nullable()->index('relation_2336_fk');
            $table->string('nama_dokumen', 100)->nullable();
            $table->string('jenis_dokumen', 100)->nullable();
            $table->string('link_dokumen_calon')->nullable();
            $table->string('status_verif_dokumen')->nullable();

            $table->primary(['id_dokumen']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_calon_siswa');
    }
};
