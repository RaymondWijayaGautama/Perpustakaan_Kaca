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
            $table->dateTime('start_kunjungan');
            $table->dateTime('end_kunjungan');
            $table->integer('id_siswa_tetap');

            $table->foreign('id_siswa_tetap')->references('id_siswa_tetap')->on('mst_siswa');
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
