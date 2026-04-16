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
        Schema::table('tr_penempatan_inventaris', function (Blueprint $table) {
            $table->foreign(['id_inventaris'], 'tr_penempatan_inventaris_ibfk_1')->references(['id_inventaris'])->on('mst_inventaris')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['id_ruang'], 'tr_penempatan_inventaris_ibfk_2')->references(['id_ruang'])->on('mst_ruang')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_penempatan_inventaris', function (Blueprint $table) {
            $table->dropForeign('tr_penempatan_inventaris_ibfk_1');
            $table->dropForeign('tr_penempatan_inventaris_ibfk_2');
        });
    }
};
