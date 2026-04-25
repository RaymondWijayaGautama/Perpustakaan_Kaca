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
        Schema::table('prestasi_calon_siswa', function (Blueprint $table) {
            $table->foreign(['KODE_CALON'], 'prestasi_calon_siswa_ibfk_1')->references(['KODE_CALON'])->on('mst_calon_siswa')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prestasi_calon_siswa', function (Blueprint $table) {
            $table->dropForeign('prestasi_calon_siswa_ibfk_1');
        });
    }
};
