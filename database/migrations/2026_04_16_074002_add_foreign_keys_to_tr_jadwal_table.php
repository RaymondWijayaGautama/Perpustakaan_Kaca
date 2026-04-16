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
        Schema::table('tr_jadwal', function (Blueprint $table) {
            $table->foreign(['nip_karyawan'], 'tr_jadwal_ibfk_1')->references(['nip_karyawan'])->on('mst_karyawan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['kode_mapel'], 'tr_jadwal_ibfk_2')->references(['kode_mapel'])->on('mst_mapel')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_kelas'], 'tr_jadwal_ibfk_3')->references(['id_kelas'])->on('mst_kelas')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_jadwal', function (Blueprint $table) {
            $table->dropForeign('tr_jadwal_ibfk_1');
            $table->dropForeign('tr_jadwal_ibfk_2');
            $table->dropForeign('tr_jadwal_ibfk_3');
        });
    }
};
