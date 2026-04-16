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
            $table->integer('id_dok_prestasi_pelatihan')->unique('dokumen_prestasi_pelatihan_pk');
            $table->integer('id_prestasi_pelatihan')->nullable()->index('relation_7010_fk');
            $table->string('nama_dok_prestasi_pelatihan', 100)->nullable();
            $table->string('link_dok_prestasi_pelatihan')->nullable();

            $table->primary(['id_dok_prestasi_pelatihan']);
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
