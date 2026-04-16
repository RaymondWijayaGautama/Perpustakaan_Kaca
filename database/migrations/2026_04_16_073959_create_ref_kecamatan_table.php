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
        Schema::create('ref_kecamatan', function (Blueprint $table) {
            $table->integer('id_kecamatan')->primary();
            $table->integer('id_kota_kab')->nullable()->index('relation_308_fk');
            $table->string('nama_kec', 100)->nullable();

            $table->unique(['id_kecamatan'], 'ref_kecamatan_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_kecamatan');
    }
};
