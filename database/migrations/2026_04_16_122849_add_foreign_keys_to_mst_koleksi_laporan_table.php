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
        Schema::table('mst_koleksi_laporan', function (Blueprint $table) {
            $table->foreign(['id_pkl_siswa'], 'mst_koleksi_laporan_ibfk_1')->references(['id_pkl_siswa'])->on('pkl_siswa')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_koleksi_laporan', function (Blueprint $table) {
            $table->dropForeign('mst_koleksi_laporan_ibfk_1');
        });
    }
};
