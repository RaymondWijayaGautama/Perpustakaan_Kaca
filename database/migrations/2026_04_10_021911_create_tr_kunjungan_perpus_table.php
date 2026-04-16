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
            $table->id('id_kunjungan');
            $table->dateTime('start_kunjungan');
            $table->dateTime('end_kunjungan')->nullable();
            
            $table->foreignId('id_siswa_tetap')->constrained('mst_siswa', 'id_siswa_tetap');
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
