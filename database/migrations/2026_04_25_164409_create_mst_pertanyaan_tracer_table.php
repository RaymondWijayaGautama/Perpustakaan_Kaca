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
        Schema::create('mst_pertanyaan_tracer', function (Blueprint $table) {
            $table->integer('ID_MST_PERTANYAAN', true)->unique('mst_pertanyaan_tracer_pk');
            $table->integer('ID_TRACER')->nullable()->index('pertanyaan_tr_tracer_fk');
            $table->string('PERTANYAAN_TRACER')->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_MST_PERTANYAAN']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_pertanyaan_tracer');
    }
};
