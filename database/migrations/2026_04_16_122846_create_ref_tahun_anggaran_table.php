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
            $table->integer('id_ta_anggaran')->primary();
            $table->boolean('is_current')->nullable();
            $table->string('deskripsi_tahun_anggaran', 100)->nullable();

            $table->unique(['id_ta_anggaran'], 'ref_tahun_anggaran_pk');
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
