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
        Schema::table('dt_nilai', function (Blueprint $table) {
            $table->foreign(['ID_SISWA_KELAS'], 'dt_nilai_ibfk_1')->references(['ID_SISWA_KELAS'])->on('siswa_kelas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['KODE_MAPEL'], 'dt_nilai_ibfk_2')->references(['KODE_MAPEL'])->on('mst_mapel')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dt_nilai', function (Blueprint $table) {
            $table->dropForeign('dt_nilai_ibfk_1');
            $table->dropForeign('dt_nilai_ibfk_2');
        });
    }
};
