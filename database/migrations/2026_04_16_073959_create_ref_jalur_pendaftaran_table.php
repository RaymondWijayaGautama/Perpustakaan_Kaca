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
        Schema::create('ref_jalur_pendaftaran', function (Blueprint $table) {
            $table->integer('id_jalur_pendaftaran')->primary();
            $table->string('nama_jalur', 100)->nullable();
            $table->dateTime('tgl_mulai_jalur')->nullable();
            $table->dateTime('tgl_selesai_jalur')->nullable();
            $table->string('keterangan_jalur')->nullable();

            $table->unique(['id_jalur_pendaftaran'], 'ref_jalur_pendaftaran_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_jalur_pendaftaran');
    }
};
