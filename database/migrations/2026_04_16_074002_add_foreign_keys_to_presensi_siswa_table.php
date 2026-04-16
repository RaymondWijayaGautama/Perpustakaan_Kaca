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
            $table->foreign(['id_tr_jadwal'], 'presensi_siswa_ibfk_1')->references(['id_tr_jadwal'])->on('tr_jadwal')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_siswa_kelas'], 'presensi_siswa_ibfk_2')->references(['id_siswa_kelas'])->on('siswa_kelas')->onUpdate('restrict')->onDelete('restrict');
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
