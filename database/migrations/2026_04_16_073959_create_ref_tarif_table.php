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
            $table->integer('id_ref_tarif')->primary();
            $table->integer('id_jenis_tarif')->nullable()->index('relation_318_fk');
            $table->integer('id_ta_anggaran')->nullable()->index('relation_6919_fk');
            $table->string('deskripsi_tarif', 100)->nullable();
            $table->double('nominal')->nullable();
            $table->dateTime('tgl_penetapan')->nullable();

            $table->unique(['id_ref_tarif'], 'ref_tarif_pk');
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
