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
            $table->foreign(['id_kat_barang'], 'mst_inventaris_ibfk_1')->references(['id_kat_barang'])->on('ref_kategori_barang')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_pembelian'], 'mst_inventaris_ibfk_2')->references(['id_pembelian'])->on('tr_pembelian_inventaris')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_tr_laporan'], 'mst_inventaris_ibfk_3')->references(['id_tr_laporan'])->on('tr_laporan_kerusakan')->onUpdate('restrict')->onDelete('restrict');
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
