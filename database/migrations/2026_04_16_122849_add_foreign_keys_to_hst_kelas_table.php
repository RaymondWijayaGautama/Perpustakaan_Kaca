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
        Schema::table('hst_kelas', function (Blueprint $table) {
            $table->foreign(['id_siswa_tetap'], 'hst_kelas_ibfk_1')->references(['id_siswa_tetap'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['kode_ta'], 'hst_kelas_ibfk_2')->references(['kode_ta'])->on('ref_ta')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hst_kelas', function (Blueprint $table) {
            $table->dropForeign('hst_kelas_ibfk_1');
            $table->dropForeign('hst_kelas_ibfk_2');
        });
    }
};
