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
        Schema::create('ref_jenis_tarif', function (Blueprint $table) {
            $table->integer('id_jenis_tarif')->primary();
            $table->string('deskripsi_jenis_tarif', 100)->nullable();

            $table->unique(['id_jenis_tarif'], 'ref_jenis_tarif_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_jenis_tarif');
    }
};
