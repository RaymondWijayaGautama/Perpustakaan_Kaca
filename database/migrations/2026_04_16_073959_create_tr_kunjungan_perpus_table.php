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
            $table->integer('id_kunjungan')->primary();
            $table->integer('id_siswa_tetap')->nullable()->index('relation_1182_fk');
            $table->dateTime('start_kunjungan')->nullable();
            $table->dateTime('end_kunjungan')->nullable();

            $table->unique(['id_kunjungan'], 'tr_kunjungan_perpus_pk');
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
