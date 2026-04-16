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
        Schema::create('ref_provinsi', function (Blueprint $table) {
            $table->integer('id_provinsi')->primary();
            $table->string('nama_prov', 100)->nullable();

            $table->unique(['id_provinsi'], 'ref_provinsi_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_provinsi');
    }
};
