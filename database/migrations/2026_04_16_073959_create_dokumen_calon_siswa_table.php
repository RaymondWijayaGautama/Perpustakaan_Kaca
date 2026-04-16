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
            $table->integer('ID_DOKUMEN')->unique('dokumen_calon_siswa_pk');
            $table->integer('ID_PENDAFTARAN')->nullable()->index('relation_2336_fk');
            $table->string('NAMA_DOKUMEN', 100)->nullable();
            $table->string('JENIS_DOKUMEN', 100)->nullable();
            $table->string('LINK_DOKUMEN_CALON')->nullable();
            $table->string('STATUS_VERIF_DOKUMEN')->nullable();

            $table->primary(['ID_DOKUMEN']);
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
