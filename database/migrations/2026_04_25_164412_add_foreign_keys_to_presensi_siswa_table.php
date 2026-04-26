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
        Schema::table('presensi_siswa', function (Blueprint $table) {
            $table->foreign(['ID_TR_JADWAL'], 'presensi_siswa_ibfk_1')->references(['ID_TR_JADWAL'])->on('tr_jadwal')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_SISWA_KELAS'], 'presensi_siswa_ibfk_2')->references(['ID_SISWA_KELAS'])->on('siswa_kelas')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presensi_siswa', function (Blueprint $table) {
            $table->dropForeign('presensi_siswa_ibfk_1');
            $table->dropForeign('presensi_siswa_ibfk_2');
        });
    }
};
