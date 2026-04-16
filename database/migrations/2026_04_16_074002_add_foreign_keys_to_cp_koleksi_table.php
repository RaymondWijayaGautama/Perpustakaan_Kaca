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
        Schema::table('cp_koleksi', function (Blueprint $table) {
            $table->foreign(['isbn'], 'cp_koleksi_ibfk_1')->references(['isbn'])->on('mst_koleksi_buku')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_mst_laporan'], 'cp_koleksi_ibfk_2')->references(['id_mst_laporan'])->on('mst_koleksi_laporan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cp_koleksi', function (Blueprint $table) {
            $table->dropForeign('cp_koleksi_ibfk_1');
            $table->dropForeign('cp_koleksi_ibfk_2');
        });
    }
};
