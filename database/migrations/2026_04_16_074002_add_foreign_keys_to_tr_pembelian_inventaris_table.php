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
        Schema::table('tr_pembelian_inventaris', function (Blueprint $table) {
            $table->foreign(['ID_REF_PEMBELIAN'], 'tr_pembelian_inventaris_ibfk_1')->references(['ID_REF_PEMBELIAN'])->on('ref_pembelian')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_pembelian_inventaris', function (Blueprint $table) {
            $table->dropForeign('tr_pembelian_inventaris_ibfk_1');
        });
    }
};
