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
        Schema::create('siswa_kelas', function (Blueprint $table) {
            $table->integer('id_siswa_kelas')->primary();
            $table->integer('id_siswa_tetap')->nullable()->index('relation_4209_fk');
            $table->integer('id_kelas')->nullable()->index('relation_4210_fk');
            $table->integer('kode_ta')->nullable()->index('relation_4264_fk');

            $table->unique(['id_siswa_kelas'], 'siswa_kelas_pk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_kelas');
    }
};
