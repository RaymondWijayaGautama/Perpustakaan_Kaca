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
        Schema::table('guru_mapel', function (Blueprint $table) {
            $table->foreign(['NIP_KARYAWAN'], 'guru_mapel_ibfk_1')->references(['NIP_KARYAWAN'])->on('mst_karyawan')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['KODE_MAPEL'], 'guru_mapel_ibfk_2')->references(['KODE_MAPEL'])->on('mst_mapel')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_mapel', function (Blueprint $table) {
            $table->dropForeign('guru_mapel_ibfk_1');
            $table->dropForeign('guru_mapel_ibfk_2');
        });
    }
};
