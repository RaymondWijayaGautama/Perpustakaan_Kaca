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
            $table->integer('ID_REF_PRESTASI', true);
            $table->string('NAMA_PRESTASI')->nullable();
            $table->string('JENIS_PRESTASI', 100)->nullable();
            $table->integer('POIN_PRESTASI')->nullable();

            $table->unique(['ID_REF_PRESTASI'], 'ref_prestasi_pk');
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
