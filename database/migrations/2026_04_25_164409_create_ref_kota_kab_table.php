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
            $table->integer('ID_KOTA_KAB', true);
            $table->integer('ID_PROVINSI')->nullable()->index('prov_kota_kab_fk');
            $table->string('NAMA_KOTA_KAB', 100)->nullable();

            $table->unique(['ID_KOTA_KAB'], 'ref_kota_kab_pk');
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
