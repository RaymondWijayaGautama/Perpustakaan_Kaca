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
        Schema::table('mst_kriteria_ketuntasan', function (Blueprint $table) {
            $table->foreign(['ID_ATP'], 'mst_kriteria_ketuntasan_ibfk_1')->references(['ID_ATP'])->on('mst_arah_tujuan_pembelajaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_MODUL_AJAR'], 'mst_kriteria_ketuntasan_ibfk_2')->references(['ID_MODUL_AJAR'])->on('modul_ajar')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_kriteria_ketuntasan', function (Blueprint $table) {
            $table->dropForeign('mst_kriteria_ketuntasan_ibfk_1');
            $table->dropForeign('mst_kriteria_ketuntasan_ibfk_2');
        });
    }
};
