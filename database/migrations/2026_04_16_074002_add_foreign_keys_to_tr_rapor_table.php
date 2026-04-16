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
            $table->foreign(['id_siswa_kelas'], 'tr_rapor_ibfk_1')->references(['id_siswa_kelas'])->on('siswa_kelas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['kode_mapel'], 'tr_rapor_ibfk_2')->references(['kode_mapel'])->on('mst_mapel')->onUpdate('restrict')->onDelete('restrict');
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
