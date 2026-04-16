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
        Schema::table('siswa_kelas', function (Blueprint $table) {
            $table->foreign(['ID_SISWA_TETAP'], 'siswa_kelas_ibfk_1')->references(['ID_SISWA_TETAP'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_KELAS'], 'siswa_kelas_ibfk_2')->references(['ID_KELAS'])->on('mst_kelas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['KODE_TA'], 'siswa_kelas_ibfk_3')->references(['KODE_TA'])->on('ref_ta')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_kelas', function (Blueprint $table) {
            $table->dropForeign('siswa_kelas_ibfk_1');
            $table->dropForeign('siswa_kelas_ibfk_2');
            $table->dropForeign('siswa_kelas_ibfk_3');
        });
    }
};
