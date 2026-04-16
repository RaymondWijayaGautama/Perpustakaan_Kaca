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
        Schema::create('ref_kota_kab', function (Blueprint $table) {
            $table->integer('id_kota_kab')->primary();
            $table->integer('id_provinsi')->nullable()->index('prov_kota_kab_fk');
            $table->string('nama_kota_kab', 100)->nullable();

            $table->unique(['id_kota_kab'], 'ref_kota_kab_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ref_kota_kab');
    }
};
