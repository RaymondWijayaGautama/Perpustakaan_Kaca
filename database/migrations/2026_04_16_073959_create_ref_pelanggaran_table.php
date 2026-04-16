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
        Schema::create('ref_pelanggaran', function (Blueprint $table) {
            $table->integer('id_ref_pelanggaran')->primary();
            $table->string('nama_pelanggaran')->nullable();
            $table->string('jenis_pelanggaran')->nullable();
            $table->integer('poin_pelanggaran')->nullable();

            $table->unique(['id_ref_pelanggaran'], 'ref_pelanggaran_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_pelanggaran');
    }
};
