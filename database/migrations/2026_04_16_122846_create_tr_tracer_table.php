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
        Schema::create('tr_tracer', function (Blueprint $table) {
            $table->integer('id_tracer')->primary();
            $table->integer('id_mst_pertanyaan')->nullable()->index('pertanyaan_tr_tracer_fk2');
            $table->integer('id_siswa_tetap')->nullable()->index('relation_2383_fk');
            $table->string('jawaban_pertanyaan')->nullable();

            $table->unique(['id_tracer'], 'tr_tracer_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_tracer');
    }
};
