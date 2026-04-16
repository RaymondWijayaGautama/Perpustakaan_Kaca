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
        Schema::create('tr_kunjungan_perpus', function (Blueprint $table) {
            $table->integer('ID_KUNJUNGAN')->primary();
            $table->integer('ID_SISWA_TETAP')->nullable()->index('relation_1182_fk');
            $table->dateTime('START_KUNJUNGAN')->nullable();
            $table->dateTime('END_KUNJUNGAN')->nullable();

            $table->unique(['ID_KUNJUNGAN'], 'tr_kunjungan_perpus_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tr_kunjungan_perpus');
    }
};
