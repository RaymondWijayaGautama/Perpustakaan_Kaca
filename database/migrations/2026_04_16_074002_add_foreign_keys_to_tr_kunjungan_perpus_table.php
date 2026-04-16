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
        Schema::table('tr_kunjungan_perpus', function (Blueprint $table) {
            $table->foreign(['ID_SISWA_TETAP'], 'tr_kunjungan_perpus_ibfk_1')->references(['ID_SISWA_TETAP'])->on('mst_siswa')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_kunjungan_perpus', function (Blueprint $table) {
            $table->dropForeign('tr_kunjungan_perpus_ibfk_1');
        });
    }
};
