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
            $table->foreign(['id_atp'], 'mst_kriteria_ketuntasan_ibfk_1')->references(['id_atp'])->on('mst_arah_tujuan_pembelajaran')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_modul_ajar'], 'mst_kriteria_ketuntasan_ibfk_2')->references(['id_modul_ajar'])->on('modul_ajar')->onUpdate('restrict')->onDelete('restrict');
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
