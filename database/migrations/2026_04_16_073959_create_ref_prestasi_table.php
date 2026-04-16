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
        Schema::create('ref_prestasi', function (Blueprint $table) {
            $table->integer('id_ref_prestasi')->primary();
            $table->string('nama_prestasi')->nullable();
            $table->string('jenis_prestasi', 100)->nullable();
            $table->integer('poin_prestasi')->nullable();

            $table->unique(['id_ref_prestasi'], 'ref_prestasi_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_prestasi');
    }
};
