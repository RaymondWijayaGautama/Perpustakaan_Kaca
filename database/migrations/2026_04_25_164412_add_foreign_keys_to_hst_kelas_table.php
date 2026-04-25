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
            $table->foreign(['ID_SISWA_TETAP'], 'hst_kelas_ibfk_1')->references(['ID_SISWA_TETAP'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['KODE_TA'], 'hst_kelas_ibfk_2')->references(['KODE_TA'])->on('ref_ta')->onUpdate('restrict')->onDelete('restrict');
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
