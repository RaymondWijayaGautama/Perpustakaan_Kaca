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
            $table->integer('ID_TRACER', true);
            $table->integer('ID_MST_PERTANYAAN')->nullable()->index('pertanyaan_tr_tracer_fk2');
            $table->integer('ID_SISWA_TETAP')->nullable()->index('relation_2383_fk');
            $table->string('JAWABAN_PERTANYAAN')->nullable();

            $table->unique(['ID_TRACER'], 'tr_tracer_pk');
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
