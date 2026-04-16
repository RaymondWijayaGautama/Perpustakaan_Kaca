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
            $table->integer('id_mst_pertanyaan')->unique('mst_pertanyaan_tracer_pk');
            $table->integer('id_tracer')->nullable()->index('pertanyaan_tr_tracer_fk');
            $table->string('pertanyaan_tracer')->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_mst_pertanyaan']);
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
