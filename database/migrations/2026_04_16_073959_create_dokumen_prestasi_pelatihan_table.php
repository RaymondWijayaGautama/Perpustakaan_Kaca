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
        Schema::create('dokumen_prestasi_pelatihan', function (Blueprint $table) {
            $table->integer('ID_DOK_PRESTASI_PELATIHAN')->unique('dokumen_prestasi_pelatihan_pk');
            $table->integer('ID_PRESTASI_PELATIHAN')->nullable()->index('relation_7010_fk');
            $table->string('NAMA_DOK_PRESTASI_PELATIHAN', 100)->nullable();
            $table->string('LINK_DOK_PRESTASI_PELATIHAN')->nullable();

            $table->primary(['ID_DOK_PRESTASI_PELATIHAN']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_prestasi_pelatihan');
    }
};
