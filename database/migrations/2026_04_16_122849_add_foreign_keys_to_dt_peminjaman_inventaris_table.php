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
        Schema::table('dt_peminjaman_inventaris', function (Blueprint $table) {
            $table->foreign(['id_tr_peminjaman_inv'], 'dt_peminjaman_inventaris_ibfk_1')->references(['id_tr_peminjaman_inv'])->on('tr_peminjaman_inventaris')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_inventaris'], 'dt_peminjaman_inventaris_ibfk_2')->references(['id_inventaris'])->on('mst_inventaris')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_ruang'], 'dt_peminjaman_inventaris_ibfk_3')->references(['id_ruang'])->on('mst_ruang')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dt_peminjaman_inventaris', function (Blueprint $table) {
            $table->dropForeign('dt_peminjaman_inventaris_ibfk_1');
            $table->dropForeign('dt_peminjaman_inventaris_ibfk_2');
            $table->dropForeign('dt_peminjaman_inventaris_ibfk_3');
        });
    }
};
