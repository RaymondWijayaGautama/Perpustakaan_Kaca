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
            $table->integer('ID_KECAMATAN', true);
            $table->integer('ID_KOTA_KAB')->nullable()->index('relation_308_fk');
            $table->string('NAMA_KEC', 100)->nullable();

            $table->unique(['ID_KECAMATAN'], 'ref_kecamatan_pk');
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
