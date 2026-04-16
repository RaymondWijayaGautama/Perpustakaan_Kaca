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
        Schema::table('tr_rapor', function (Blueprint $table) {
            $table->foreign(['ID_SISWA_KELAS'], 'tr_rapor_ibfk_1')->references(['ID_SISWA_KELAS'])->on('siswa_kelas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['KODE_MAPEL'], 'tr_rapor_ibfk_2')->references(['KODE_MAPEL'])->on('mst_mapel')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_rapor', function (Blueprint $table) {
            $table->dropForeign('tr_rapor_ibfk_1');
            $table->dropForeign('tr_rapor_ibfk_2');
        });
    }
};
