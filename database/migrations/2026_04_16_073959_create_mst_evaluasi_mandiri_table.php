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
        Schema::create('mst_evaluasi_mandiri', function (Blueprint $table) {
            $table->integer('ID_MST_EVALUASI_MANDIRI')->unique('mst_evaluasi_mandiri_pk');
            $table->string('NAMA_KOMPETENSI_EVALUASI')->nullable();
            $table->boolean('IS_VALID_EVAL')->nullable();
            $table->boolean('IS_DELETE')->nullable();

            $table->primary(['ID_MST_EVALUASI_MANDIRI']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mst_evaluasi_mandiri');
    }
};
