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
        Schema::table('dt_pembelian_bahan_baku', function (Blueprint $table) {
            $table->foreign(['id_bahan_baku'], 'dt_pembelian_bahan_baku_ibfk_1')->references(['id_bahan_baku'])->on('mst_bahan_baku')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_tr_pembelian_bahan'], 'dt_pembelian_bahan_baku_ibfk_2')->references(['id_tr_pembelian_bahan'])->on('tr_pembelian_bahan_baku')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dt_pembelian_bahan_baku', function (Blueprint $table) {
            $table->dropForeign('dt_pembelian_bahan_baku_ibfk_1');
            $table->dropForeign('dt_pembelian_bahan_baku_ibfk_2');
        });
    }
};
