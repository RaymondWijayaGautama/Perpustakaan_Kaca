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
            $table->foreign(['id_siswa_tetap'], 'siswa_kelas_ibfk_1')->references(['id_siswa_tetap'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_kelas'], 'siswa_kelas_ibfk_2')->references(['id_kelas'])->on('mst_kelas')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['kode_ta'], 'siswa_kelas_ibfk_3')->references(['kode_ta'])->on('ref_ta')->onUpdate('restrict')->onDelete('restrict');
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
