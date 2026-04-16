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
        Schema::table('tr_stock_opname', function (Blueprint $table) {
            $table->foreign(['ID_INVENTARIS'], 'tr_stock_opname_ibfk_1')->references(['ID_INVENTARIS'])->on('mst_inventaris')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tr_stock_opname', function (Blueprint $table) {
            $table->dropForeign('tr_stock_opname_ibfk_1');
        });
    }
};
