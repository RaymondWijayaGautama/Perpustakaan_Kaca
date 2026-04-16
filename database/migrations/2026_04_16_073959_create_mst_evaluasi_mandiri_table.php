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
            $table->integer('id_mst_evaluasi_mandiri')->unique('mst_evaluasi_mandiri_pk');
            $table->string('nama_kompetensi_evaluasi')->nullable();
            $table->boolean('is_valid_eval')->nullable();
            $table->boolean('is_delete')->nullable();

            $table->primary(['id_mst_evaluasi_mandiri']);
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
