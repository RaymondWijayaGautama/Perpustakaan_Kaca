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
            $table->integer('ID_JALUR_PENDAFTARAN', true);
            $table->string('NAMA_JALUR', 100)->nullable();
            $table->dateTime('TGL_MULAI_JALUR')->nullable();
            $table->dateTime('TGL_SELESAI_JALUR')->nullable();
            $table->string('KETERANGAN_JALUR')->nullable();

            $table->unique(['ID_JALUR_PENDAFTARAN'], 'ref_jalur_pendaftaran_pk');
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
