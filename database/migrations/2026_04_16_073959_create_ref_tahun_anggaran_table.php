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
        Schema::create('ref_tahun_anggaran', function (Blueprint $table) {
            $table->integer('ID_TA_ANGGARAN')->primary();
            $table->boolean('IS_CURRENT')->nullable();
            $table->string('DESKRIPSI_TAHUN_ANGGARAN', 100)->nullable();

            $table->unique(['ID_TA_ANGGARAN'], 'ref_tahun_anggaran_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_tahun_anggaran');
    }
};
