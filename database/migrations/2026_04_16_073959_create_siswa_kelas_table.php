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
            $table->integer('ID_SISWA_KELAS')->primary();
            $table->integer('ID_SISWA_TETAP')->nullable()->index('relation_4209_fk');
            $table->integer('ID_KELAS')->nullable()->index('relation_4210_fk');
            $table->integer('KODE_TA')->nullable()->index('relation_4264_fk');

            $table->unique(['ID_SISWA_KELAS'], 'siswa_kelas_pk');
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
