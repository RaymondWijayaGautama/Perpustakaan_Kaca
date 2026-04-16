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
        Schema::create('ref_kelurahan', function (Blueprint $table) {
            $table->integer('id_kelurahan')->primary();
            $table->integer('id_kecamatan')->nullable()->index('relation_309_fk');
            $table->string('nama_kel', 100)->nullable();

            $table->unique(['id_kelurahan'], 'ref_kelurahan_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_kelurahan');
    }
};
