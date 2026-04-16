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
        Schema::create('ref_tarif', function (Blueprint $table) {
            $table->integer('ID_REF_TARIF')->primary();
            $table->integer('ID_JENIS_TARIF')->nullable()->index('relation_318_fk');
            $table->integer('ID_TA_ANGGARAN')->nullable()->index('relation_6919_fk');
            $table->string('DESKRIPSI_TARIF', 100)->nullable();
            $table->double('NOMINAL')->nullable();
            $table->dateTime('TGL_PENETAPAN')->nullable();

            $table->unique(['ID_REF_TARIF'], 'ref_tarif_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_tarif');
    }
};
