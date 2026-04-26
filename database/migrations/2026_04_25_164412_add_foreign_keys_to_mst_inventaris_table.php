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
        Schema::table('mst_inventaris', function (Blueprint $table) {
            $table->foreign(['ID_KAT_BARANG'], 'mst_inventaris_ibfk_1')->references(['ID_KAT_BARANG'])->on('ref_kategori_barang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_PEMBELIAN'], 'mst_inventaris_ibfk_2')->references(['ID_PEMBELIAN'])->on('tr_pembelian_inventaris')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ID_TR_LAPORAN'], 'mst_inventaris_ibfk_3')->references(['ID_TR_LAPORAN'])->on('tr_laporan_kerusakan')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mst_inventaris', function (Blueprint $table) {
            $table->dropForeign('mst_inventaris_ibfk_1');
            $table->dropForeign('mst_inventaris_ibfk_2');
            $table->dropForeign('mst_inventaris_ibfk_3');
        });
    }
};
